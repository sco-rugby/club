<?php

namespace App\Event;

use App\Model\Import\ImportInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ImportFichierEvent extends Event {

    public const AFFILIE = 'import.fichier.affilie';
    public const LICENCE = 'import.fichier.licence';
    public const BENEVOLE = 'import.fichier.benevole';

    public function __construct(protected ImportInterface $type, protected \SplFileInfo $fichier) {
        return;
    }

    public function getType(): ImportInterface {
        return $this->type;
    }

    public function getFichier(): \SplFileInfo {
        return $this->fichier;
    }

}
