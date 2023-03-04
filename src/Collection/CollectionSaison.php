<?php

namespace App\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Jeu\Saison;

class CollectionSaison extends ArrayCollection {

    public function enCours(): ArrayCollection {
        return $this->filter(function (Saison $saison) {
                    //return $saison->getId() = Saison::enCours()
                });
    }

    public function historique($saison = null) {
        if (null === $saison) {
            return $this->filter(function (Saison $saison) {
                        //return $saison->getId() = Saison::enCours()
                    });
        } else {
            
        }
    }

}
