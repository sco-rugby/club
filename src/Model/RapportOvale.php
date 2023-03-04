<?php

namespace App\Model;

use App\Service\Import\ImportServiceInterface;
use App\Service\Import\ImportAffilie;
use App\Service\Import\ImportLicence;
use App\Service\Import\ImportPassVolontaire;
use App\Model\Import\ImportInterface;
use Doctrine\ORM\EntityManagerInterface;

enum RapportOvale: string implements ImportInterface {

    case OVALE2001 = 'OVALE2-001 : Liste des licences';
    case OVALE2004 = 'OVALE2-004 : Liste des affiliés';
    case OVALE2050 = 'OVALE2-050 : Pass Volontaires';

    public function createService(EntityManagerInterface $em): ImportServiceInterface {
        $service = match ($this) {
            RapportOvale::OVALE2001 => ImportLicence::class,
            RapportOvale::OVALE2004 => ImportAffilie::class,
            RapportOvale::OVALE2050 => ImportPassVolontaire::class,
        };
        return new $service($em);
    }

    public function getDescription(): string {
        return match ($this) {
            RapportOvale::OVALE2001 => 'Liste des licences de la structure.<br />Plusieurs lignes pour une personne possédant plusieurs licences',
            RapportOvale::OVALE2004 => 'Liste des personnes ayant eu au moins une licence dans la structure.<br />Une seule ligne par personne, quel que soit son nombre de licence',
            RapportOvale::OVALE2050 => 'Extraction des Pass Volontaires',
        };
    }

    public function getMapping(): array {
        return match ($this) {
            //, 'filler1', 'filler2'
            RapportOvale::OVALE2001 => ['ligue_code', 'ligue_nom', 'club_code', 'club_nom', 'cd', 'saison', 'licence', 'date_première_affiliation', 'nom', 'prenom', 'sexe', 'nationalite', 'annee_naissance', 'date_naissance', 'ville_naissance', 'age', 'classe_age', 'qualite', 'autorisation_ffr', 'autorisation_tiers', '1ere_ligne', 'lca', 'dat', 'diplomes', 'type_contrat', 'qualite_date_début', 'qualite_date_fin', 'email', 'telephone_dom', 'telephone_pro', 'telephone_port', 'geocodage_', 'voie', 'localite', 'lieu-dit', 'immeuble', 'complement', 'code_postal', 'bassin'],
            RapportOvale::OVALE2004 => [],
            RapportOvale::OVALE2050 => ['ligue_code', 'ligue_nom', 'cd', 'club_code', 'club_nom', 'nom', 'prenom', 'sexe', 'date_naissance', 'nationalite', 'voie', 'localite', 'lieu-dit', 'immeuble', 'complement', 'code_postal', 'email', 'telephone_dom', 'telephone_port', 'telephone_pro', 'telephone_public', 'saison',	'fonctions', 'bassin'],
        };
    }

}
