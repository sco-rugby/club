<?php

namespace App\Service\Datawarehouse;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Geomap;
use App\Entity\Club\Section;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Adresse\Commune;

final class BuildGeomap extends AbstractBuildService {

    private $communes = [],
            $stats = [];

    public function init(Saison $saison): void {
        parent::init($saison);
        try {
            if ($this->hasLogger()) {
                $this->logger->info(sprintf('supprimer les "geomap" de la saison %s', $this->saison));
            }
            $status = $this->em->getRepository(Geomap::class)->deleteSaison($saison, false);
            if ($this->hasLogger()) {
                $this->logger->debug($status . ' données supprimées');
            }
        } catch (Exception $e) {
            $this->rollback = true;
            throw $e;
        }
    }

    public function build(): bool {
        try {
            //
            $status = $this->buildStats();
            if (false === $status) {
                throw new \Exception();
            }
            //
            $status = $this->buildData();
            if (false === $status) {
                throw new \Exception();
            }
        } catch (\Exception $ex) {
            $status = false;
        } finally {
            $this->endProcess();
            return $status;
        }
    }

    public function buildStats(): bool {
        try {
            // Déterminer champs du SQL
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('licence_id', 'licence_id');
            $rsm->addScalarResult('debut', 'debut', 'boolean');
            $rsm->addScalarResult('arret', 'arret', 'boolean');
            $rsm->addScalarResult('joueur', 'joueur', 'boolean');
            $rsm->addScalarResult('educateur', 'educateur', 'boolean');
            $rsm->addScalarResult('arbitre', 'arbitre', 'boolean');
            $rsm->addScalarResult('dirigeant', 'dirigeant', 'boolean');
            $rsm->addScalarResult('section_id', 'section_id');
            $rsm->addScalarResult('parent_id', 'parent_id');
            $rsm->addScalarResult('commune_id', 'commune_id');
            $rsm->addScalarResult('nom', 'nom');
            $rsm->addScalarResult('latitude', 'latitude');
            $rsm->addScalarResult('longitude', 'longitude');
            // Requête SQL pour optimiser temps de réponse (toutes les entités n'ont pas de relations et ne doivent pas en avoir)
            $query = $this->em->createNativeQuery('SELECT ls.licence_id, ls.debut, ls.arret, ls.joueur, ls.educateur, ls.arbitre, ls.dirigeant, afs.section_id, s.parent_id, v.id commune_id, v.nom, v.latitude, v.longitude FROM datawarehouse.licence_saison ls JOIN club.affilie_section afs ON afs.affilie_id=ls.licence_id AND afs.saison_id=ls.saison_id JOIN club.section s ON s.id=afs.section_id JOIN club.affilie a ON a.id=ls.licence_id JOIN club.contact c ON c.id=a.contact_id JOIN club.commune v ON v.id=c.commune_id WHERE ls.saison_id = :saison', $rsm);
            $query->setParameter('saison', $this->saison->getId());
            $licences = $query->getResult();
            //
            if (empty($licences)) {
                if ($this->hasLogger()) {
                    $this->logger->info(sprintf('Aucune licence pour la saison %s', $this->saison));
                }
                return false;
            }
            //
            if ($this->hasLogger()) {
                $this->logger->debug('Construire les stats');
                $this->logger->debug(sprintf('    %s licences à traiter', count($licences)));
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->setMaxSteps(count($licences));
            }
            $this->startProcess();
            //
            $affilies = [];
            foreach ($licences as $licence) {
                if (!key_exists($licence['commune_id'], $this->communes)) {
                    $this->communes[$licence['commune_id']]['nom'] = $licence['nom'];
                    $this->communes[$licence['commune_id']]['latitude'] = $licence['latitude'];
                    $this->communes[$licence['commune_id']]['longitude'] = $licence['longitude'];
                }
                // Aggréger au niveau club, si licence pas déja recencée
                if (!in_array($licence['licence_id'], $affilies)) {
                    $affilies[] = $licence['licence_id'];
                    $this->calculerStats($licence, 'club');
                }
                // Aggréger au niveau regroupement section, si regroupement section
                if (!is_null($licence['parent_id'])) {
                    $this->calculerStats($licence, $licence['parent_id']);
                }
                // Aggréger au niveau section
                $this->calculerStats($licence, $licence['section_id']);
                //
                $this->nextStep();
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            return true;
        } catch (Exception $e) {
            $this->rollback = true;
            throw $e;
        }
    }

    public function buildData(): bool {
        try {
            if ($this->hasLogger()) {
                $this->logger->debug('Construire les données');
                $this->logger->debug(sprintf('   %s communes à créer', count($this->communes)));
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->setMaxSteps(count($this->communes));
            }
            ksort($this->communes);
            $this->startProcess();
            foreach ($this->communes as $id => $commune) {
                forEach ($this->stats[$id] as $section => $data) {
                    $geomap = new Geomap();
                    $geomap->setSaison($this->saison);
                    $geomap->setCommune($id);
                    $geomap->setNom($commune['nom']);
                    $geomap->setLatitude($commune['latitude']);
                    $geomap->setLongitude($commune['longitude']);
                    if ('club' != $section) {
                        $geomap->setSection($section);
                    }
                    $this->setData($geomap, $data);
                    $this->em->persist($geomap);
                    if ($this->hasLogger()) {
                        $this->logger->debug('    Enregistrement');
                    }
                }
                $this->nextStep();
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            return true;
        } catch (Exception $e) {
            $this->rollback = true;
            throw $e;
        }
    }

    private function calculerStats($licence, $section) {
        if (!key_exists($licence['commune_id'], $this->stats)) {
            $this->stats[$licence['commune_id']] = [];
        }
        if (!key_exists($section, $this->stats[$licence['commune_id']])) {
            $this->stats[$licence['commune_id']][$section]['nb'] = 0;
            $this->stats[$licence['commune_id']][$section]['joueurs'] = 0;
            $this->stats[$licence['commune_id']][$section]['joueuses'] = 0;
            $this->stats[$licence['commune_id']][$section]['educateurs'] = 0;
            $this->stats[$licence['commune_id']][$section]['debutants'] = 0;
            $this->stats[$licence['commune_id']][$section]['encours'] = 0;
            $this->stats[$licence['commune_id']][$section]['arrets'] = 0;
        }
        $agg = $this->stats[$licence['commune_id']][$section];
        $agg['nb']++;
        if (true === $licence['joueur'] && 1 == substr($licence['licence_id'], 6, 1)) {
            $agg['joueurs']++;
        } elseif (true === $licence['joueur'] && 2 == substr($licence['licence_id'], 6, 1)) {
            $agg['joueuses']++;
        }
        if (true === $licence['educateur']) {
            $agg['educateurs']++;
        }
        if (true === $licence['debut']) {
            $agg['debutants']++;
        }
        If ( true == $licence['arret']) {
            $agg['arrets']++;
        }
        if (false === $licence['debut'] && false == $licence['arret']) {
            $agg['encours']++;
        } 
        $this->stats[$licence['commune_id']][$section] = $agg;
    }

    private function setData(Geomap &$geomap, array $data) {
        $geomap->setNb($data['nb']);
        $geomap->setPourcentage('0.00');
        $geomap->setJoueurs($data['joueurs']);
        $geomap->setPourcentageJoueurs('0.00');
        $geomap->setJoueuses($data['joueuses']);
        $geomap->setPourcentageJoueuses('0.00');
        $geomap->setEducateurs($data['educateurs']);
        $geomap->setPourcentageEducateurs('0.00');
        $geomap->setDebutants($data['debutants']);
        $geomap->setPourcentageDebutants('0.00');
        $geomap->setEncours($data['encours']);
        $geomap->setPourcentageEncours('0.00');
        $geomap->setArrets($data['arrets']);
        $geomap->setPourcentageArrets('0.00');
    }

}
