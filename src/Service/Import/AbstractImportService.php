<?php

namespace App\Service\Import;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Psr\Log\LoggerInterface;
use \App\Service\ServiceTrait;
use \App\Service\TransactionTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Entity\Contact\Contact;
use App\Entity\Contact\ContactEmail;
use App\Entity\Contact\ContactTelephone;
use App\Manager\Jeu\SaisonManager;
use App\Manager\Contact\ContactManager;
use App\Entity\Jeu\Saison;
use Doctrine\Common\Collections\ArrayCollection;
use App\Exception\InvalidParameterException;
use Doctrine\Common\Collections\Criteria;
use App\Exception\ContactException;
use App\Model\Import\ImportInterface;
use App\Entity\Adresse\Adresse;
use Symfony\Component\String\UnicodeString;

//TODO : Suppression donnée saison KO
abstract class AbstractImportService implements ImportServiceInterface {

    use ServiceTrait,
        TransactionTrait;

    const APP_MAITRE = 'oval-e';

    protected int $lecture = 0;
    protected $managers = [];
    protected $creations = [];
    protected ?string $cr = null;
    protected ArrayCollection $contacts;
    protected ArrayCollection $saisons;
    protected \SplFileInfo $fichier;
    protected Spreadsheet $spreadsheet;

    public function __construct(protected EntityManagerInterface $em, protected EventDispatcherInterface $dispatcher, protected ValidatorInterface $validator, protected ContainerBagInterface $params, protected ?ProgressBar $progressBar = null, protected ?LoggerInterface $logger = null) {
        $this->managers['saison'] = new SaisonManager($this->em->getRepository(Saison::class), $this->params, $this->dispatcher, $this->validator);
        $this->managers['contact'] = new ContactManager($this->em->getRepository(Contact::class), $this->em, $this->dispatcher, $this->validator);
        $this->contacts = new ArrayCollection();
        $this->saisons = new ArrayCollection();
        $this->creations['contact'] = 0;
    }

    public function init(): void {
        try {
            $params = $this->params->get('import');
            $this->initProcess(static::class, $params['report_path'] . DIRECTORY_SEPARATOR . static::NOM_FICHIER);
            $contacts = $this->managers['contact']->findAllForSearch();
            foreach ($contacts as $contact) {
                $contact['contact'] = null;
                $this->contacts->set($contact['c_id'], $contact);
            }
            $this->initTransaction();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    public function load(\SplFileInfo $fichier): void {
        try {
            if (!$fichier->isReadable()) {
                throw new InvalidParameterException(sprintf('Le fichier %s ne peut pas être lu', $fichier));
            }
            $reader = IOFactory::createReaderForFile($fichier->getPathname());
            $reader->setReadDataOnly(true);
            $this->fichier = $fichier;
            $this->traitement->setFichier($fichier);
            $this->spreadsheet = $reader->load($this->fichier);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    protected function readSpreadsheet(ImportInterface $rapport, Worksheet $worksheet): array {
        $endCol = Coordinate::stringFromColumnIndex(count($rapport->getMapping()));
        $rows = [];
        foreach ($worksheet->getRowIterator() as $row) {
            if ($row->isEmpty(1) || 1 == $row->getRowIndex()) {
                continue;
            }
            $values = [];
            foreach ($row->getCellIterator('A', $endCol) as $cell) {
                //$cell->setDataType(DataType::TYPE_STRING);
                $values[] = $cell->getValue();
            }
            $rows[] = array_combine($rapport->getMapping(), $values);
        }
        return $rows;
    }

    public function shutdown(): void {
        // Clore le traitement
        $this->shutDownProcess();
        // commit or rollbach transaction
        $this->shutDownTransaction();
        // supprimer le fichier
        /* if (isset($this->fichier)) {
          $this->filesystem->remove($this->fichier); //TODO reactiver
          } */
    }

    protected function isContactCreated(string $nom, string $prenom, ?string $cp): bool {
        return (null !== $this->findContact($nom, $prenom, $cp));
    }

    protected function findContact(string $nom, string $prenom, ?string $cp): ?Contact {
        $exprBuilder = Criteria::expr();
        $expr = $exprBuilder->andX(
                $exprBuilder->eq('canonized_nom', strtoupper($nom)),
                $exprBuilder->eq('canonized_prenom', strtoupper($prenom))
        );
        $result = $this->contacts->matching(new Criteria($expr));
        if ($result->isEmpty()) {
            return null;
        } elseif (count($result) > 1) {
            forEach ($result as $row) {
                if ($cp == $row['c_adresse.codePostal']) {
                    $result = new ArrayCollection();
                    $result->add($row);
                    break;
                }
            }
            if (count($result) > 1) {
                throw new ContactException(sprintf('%s contacts trouvés avec le même nom (%s %s) et code postal (%s)', count($result), $nom, $prenom, $cp));
            }
        }
        $data = $result->first();
        if (null === $data['contact']) {
            $contact = $this->managers['contact']->get(intval($data['c_id']));
            $data['contact'] = $contact;
            $this->contacts->set($data['c_id'], $data);
        } else {
            $contact = $data['contact'];
        }
        return $contact;
    }

    protected function createContact(array $data): Contact {
        if ($this->hasLogger()) {
            $this->logger->debug("Création contact");
        }
        $nom = $this->managers['contact']->normalizeNom($data['nom']);
        $prenom = $this->managers['contact']->normalizePrenom($data['prenom']);
        if (key_exists('autorisation_ffr', $data)) {
            $listeRouge = $this->toBoolean($data['autorisation_ffr']);
        } else {
            $listeRouge = true;
        }
        //
        $contact = $this->managers['contact']->createObject();
        $contact->setPublic(false)
                ->setListeRouge($listeRouge)
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setGenre($this->convertirGenre($data['sexe']))
                ->setAppliMaitre(self::APP_MAITRE)
                ->setImportedAt($this->traitement->getDebut());
        //
        $this->addContactEmail($contact, $data);
        $this->addContactPhone($contact, $data);
        //
        $this->managers['contact']->setResource($contact);
        // Adresse
        $adresse = $this->convertirAdresse($data);
        $this->managers['contact']->setAddress($adresse);
        //
        $this->creations['contact']++;
        //
        return $this->managers['contact']->getResource();
    }

    protected function addContactEmail(Contact &$contact, array $data): void {
        if (empty($data['email'])) {
            return;
        }
        $email = new ContactEmail();
        $email->setEmail($data['email']);
        $email->setPrefere(true);
        $email->setType('D');
        $contact->addEmail($email);
    }

    protected function addContactPhone(Contact &$contact, array $data): void {
        $pref = false;
        foreach (['dom' => 'D', 'pro' => 'P', 'port' => 'M'] as $field => $type) {
            if (!empty($data['telephone_' . $field])) {
                $str = (new UnicodeString($data['telephone_' . $field]))
                        ->replace(' ', '')
                        ->replaceMatches('/^\+[0-9]+/', '')
                        ->ensureStart('0');
                $no = $str->split('-');
                $no = (new UnicodeString($no[0]))->truncate(20);
                $tel = new ContactTelephone();
                $tel->setNumero($no);
                if ('port' == $field) {
                    $tel->setPrefere(true);
                    $pref = true;
                } else {
                    $tel->setPrefere(false);
                }
                $tel->setType($type);
                $tel->setCodePays('FR');
                $contact->addTelephone($tel);
            }
        }
        if (false === $pref && !$contact->getTelephones()->isEmpty()) {
            $contact->getTelephones()->first()->setPrefere(true);
        }
    }

    protected function addCompteRendu(string $msg) {
        if (null !== $this->cr) {
            $this->cr .= ', ';
        }
        $this->cr .= $msg;
    }

    abstract protected function convertirAdresse(array $data): Adresse;

    protected function toBoolean($bool): bool {
        return match (strtolower($bool)) {
            'oui' => true,
            'non' => false,
            default => false
        };
    }

    protected function convertirGenre(string $genre): string {
        return match ($genre) {
            'Masculin' => 'M',
            'Féminin' => 'F'
        };
    }

    protected function convertirDate($date): ?\DateTime {
        if ($date == $ts = intval($date)) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($ts);
        } else {
            $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        }
        if (false === $dateTime) {
            if ($this->hasLogger()) {
                $this->logger->debug(self::class . "::convertirDate() : conversion impossible pour " . $date);
            }
            return null;
        }
        return $dateTime;
    }

    protected function convertirDate2Saison(string $date): Saison {
        return $this->saisonManager->findByDate($this->convertirDate($date));
    }

    protected function findSaison($saison): Saison {
        $annee = $this->managers['saison']->convertirSaison($saison);
        if (!$this->saisons->containsKey($annee)) {
            $this->saisons->set($annee, $this->managers['saison']->get($annee));
        }
        return $this->saisons->get($annee);
    }

    public function __destruct() {
        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('%s::__destruct()', self::class));
        }
        $this->shutdown();
    }

}
