<?php

namespace App\Service\Datawarehouse;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Effectif\Club as StatsClub;
use App\Entity\Datawarehouse\Effectif\Section as StatsSection;
use App\Entity\Datawarehouse\Effectif\Duree as StatsDuree;
use App\Entity\Club\Section;
use App\Entity\Club\FonctionSection;
use App\Entity\Club\FonctionClub;
use App\Entity\Datawarehouse\Licence\LicenceSaison;
use Doctrine\ORM\Query\ResultSetMapping;

class BuildEffectif extends AbstractBuildService {

    private $statsClub = [],
            $stats = [],
            $ages = [],
            $durees = [];

    public function init(Saison $saison): void {
        parent::init($saison);
        try {
            $status = $this->em->getRepository(StatsClub::class)->deleteSaison($saison, false);
            if ($this->hasLogger()) {
                $this->logger->debug($status . ' stats club supprimées');
            }
            $status = $this->em->getRepository(StatsSection::class)->deleteSaison($saison, false);
            if ($this->hasLogger()) {
                $this->logger->debug($status . ' stats section supprimées');
            }

            $status = $this->em->getRepository(StatsDuree::class)->deleteSaison($saison, false);
            if ($this->hasLogger()) {
                $this->logger->debug($status . ' stats durée supprimées');
            }
            //
            $this->statsClub['licencies'] = 0;
            $this->statsClub['joueurs'] = 0;
            $this->statsClub['joueuses'] = 0;
            $this->statsClub['educateurs'] = 0;
            $this->statsClub['dirigeants'] = 0;
            $this->statsClub['arbitres'] = 0;
            $this->statsClub['soigneurs'] = 0;
            $this->statsClub['benevoles'] = 0;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    public function build(): bool {
        try {
            $status = $this->buildStatsClub();
            if (false === $status) {
                throw new \Exception();
            }
            $status = $this->buildStatsSection();
            if (false === $status) {
                throw new \Exception();
            }
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

    public function buildData(): bool {
        try {
            // Stats club
            $statsClub = new StatsClub();
            $statsClub->setSaison($this->saison);
            $this->setDataClub($statsClub);
            $this->em->persist($statsClub);
            // Stats par section
            forEach ($this->stats as $sectionId => $data) {
                $sectionObj = null;
                $statsSection = new StatsSection();
                $statsSection->setSaison($this->saison);
                if ('club' != $sectionId) {
                    $sectionObj = $this->em->getRepository(Section::class)->find($sectionId);
                    $statsSection->setSection($sectionObj);
                    $statsSection->setLibelle($statsSection->getSection()->getLibelle());
                    $statsSection->setCouleur($statsSection->getSection()->getCouleur());
                    $statsSection->setRegroupement((null === $statsSection->getSection()->getParent()));
                }
                $this->setDataSection($statsSection, $data);
                $this->setDataDuree($sectionObj, $data);
                $this->em->persist($statsSection);
                unset($statsSection);
            }
            //
            return true;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    private function determinerGenre(LicenceSaison|array $licence): int {
        if ($licence instanceof LicenceSaison) {
            $licenceId = $licence->getLicence()->getId();
        } else {
            $licenceId = $licence['licence_id'];
        }
        return intval(substr($licenceId, 6, 1));
    }

    ###########
    # CLUB
    ###########

    public function buildStatsClub(): bool {
        try {
            $licences = $this->getLicencesClub();
            if (empty($licences)) {
                if ($this->hasLogger()) {
                    $this->logger->info(sprintf('Aucune licence club pour la saison %s', $this->saison));
                }
                return false;
            }
            //
            if ($this->hasLogger()) {
                $this->logger->debug('Construire les stats club');
                $this->logger->debug(sprintf('    %s licences à traiter', count($licences)));
            }
            $this->startProcess(count($licences));
            foreach ($licences as $licence) {
                $this->calculerStatsClub($licence);
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            return true;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    private function calculerStatsClub(LicenceSaison $licence): void {
        $this->statsClub['licencies']++;
        $genre = $this->determinerGenre($licence);
        if ($licence->isJoueur() && 1 == $genre) {
            $this->statsClub['joueurs']++;
        } elseif ($licence->isJoueur() && 2 == $genre) {
            $this->statsClub['joueuses']++;
        }
        if ($licence->isEducateur()) {
            $this->statsClub['educateurs']++;
        }
        if ($licence->isDirigeant()) {
            $this->statsClub['dirigeants']++;
        }
        if ($licence->isArbitre()) {
            $this->statsClub['arbitres']++;
        }
        if ($licence->isSoigneur()) {
            $this->statsClub['soigneurs']++;
        }
    }

    private function getLicencesClub(): array {
        return $this->em->getRepository(LicenceSaison::class)->findBySaison($this->saison);
    }

    private function setDataClub(StatsClub &$statsClub): void {
        $statsClub->setLicencies($this->statsClub['licencies']);
        $statsClub->setJoueurs($this->statsClub['joueurs']);
        $statsClub->setJoueuses($this->statsClub['joueuses']);
        $statsClub->setEducateurs($this->statsClub['educateurs']);
        $statsClub->setDirigeants($this->statsClub['dirigeants']);
        $statsClub->setArbitres($this->statsClub['arbitres']);
        $statsClub->setSoigneurs($this->statsClub['soigneurs']);
        $statsClub->setBenevoles($this->em->getRepository(FonctionClub::class)->getNbBenevoles($this->saison));
    }

    ###########
    # SECTION
    ###########

    public function buildStatsSection(): bool {
        try {
            $licences = $this->getLicencesSection();
            if (empty($licences)) {
                if ($this->hasLogger()) {
                    $this->logger->info(sprintf('Aucune licence pour la saison %s', $this->saison));
                }
                return false;
            }
            //
            if ($this->hasLogger()) {
                $this->logger->debug('Construire les stats par section');
                $this->logger->debug(sprintf('    %s licences à traiter', count($licences)));
            }
            $this->startProcess(count($licences));
            //
            $affilies = [];
            foreach ($licences as $licence) {
                // Aggréger au niveau club, si licence pas déja recencée
                if (!in_array($licence['licence_id'], $affilies)) {
                    $affilies[] = $licence['licence_id'];
                    $this->calculerStatsSection($licence, 'club');
                }
                // Aggréger au niveau regroupement section, si regroupement section
                if (!is_null($licence['parent_id'])) {
                    $this->calculerStatsSection($licence, $licence['parent_id']);
                }
                // Aggréger au niveau section
                $this->calculerStatsSection($licence, $licence['section_id']);
                //
                $this->nextStep();
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            return true;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    private function calculerStatsSection(array $licence, string $section): void {
        // Init. stats section, si pas créée
        if (!key_exists($section, $this->stats)) {
            $this->initStatsSection($section);
        }
        //
        if (!key_exists($licence['nombre_saisons'], $this->durees[$section])) {
            $this->durees[$section][$licence['nombre_saisons']]['actuel'] = 0;
            $this->durees[$section][$licence['nombre_saisons']]['debutants'] = 0;
            $this->durees[$section][$licence['nombre_saisons']]['encours'] = 0;
            $this->durees[$section][$licence['nombre_saisons']]['arrets'] = 0;
        }
        //
        $this->stats[$section]['licencies']++;
        $this->setStatsTypeLicence('actuel', $section, $licence);
        if (true === $licence['debut']) {
            $this->setStatsTypeLicence('debutants', $section, $licence);
        }
        if (true === $licence['arret']) {
            $this->setStatsTypeLicence('arrets', $section, $licence);
        }
        if (false === $licence['debut'] && false === $licence['arret']) {
            $this->setStatsTypeLicence('encours', $section, $licence);
        }
    }

    private function setStatsTypeLicence(string $typeLicence, string $section, array $licence): void {
        $genre = $this->determinerGenre($licence);
        if (true === $licence['joueur'] && 1 == $genre) {
            $this->stats[$section][$typeLicence]['joueurs']++;
        } elseif (true === $licence['joueur'] && 2 == $genre) {
            $this->stats[$section][$typeLicence]['joueuses']++;
        }
        //
        $this->ages[$section][$typeLicence][] = $licence['age'];
        ///
        $this->durees[$section][$typeLicence][] = $licence['nombre_saisons'];
        $this->durees[$section][$licence['nombre_saisons']][$typeLicence]++;
    }

    private function getLicencesSection(): array {
        // Déterminer champs du SQL
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('licence_id', 'licence_id');
        $rsm->addScalarResult('age', 'age', 'integer');
        $rsm->addScalarResult('nombre_saisons', 'nombre_saisons', 'integer');
        $rsm->addScalarResult('debut', 'debut', 'boolean');
        $rsm->addScalarResult('arret', 'arret', 'boolean');
        $rsm->addScalarResult('joueur', 'joueur', 'boolean');
        $rsm->addScalarResult('educateur', 'educateur', 'boolean');
        $rsm->addScalarResult('arbitre', 'arbitre', 'boolean');
        $rsm->addScalarResult('dirigeant', 'dirigeant', 'boolean');
        $rsm->addScalarResult('soigneur', 'soigneur', 'boolean');
        $rsm->addScalarResult('section_id', 'section_id');
        $rsm->addScalarResult('parent_id', 'parent_id');
        // Requête SQL pour optimiser temps de réponse (toutes les entités n'ont pas de relations et ne doivent pas en avoir)
        $query = $this->em->createNativeQuery('SELECT ls.*, afs.section_id, s.parent_id FROM datawarehouse.licence_saison ls JOIN club.affilie_section afs ON afs.affilie_id=ls.licence_id AND afs.saison_id=ls.saison_id JOIN club.section s ON s.id=afs.section_id JOIN club.affilie a ON a.id=ls.licence_id WHERE ls.saison_id = :saison', $rsm);
        $query->setParameter('saison', $this->saison->getId());
        return $query->getResult();
    }

    private function initStatsSection(string $section): void {
        if (key_exists($section, $this->stats)) {
            return;
        }
        $this->stats[$section]['licencies'] = 0;
        $this->stats[$section]['educateurs'] = 0;
        foreach (['actuel', 'debutants', 'encours', 'arrets'] as $typeStat) {
            $this->stats[$section][$typeStat]['joueurs'] = 0;
            $this->stats[$section][$typeStat]['joueuses'] = 0;
            //
            $this->ages[$section][$typeStat] = [];
            $this->durees[$section][$typeStat] = [];
        }
    }

    private function setDataSection(StatsSection &$statsSection, array $data): void {
        if (null === $statsSection->getSection()) {
            $sectionId = 'club';
        } else {
            $sectionId = $statsSection->getSection()->getId();
        }
        $effectifClub = $this->statsClub['joueurs'] + $this->statsClub['joueuses'];
        // Educateurs
        if ('club' == $sectionId) {
            $statsSection->setEducateurs($this->statsClub['educateurs']);
        } elseIf ($statsSection->isRegroupement() && 'M+18' != $statsSection->getSection()->getId()) {
            // Educateurs pour regroupement
            $statsSection->setEducateurs($this->em->getRepository(FonctionSection::class)->getNbEducateurs($statsSection->getSection(), $this->saison, true));
        } else {
            // Educateurs / section
            $statsSection->setEducateurs($this->em->getRepository(FonctionSection::class)->getNbEducateurs($statsSection->getSection(), $this->saison));
        }
        // actuel
        $statsSection->getActuel()->setJoueurs($data['actuel']['joueurs']);
        $statsSection->getActuel()->setJoueuses($data['actuel']['joueuses']);
        $statsSection->getActuel()->setAges($this->ages[$sectionId]['actuel']);
        $statsSection->getActuel()->setDurees($this->durees[$sectionId]['actuel']);
        $statsSection->getActuel()->setEffectif($data['licencies']);
        $statsSection->getActuel()->setEffectifClub($effectifClub);
        // début
        $statsSection->getDebut()->setJoueurs($data['debutants']['joueurs']);
        $statsSection->getDebut()->setJoueuses($data['debutants']['joueuses']);
        $statsSection->getDebut()->setAges($this->ages[$sectionId]['debutants']);
        $statsSection->getDebut()->setEffectif($data['licencies']);
        $statsSection->getDebut()->setEffectifClub($effectifClub);
        // encours
        $statsSection->getEnCours()->setJoueurs($data['encours']['joueurs']);
        $statsSection->getEnCours()->setJoueuses($data['encours']['joueuses']);
        $statsSection->getEnCours()->setAges($this->ages[$sectionId]['encours']);
        $statsSection->getEnCours()->setDurees($this->durees[$sectionId]['encours']);
        $statsSection->getEnCours()->setEffectif($data['licencies']);
        $statsSection->getEnCours()->setEffectifClub($effectifClub);
        // arret
        $statsSection->getArret()->setJoueurs($data['arrets']['joueurs']);
        $statsSection->getArret()->setJoueuses($data['arrets']['joueuses']);
        $statsSection->getArret()->setAges($this->ages[$sectionId]['arrets']);
        $statsSection->getArret()->setDurees($this->durees[$sectionId]['arrets']);
        $statsSection->getArret()->setEffectif($data['licencies']);
        $statsSection->getArret()->setEffectifClub($effectifClub);
    }

    ###########
    # DUREE
    ###########    

    private function setDataDuree($sectionObj, array $data): void {
        if (null === $sectionObj) {
            $sectionId = 'club';
        } else {
            $sectionId = $sectionObj->getId();
        }
        $nbSaison = array_keys($this->durees[$sectionId]);
        sort($nbSaison);
        $effectifClub = $this->statsClub['joueurs'] + $this->statsClub['joueuses'];
        forEach ($nbSaison as $duree) {
            if (!in_array($duree, ['actuel', 'debutants', 'encours', 'arrets'])) {
                if (0 == $this->durees[$sectionId][$duree]['encours'] && 0 == $this->durees[$sectionId][$duree]['arrets']) {
                    continue;
                }
                $statDuree = new StatsDuree();
                $statDuree->setSaison($this->saison);
                $statDuree->setSection($sectionObj);
                $statDuree->setDuree(intval($duree));
                // En cours
                $statDuree->getEnCours()->setNombre($this->durees[$sectionId][$duree]['encours']);
                $statDuree->getEnCours()->setEffectif($data['licencies']);
                $statDuree->getEnCours()->setEffectifClub($effectifClub);
                // Arrêt
                $statDuree->getArret()->setNombre($this->durees[$sectionId][$duree]['arrets']);
                $statDuree->getArret()->setEffectif($data['licencies']);
                $statDuree->getArret()->setEffectifClub($effectifClub);
                //
                $this->em->persist($statDuree);
            }
        }
    }

}
