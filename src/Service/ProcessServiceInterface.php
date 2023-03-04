<?php

namespace App\Service;

use App\Entity\Traitement;

interface ProcessServiceInterface {

    public function init(): void;

    public function shutdown(): void;

    public function getTraitement(): ?Traitement;
}
