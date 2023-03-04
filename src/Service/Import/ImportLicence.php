<?php

namespace App\Service\Import;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Club\Club;
use App\Entity\Contact\Contact;
use App\Entity\Contact\ContactEmail;
use App\Entity\Contact\ContactTelephone;
use App\Entity\Affilie\Affilie;
use App\Manager\Affilie\AffilieManager;
use App\Entity\Affilie\Licence;
use App\Entity\Affilie\Qualite;
use App\Entity\Affilie\AffilieSection;
use App\Entity\Club\Section;
use App\Model\RapportOvale;
use App\Event\ImportFichierEvent;
use App\Entity\Adresse\Adresse;
use Symfony\Component\String\UnicodeString;

final class ImportLicence extends AbstractImportService {

    CONST NOM_FICHIER = 'import_licence';

    private ArrayCollection $affilies;
    private ArrayCollection $licences;
    private ArrayCollection $affilieSections;
    private ArrayCollection $sections;
    private ArrayCollection $qualites;
    private Club $club;

    public function init(): void {
        parent::init();
        //
        $this->creations['affilie'] = 0;
        $this->creations['licence'] = 0;
        $this->creations['section'] = 0;
        $this->affilies = new ArrayCollection();
        $this->licences = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->qualites = new ArrayCollection();
        $this->affilieSections = new ArrayCollection();
        $this->traitement->setDescription((RapportOvale::OVALE2001)->value);
        //
        try {
            $club = $this->params->get('club');
            $this->club = $this->em->getRepository(Club::class)->findOneBy(['club_id' => $club['code']]);
            foreach ($this->em->getRepository(Qualite::class)->findAll() as $qualite) {
                $this->qualites->set($qualite->getId(), $qualite);
            }
            $this->managers['affilie'] = new AffilieManager($this->em->getRepository(Affilie::class), $this->dispatcher, $this->validator);
            foreach ($this->managers['affilie']->findAll() as $affilie) {
                $this->affilies->set($affilie->getId(), $affilie);
            }
            //
            foreach ($this->em->getRepository(Licence::class)->findAll() as $licence) {
                $this->setLicenceList($licence);
            }
            foreach ($this->em->getRepository(AffilieSection::class)->findAll() as $section) {
                $this->sections->set($section->getSection()->getId(), $section->getSection());
                $this->setSectionAffilieList($section);
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
        $rows = $this->readSpreadsheet(RapportOvale::OVALE2001, $worksheet);
        //
        $result = true;
        try {
            $this->startProcess(count($rows));
            foreach ($rows as $no => $data) {
                $saison = $this->managers['saison']->convertirSaison($data['saison']);
                $qualite = $this->convertirQualite($data['qualite']);
                $section = $this->convertirSection($data['qualite'], $data['classe_age']);
                $trmnt = sprintf('ligne %s - %s/%s/%s %s %s => %s-%s', $no, $saison, $data['licence'], $data['nom'], $data['prenom'], $data['qualite'], $qualite, $section);
                try {
                    if ($this->hasLogger()) {
                        $this->logger->info('traitement ' . $trmnt);
                    }
                    if (!$this->isLicenceCreated($data['licence'], $saison, $qualite)) {
                        $licence = $this->createLicence($data);
                    }
                    if (null !== $section && !$this->isSectionAffilieCreated($data['licence'], $saison, $section)) {
                        $section = $this->createSectionAffilie($data);
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
            if ($this->creations['affilie'] > 0) {
                $this->addCompteRendu($this->creations['affilie'] . ' affiliés créés');
            }
            if ($this->creations['licence'] > 0) {
                $this->addCompteRendu($this->creations['licence'] . ' licences créées');
            }
            if ($this->creations['section'] > 0) {
                $this->addCompteRendu($this->creations['section'] . ' affiliés ajoutés aux sections');
            }
            if (true === $result) {
                if (null === $this->cr) {
                    $msg = sprintf('%s lignes lues : Aucune modification - Fichier %s ignoré', $this->lecture, $this->fichier->getFilename());
                } else {
                    $msg = sprintf('%s lignes lues : %s - Fichier %s importé', $this->lecture, $this->cr, $this->fichier->getFilename());
                }
                $this->setSuccess($msg);
            }
            $event = new ImportFichierEvent(RapportOvale::OVALE2001, $this->fichier);
            $this->dispatcher->dispatch($event, ImportFichierEvent::LICENCE);
            $this->endProcess();
            return $result;
        } catch (\Exception $ex) {
            $this->setError($ex->getMessage());
            throw $ex;
        }
    }

    protected function endProcess(): void {
        unset($this->creations);
        unset($this->affilies);
        unset($this->licences);
        unset($this->sections);
        unset($this->qualites);
        unset($this->affilieSections);
        unset($this->contacts);
        unset($this->saisons);
        $this->em->flush();
        parent::endProcess();
    }

    private function convertirQualite($qualite): string {
        $qualiteId = $qualite;
        if ('VET' == $qualite) {
            $qualiteId = 'RLO';
        } elseIf (in_array($qualite, ['EDE', 'EBF'])) {
            $qualiteId = 'EDU';
        } elseIf (true === $this->isMutation($qualite)) {
            $qualiteId = substr($qualite, 0, 1);
        }
        return $qualiteId;
    }

    private function convertirSection(string $qualite, ?string $classeAge): ?string {
        if (null === $classeAge) {
            return null;
        }
        switch (strtoupper($qualite)) {
            case 'VET':
            case 'RLO':
                return 'RLO';
            case 'RLSP':
                return 'RLSP';
        };
        return match ($classeAge) {
            'M+18', 'M+19' => 'M+18',
            'F+18' => 'F+18',
            'F-18' => 'F-18',
            'M-17', 'M-18', 'M-19' => 'U19',
            'M-16', 'M-15' => 'U16',
            'F-15' => 'F-15',
            'F-13', 'M-13', 'M-14' => 'U14',
            'F-12', 'M-12', 'F-11', 'M-11' => 'U12',
            'F-10', 'M-10', 'M-9', 'F-9' => 'U10',
            'F-8', 'M-8', 'M-7' => 'U8',
            'F-6', 'M-6' => 'U6'
        };
    }

    /**
     * Define abstract method, used in AbstractImportService::createContact()
     * 
     * @param array $data
     * @return Adresse
     */
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

    private function isMutation($qualite): bool {
        return match (substr($qualite, 1)) {
            'M', 'MC' => true,
            default => false
        };
    }

    private function isEnFormation($qualite): bool {
        return match (substr($qualite, 2)) {
            'CF' => true,
            default => false
        };
    }

    private function findQualite(string $qualite): Qualite {
        return $this->qualites->get($qualite);
    }

    /**
     * AFFILIE
     */
    private function isAffilieCreated(int $licenceId) {
        return null !== $this->findAffilie($licenceId);
    }

    private function findAffilie(int $licenceId): ?Affilie {
        return $this->affilies->get($licenceId);
    }

    private function createAffilie(array $data): Affilie {
        if ($this->hasLogger()) {
            $this->logger->debug("Création affilie " . $data['licence']);
        }
        if (false !== $this->isContactCreated($data['nom'], $data['prenom'], $data['code_postal'])) {
            $found = $this->findContact($data['nom'], $data['prenom'], $data['code_postal']);
            $contact = $this->managers['contact']->get($found->getId());
        } else {
            $contact = $this->createContact($data);
        }
        $affilie = $this->managers['affilie']->createObject();
        $affilie->setId($data['licence'])
                ->setContact($contact)
                ->setDateNaissance($this->convertirDate($data['date_naissance']))
                ->setClub($this->club)
                ->setAppliMaitre(self::APP_MAITRE)
                ->setImportedAt($this->traitement->getDebut());
        //
        $saison = null;
        $date = $this->convertirDate($data['date_première_affiliation']);
        if (null !== $date) {
            $saison = $this->managers['saison']->findByDate($date);

            if (null === $saison) {
                $annee = $this->managers['saison']::determinerSaison($date);
                $this->managers['saison']->setAnnee($annee);
                $saison = $this->managers['saison']->create(null);
            }
        }
        $affilie->setPremiereAffiliation($saison);
        //$this->managers['affilie']->create($affilie); // persité par licence et AffilieSection
        $this->creations['affilie']++;
        // Ajout à liste
        $this->affilies->set($affilie->getId(), $affilie);
        //
        return $affilie;
    }

    /**
     * LICENCE
     */
    private function isLicenceCreated(int $licenceId, int $saison, string $qualite) {
        $key = sprintf("%s/%s/%s", $licenceId, $saison, $qualite);
        return null !== $this->licences->get($key);
    }

    private function createLicence($data): Licence {
        if ($this->hasLogger()) {
            $this->logger->debug("Création licence " . $data['licence'] . " " . $data['qualite']);
        }
        // create affilie
        if (!$this->isAffilieCreated($data['licence'])) {
            $affilie = $this->createAffilie($data);
        } else {
            $affilie = $this->findAffilie($data['licence']);
        }
        //
        $qualite = $this->convertirQualite($data['qualite']);
        $licence = (new Licence())
                ->setAffilie($affilie)
                ->setSaison($this->findSaison($data['saison']))
                ->setQualite($this->findQualite($qualite))
                ->setMutation($this->isMutation($data['qualite']))
                ->setPremiereLigne($this->toBoolean($data['1ere_ligne']))
                ->setEnFormation($this->isEnFormation($data['qualite']))
                ->setAppliMaitre(self::APP_MAITRE)
                ->setImportedAt($this->traitement->getDebut());
        $this->em->persist($licence);
        $this->creations['licence']++;
        // Ajout à liste
        $this->setLicenceList($licence);
        //
        return $licence;
    }

    private function setLicenceList(Licence $licence) {
        $key = sprintf("%s/%s/%s", $licence->getAffilie()->getId(), $licence->getSaison()->getId(), $licence->getQualite()->getId());
        $this->licences->set($key, $licence);
    }

    /**
     * SECTION
     */
    private function createSectionAffilie($data): AffilieSection {
        if ($this->hasLogger()) {
            $this->logger->debug("Création section " . $data['licence'] . " " . $data['classe_age']);
        }
        // create affilie
        if (!$this->isAffilieCreated($data['licence'])) {
            $affilie = $this->createAffilie($data);
        } else {
            $affilie = $this->findAffilie($data['licence']);
        }
        $section = (new AffilieSection())
                ->setAffilie($affilie)
                ->setSaison($this->findSaison($data['saison']))
                ->setSection($this->findSection($data['qualite'], $data['classe_age']));
        $this->em->persist($section);
        $this->creations['section']++;
        // Ajout à liste
        $this->setSectionAffilieList($section);
        //
        return $section;
    }

    private function findSection(string $qualite, string $classeAge): Section {
        $id = $this->convertirSection($qualite, $classeAge);
        if (!$this->sections->containsKey($id)) {
            $this->sections->set($id, $this->em->getRepository(Section::class)->find($id));
        }
        return $this->sections->get($id);
    }

    private function isSectionAffilieCreated(int $licenceId, int $saison, string $section): bool {
        $key = sprintf("%s/%s/%s", $licenceId, $saison, $section);
        return null !== $this->affilieSections->get($key);
    }

    private function setSectionAffilieList(AffilieSection $section) {
        $key = sprintf("%s/%s/%s", $section->getAffilie()->getId(), $section->getSaison()->getId(), $section->getSection()->getId());
        $this->affilieSections->set($key, $section);
    }

}
