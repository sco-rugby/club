<?php

namespace App\Model;

use App\Entity\Jeu\Saison;

interface SaisonManagedInterface {

    public function getSaison(): ?Saison;

    public function setSaison(Saison $saison);
}
