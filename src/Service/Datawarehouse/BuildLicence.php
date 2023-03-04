<?php

namespace App\Service\Datawarehouse;

use App\Entity\Jeu\Saison;
use App\Entity\Affilie\Affilie;
use App\Entity\Affilie\Licence as LicenceAffilie;
use App\Entity\Datawarehouse\Licence\Licence as LicenceAggregee;
use App\Entity\Datawarehouse\Licence\LicenceSaison;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

final class BuildLicence extends AbstractBuildService {

    private $saisonEnCours,
            $aggregations,
            $affilies = [],
            $licences = [],
            $saisons = [];

    public function init(Saison $saison): void {
        // Saison en cours
        $this->saisonEnCours = $this->saisonManager->findEnCours();
        if (null === $this->saisonEnCours) {
            $msg = 'Pas de saison en cours';
            if ($this->hasLogger()) {
                $this->logger->error($msg);
            }
            throw new EntityNotFoundException($msg);
        }
        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('saison en cours : %s', $this->saisonEnCours));
        }
        $this->saisons[$this->saisonEnCours->getId()] = $this->saisonEnCours;
        //
        parent::init($saison);
        //
        try {
            if ($this->hasLogger()) {
                $this->logger->info(sprintf('supprimer les aggrégations "licence" de la saison %s', $this->saison));
            }
            $status = $this->em->getRepository(LicenceSaison::class)->deleteSaison($saison, false);
            if ($this->hasLogger()) {
                $this->logger->debug($status . ' données supprimées');
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
        //
        if ($this->saison->getId() != $this->saisonEnCours->getId()) {
            $this->saisons[$this->saison->getId()] = $this->saison;
        }
        // Lister les licences déjà aggrégées
        $dw = $this->em->getRepository(LicenceAggregee::class)->findAll();
        if (empty($dw)) {
            $dw = [];
        }
        $this->aggregations = new ArrayCollection($dw);
        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('%s affilies trouvés dans datawarehouse', $this->aggregations->count()));
        }
    }

    public function build(): bool {
        // Lister les licences club pour la saison, si aucune => terminer le processus
        if (empty($this->getLicences())) {
            if ($this->hasLogger()) {
                $this->logger->info(sprintf('Aucune licence club pour la saison %s', $this->saison));
            }
            return false;
        }
        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('%s licences à aggréger', count($this->licences)));
        }
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
            if ($this->hasLogger()) {
                $this->logger->info('Contruction des stats');
            }
            $this->startProcess(count($this->licences));
            foreach ($this->getLicences() as $licence) {
                if ($this->hasLogger()) {
                    $this->logger->info(sprintf('Traitement licence %s pour %s (%s)', $licence->getQualite()->getId(), $licence->getAffilie(), $licence->getAffilie()->getId()));
                }
                $this->calculerStatsAffilie($licence->getAffilie());
                $this->calculerStatsSaison($licence);
                //
                $this->nextStep();
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            //
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    public function buildData(): bool {
        try {
            if ($this->hasLogger()) {
                $this->logger->info('Enregistements des données');
            }
            $this->startProcess(count($this->affilies));
            $criteria = new Criteria();
            forEach ($this->affilies as $id => $data) {
                // Recherche affilié
                $expr = new Comparison('id', '=', $id);
                $criteria->where($expr);
                $coll = $this->aggregations->matching($criteria);
                // Si pas encore aggrégée
                if ($coll->isEmpty()) {
                    $stat = new LicenceAggregee();
                } else {
                    $stat = $coll->first();
                }
                $this->setDataAfffilie($stat, $data);
                $this->em->persist($stat);
                // Licence saison
                $licenceSaison = $this->createSaison($stat);
                $this->setDataSaison($licenceSaison, $data);
                $this->em->persist($licenceSaison);
                //
                $this->nextStep();
            }
            if ($this->hasProgressBar()) {
                $this->progressBar->finish();
            }
            //
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    private function getLicences(): array {
        if (empty($this->licences)) {
            $this->licences = $this->em->getRepository(LicenceAffilie::class)->findBySaison($this->saison);
        }
        return $this->licences;
    }

    ###########
    # AFFILIE
    ###########

    private function calculerStatsAffilie(Affilie $affilie): void {
        if (key_exists($affilie->getId(), $this->affilies)) {
            return;
        }
        if ($this->hasLogger()) {
            $this->logger->info(sprintf('Traitement affilié %s (%s)', $affilie, $affilie->getId()));
        }
        $stats = $this->em->getRepository(LicenceAffilie::class)->getStatsAffilie($affilie);
        // Déterminer stats
        $affilieId = $affilie->getId();
        $this->affilies[$affilieId]['id'] = $affilieId; // utlisé par BuildLicence::setDataAfffilie()
        $this->affilies[$affilieId]['nom'] = $affilie->getContact()->getNom();
        $this->affilies[$affilieId]['prenom'] = $affilie->getContact()->getPrenom();
        $this->affilies[$affilieId]['date_naissance'] = $affilie->getDateNaissance();
        $this->affilies[$affilieId]['1ere_affiliation'] = $affilie->getPremiereAffiliation();
        $this->affilies[$affilieId]['nb_saisons'] = $stats['nb'];
        if (!array_key_exists($stats['debut'], $this->saisons)) {
            $this->saisons[$stats['debut']] = $this->em->getRepository(Saison::class)->find($stats['debut']);
        }
        $this->affilies[$affilieId]['saison_debut'] = $stats['debut'];
        // Fin
        if ($stats['fin'] < $this->saisonEnCours->getId()) {
            if (!array_key_exists($stats['fin'], $this->saisons)) {
                $this->saisons[$stats['fin']] = $this->em->getRepository(Saison::class)->find($stats['fin']);
            }
            $this->affilies[$affilieId]['saison_fin'] = $stats['fin'];
        } else {
            $this->affilies[$affilieId]['saison_fin'] = null;
        }
        $this->affilies[$affilieId]['arbitre'] = false;
        $this->affilies[$affilieId]['educateur'] = false;
        $this->affilies[$affilieId]['dirigeant'] = false;
        $this->affilies[$affilieId]['joueur'] = false;
        $this->affilies[$affilieId]['soigneur'] = false;
        $this->affilies[$affilieId]['debut'] = false;
        $this->affilies[$affilieId]['arret'] = false;
        if ($this->saison->getId() == $stats['debut']) {
            $this->affilies[$affilieId]['debut'] = true;
        }
        if ($this->saison->getId() == $this->affilies[$affilieId]['saison_fin']) {
            $this->affilies[$affilieId]['arret'] = true;
        }
    }

    private function setDataAfffilie(LicenceAggregee &$licence, array $data): void {
        if (null === $licence->getId()) {
            $licence->setId($data['id'])
                    ->setNom($data['nom'])
                    ->setPrenom($data['prenom'])
                    ->setDateNaissance($data['date_naissance'])
                    ->setPremiereAffiliation($data['1ere_affiliation']);
        }
        $licence->setNombreSaisons($data['nb_saisons']);
        // Debut
        if (null === $licence->getSaisonDebut() || $data['saison_debut'] != $licence->getSaisonDebut()->getId()) {
            $saisonDebut = $this->saisons[$data['saison_debut']];
            $this->saisonManager->setResource($saisonDebut);
            $licence->setSaisonDebut($saisonDebut);
            $licence->setAgeDebut($this->saisonManager->calculerAge($licence));
        }
        // Fin
        if (null !== $data['saison_fin']) {
            $saisonFin = $this->saisons[$data['saison_fin']];
            $this->saisonManager->setResource($saisonFin);
            $licence->setSaisonFin($saisonFin);
            $licence->setAgeFin($this->saisonManager->calculerAge($licence));
        } else {
            // âge actuel
            $this->saisonManager->setResource($this->saisonEnCours);
            $licence->setAgeActuel($this->saisonManager->calculerAge($licence));
        }
        // 1ere affiliation
        if (null === $licence->getPremiereAffiliation()) {
            $licence->setPremiereAffiliation($licence->getSaisonDebut());
        } elseif ($licence->getPremiereAffiliation()->getId() > $licence->getSaisonDebut()->getId()) {
            $licence->setPremiereAffiliation($licence->getSaisonDebut());
        }
    }

    ###########
    # SAISON
    ###########

    private function calculerStatsSaison(LicenceAffilie $licence): void {
        $affilieId = $licence->getAffilie()->getId();
        switch ($licence->getQualite()->getGroupe()) {
            case 'A':
                $this->affilies[$affilieId]['arbitre'] = true;
                break;
            case 'E':
                $this->affilies[$affilieId]['educateur'] = true;
                break;
            case 'D':
                $this->affilies[$affilieId]['dirigeant'] = true;
                break;
            case 'J':
                $this->affilies[$affilieId]['joueur'] = true;
                break;
            case 'S':
                $this->affilies[$affilieId]['soigneur'] = true;
                break;
        }
    }

    private function createSaison(LicenceAggregee $licence): LicenceSaison {
        $licenceSaison = new LicenceSaison();
        $licenceSaison->setLicence($licence);
        $licenceSaison->setSaison($this->saison);
        $this->saisonManager->setResource($this->saison);
        $licenceSaison->setAge($this->saisonManager->calculerAge($licence));
        $licenceSaison->setNombreSaisons($this->em->getRepository(LicenceAffilie::class)->getNbSaisons($licence, $this->saison));
        return $licenceSaison;
    }

    private function setDataSaison(LicenceSaison &$licence, array $data): void {
        $licence->setArbitre($data['arbitre']);
        $licence->setEducateur($data['educateur']);
        $licence->setDirigeant($data['dirigeant']);
        $licence->setJoueur($data['joueur']);
        $licence->setSoigneur($data['soigneur']);
        $licence->setDebut($data['debut']);
        $licence->setArret($data['arret']);
    }

}
