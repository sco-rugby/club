<?php

namespace App\Manager\Jeu;

use App\Entity\Jeu\Saison;

interface SaisonManagerInterface {

    public function getSaison(): ?Saison;

    public function setSaison(Saison $saison);
}
