<?php

namespace App\Manager\Adresse;

use App\Manager\AbstractResourceManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Adresse\Adresse;
use App\Entity\Adresse\Commune;
use App\Repository\Adresse\CommuneRepository;
use App\Entity\Adresse\CodePostal;
use App\Repository\Adresse\CodePostalRepository;
use Symfony\Component\String\UnicodeString;

final class AdresseManager extends AbstractResourceManager {

    public function __construct(CommuneRepository $repository, ?EventDispatcherInterface $dispatcher = null, ?ValidatorInterface $validator = null, ?FormFactoryInterface $formFactory = null) {
        parent::__construct($repository, $dispatcher, $validator, $formFactory);
    }

    static public function normalizeAddress(Adresse &$adresse): void {
        $elmt['adresse'] = $adresse->getAdresse();
        $elmt['complement'] = $adresse->getComplement();
        $elmt['ville'] = $adresse->getVille();
        //
        foreach ($elmt as $id => $value) {
            if (!empty($value)) {
                $elmt[$id] = (new UnicodeString($value))->trim()
                        ->folded()->title(true)
                        ->replace(' Le ', ' le ')
                        ->replace("L'", "l'")
                        ->replace("D'", "d'")
                        ->replace(' De ', ' de ')
                        ->replace(' Des ', ' des ')
                        ->replace(' Du ', ' du ')
                        ->replace(' En ', ' en ');
            }
        }
        //
        $adresse->setAdresse($elmt['adresse']);
        $adresse->setComplement($elmt['complement']);
        //
        if (!empty($elmt['ville'])) {
            $ville = Commune::normalizeNom($elmt['ville']);
            $adresse->setVille($ville);
        }
    }

    static public function canonizeNom(string $commune): string {
        return Commune::canonizeNom($commune);
    }

    protected function dispatchException(\Exception $ex) {
        return;
    }

    protected function dispatchEvent(string $name) {
        return;
    }

}
