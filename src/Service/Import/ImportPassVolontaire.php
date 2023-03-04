<?php

namespace App\Service\Import;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Adresse\Adresse;
use App\Entity\Club\Fonction;
use App\Entity\Club\FonctionClub;
use App\Model\RapportOvale;
use App\Event\ImportFichierEvent;

final class ImportPassVolontaire extends AbstractImportService {

    private Fonction $fonction;

    CONST NOM_FICHIER = 'import_benevole';
    const BENEVOLE = 'BENE';

    public function init(): void {
        parent::init();
        //
        $this->creations['benevole'] = 0;
        $this->fonction = $this->em->getRepository(Fonction::class)->find(self::BENEVOLE);
        $this->benevoles = new ArrayCollection();
        $this->traitement->setDescription((RapportOvale::OVALE2050)->value);
        //
        try {
            //
            foreach ($this->em->getRepository(FonctionClub::class)->findBy(['fonction' => self::BENEVOLE]) as $benevole) {
                $this->setBenevoleList($benevole);
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    public function import(): bool {
        $worksheet = $this->spreadsheet->getActiveSheet();
        if ($this->hasLogger()) {
            $this->logger->debug('Ouvrir ' . $worksheet->getTitle());
        }
        $rows = $this->readSpreadsheet(RapportOvale::OVALE2050, $worksheet);
        //
        $result = true;
        try {
            $this->startProcess(count($rows));
            foreach ($rows as $no => $data) {
                $saison = $this->managers['saison']->convertirSaison($data['saison']);
                try {
                    if ($this->hasLogger()) {
                        $this->logger->info(sprintf('traitement %s %s pour %s', $data['nom'], $data['prenom'], $saison));
                    }
                    if (!$this->isBenevoleCreated($data['nom'], $data['prenom'], $saison)) {
                        $benevole = $this->createBenevole($data);
                    }
                } catch (\Exception $ex) {
                    $this->setFailure($ex->getMessage() . ';' . implode(';', $data));
                    $result = false;
                }
                //
                $this->lecture++;
                $this->nextStep();
            }
            if ($this->creations['contact'] > 0) {
                $this->addCompteRendu($this->creations['contact'] . ' contacts créés');
            }
            if ($this->creations['benevole'] > 0) {
                $this->addCompteRendu($this->creations['benevole'] . ' bénévoles créés');
            }
            if (true === $result) {
                if (null === $this->cr) {
                    $msg = sprintf('%s lignes lues : Aucune modification - Fichier %s ignoré', $this->lecture, $this->fichier->getFilename());
                } else {
                    $msg = sprintf('%s lignes lues : %s - Fichier %s importé', $this->lecture, $this->cr, $this->fichier->getFilename());
                }
                $this->setSuccess($msg);
            }
            $event = new ImportFichierEvent(RapportOvale::OVALE2050, $this->fichier);
            $this->dispatcher->dispatch($event, ImportFichierEvent::BENEVOLE);
            $this->endProcess();
            return $result;
        } catch (\Exception $ex) {
            $this->setError($ex->getMessage());
            throw $ex;
        }
    }

    private function isBenevoleCreated(string $nom, string $prenom, int $saison): bool {
        if (!$this->isContactCreated($nom, $prenom, null)) {
            return false;
        }
        $contactId = $this->findContact($nom, $prenom, null)->getId();
        return null !== $this->findBenevole($contactId, $saison);
    }

    private function findBenevole(int $contactId, int $saison): ?FonctionClub {
        return $this->benevoles->get($this->convertirBenevoleKey($contactId, $saison));
    }

    private function createBenevole(array $data): FonctionClub {
        if ($this->hasLogger()) {
            $this->logger->debug('Création bénévole');
        }
        if (false !== $this->isContactCreated($data['nom'], $data['prenom'], $data['code_postal'])) {
            $found = $this->findContact($data['nom'], $data['prenom'], $data['code_postal']);
            $contact = $this->managers['contact']->get($found->getId());
        } else {
            $contact = $this->createContact($data);
        }
        $benevole = (new FonctionClub())
                ->setSaison($this->findSaison($data['saison']))
                ->setContact($contact)
                ->setFonction($this->fonction)
                ->setNote($data['fonctions']);
        //
        $this->em->persist($benevole);
        $this->creations['benevole']++;
        // Ajout à liste
        $this->setBenevoleList($benevole);
        //
        return $benevole;
    }

    private function setBenevoleList(FonctionClub $benevole) {
        $this->benevoles->set($this->convertirBenevoleKey($benevole->getContact()->getId(), $benevole->getSaison()->getId()), $benevole);
    }

    private function convertirBenevoleKey(int $contactId, int $saison) {
        return sprintf("%s/%s", $contactId, $saison);
    }

    protected function convertirAdresse(array $data): Adresse {
        $complement = null;
        forEach (['lieu-dit', 'immeuble', 'complement'] as $elmt) {
            if (!empty($complement) && !empty($elmt)) {
                $complement .= ' - ';
            }
            if (!empty($data[$elmt])) {
                $complement .= $data[$elmt];
            }
        }
        //
        $adresse = new Adresse();
        $adresse->setAdresse($data['voie']);
        $adresse->setComplement($complement);
        $adresse->setCodePostal($data['code_postal']);
        $adresse->setVille($data['localite']);
        $adresse->setPays('FR');
        //
        return $adresse;
    }

}
