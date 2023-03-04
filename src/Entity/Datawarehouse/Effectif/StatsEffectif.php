<?php

namespace App\Entity\Datawarehouse\Effectif;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class StatsEffectif extends StatsPourcentage {

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $joueurs = 0;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $joueuses = 0;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private $ageMoyen = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private $dureeMoyenne = null;

    public function getJoueurs(): int {
        return intval($this->joueurs);
    }

    public function setJoueurs(int $nb): self {
        $this->joueurs = $nb;
        $this->setNombre($this->joueurs + $this->getJoueuses());
        $this->prc = $this->calculerPourcentage($this->effectif);
        $this->prcClub = $this->calculerPourcentage($this->effectifClub);
        //$this->prcEffectif = $this->calculerPourcentage($this->effectifTotal);
        return $this;
    }

    public function getJoueuses(): int {
        return intval($this->joueuses);
    }

    public function setJoueuses(int $nb): self {
        $this->joueuses = $nb;

        $this->setNombre($this->getJoueurs() + $this->joueuses);
        $this->prc = $this->calculerPourcentage($this->effectif);
        $this->prcClub = $this->calculerPourcentage($this->effectifClub);
        //$this->prcEffectif = $this->calculerPourcentage($this->effectifTotal);
        return $this;
    }

    public function setEffectif(int $nb): self {
        $this->effectif = $nb;
        $this->prc = $this->calculerPourcentage($this->effectif);
        return $this;
    }

    public function setEffectifClub(int $nb): self {
        $this->effectifClub = $nb;
        $this->prcClub = $this->calculerPourcentage($this->effectifClub);
        return $this;
    }

    public function getAgeMoyen(): int {
        return $this->ageMoyen;
    }

    /**
     * Liste des âges pour calculer les moyennes
     */
    public function setAges(array $data): void {
        $this->ageMoyen = $this->calculerMoyenne($data);
    }

    public function getDureeMoyenne(): int {
        return $this->dureeMoyenne;
    }

    /**
     * Liste des durées pour calculer les moyennes
     */
    public function setDurees(array $data): void {
        $this->dureeMoyenne = $this->calculerMoyenne($data);
    }

    public function getPourcentage(): int {
        return $this->prc;
    }

    public function getPourcentageClub(): int {
        return $this->prcClub;
    }

    private function calculerMoyenne(array $data): ?int {
        if (empty($data)) {
            return null;
        }
        $somme = 0;
        forEach ($data as $elmt) {
            $somme += $elmt;
        }
        return $somme / count($data);
    }

}
