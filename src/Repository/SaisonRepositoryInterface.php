<?php

namespace App\Repository;

use App\Entity\Jeu\Saison;

interface SaisonRepositoryInterface {

    public function findBySaison(Saison $saison): array;
}
