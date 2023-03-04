<?php

namespace App\Service\Datawarehouse;

use App\Service\ProcessServiceInterface;
use App\Entity\Jeu\Saison;

interface DatawarehouseBuilderInterface {

    public function init(Saison $saison): void;

    public function build(): bool;

    public function shutdown(): void;
}
