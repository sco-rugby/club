<?php

namespace App\Command;

trait SaisonUtility {

    protected static function convertirSaison($annee): int {
        if (is_string($annee)) {
            $annee = substr($annee, 0, 4);
        }
        if (preg_match("/\D/", $annee)) {
            throw new \InvalidArgumentException(sprintf('L\'année doit être de type "integer". (%s) "%s" fourni', gettype($annee), $annee));
        }
        return intval($annee);
    }

}
