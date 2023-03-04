<?php

namespace App\Manager\Contact;

use App\Manager\AbstractResourceManager;
use App\Manager\Adresse\AdresseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\Contact\ContactRepository;
use App\Entity\Adresse\Adresse;
use App\Entity\Adresse\Commune;
use Symfony\Component\String\UnicodeString;

final class ContactManager extends AbstractResourceManager {

    private $adresseManager;

    public function __construct(ContactRepository $repository, protected EntityManagerInterface $em, ?EventDispatcherInterface $dispatcher = null, ?ValidatorInterface $validator = null, ?FormFactoryInterface $formFactory = null) {
        parent::__construct($repository, $dispatcher, $validator, $formFactory);
        $this->adresseManager = new AdresseManager($this->em->getRepository(Commune::class), $dispatcher, $validator, $formFactory);
    }

    public function setAddress(Adresse $adresse): void {
        $this->adresseManager->normalizeAddress($adresse);
        // Commune (pour geolocalisation)
        if (null === $adresse->getVille()) {
            
        }
        $commune = $this->findByNom($adresse->getVille());
        if (null !== $commune) {
            $adresse->setVille($commune->getNom());
            if ($commune->isRegroupement()) {
                $cmplmt = $this->findByNom($adresse->getComplement());
                if (null !== $cmplmt) {
                    $commune = $cmplmt;
                }
            }
        }
        if (null !== $commune) {
            $this->getResource()->setCommune($commune);
        }
        $this->getResource()->setAdresse($adresse);
    }

    private function findByNom(?string $nom): ?\App\Entity\Adresse\Commune {
        if (null === $nom) {
            return null;
        }
        return $this->adresseManager->findByNom($nom);
    }

    static public function normalizeNom(string $nom): string {
        return (string) (new UnicodeString($nom))
                        ->trim()
                        ->folded()
                        ->title(true)
                        ->replace('De ', 'de ')
                        ->replace("D'", "d'");
    }

    static public function normalizePrenom(string $prenom): string {
        return (string) (new UnicodeString($prenom))
                        ->trim()
                        ->folded()
                        ->title(true)
                        ->replace(' ', '-');
    }

    protected function dispatchException(\Exception $ex) {
        return;
    }

    protected function dispatchEvent(string $name) {
        return;
    }

}
