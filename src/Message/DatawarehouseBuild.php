<?php

namespace App\Message;

class DatawarehouseBuild {

    public function __construct(protected int $annee) {
        return;
    }

    public function getAnnee(): int {
        return $this->annee;
    }

}
