<?php

namespace App\Entity\Datawarehouse\Effectif;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class StatsPourcentage {

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    protected ?int $nb = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    protected $prc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    protected $prcClub = null;
    //
    protected $effectif = 0,
            $effectifClub = 0;

    public function getNombre(): int {
        return $this->nb;
    }

    public function setNombre(int $nb): self {
        $this->nb = $nb;
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

    public function getPourcentage(): int {
        return $this->prc;
    }

    public function getPourcentageClub(): int {
        return $this->prcClub;
    }

    protected function calculerPourcentage(int $total): ?float {
        if (null === $total || 0 == $total) {
            return null;
        }
        return ($this->getNombre() / $total) * 100;
    }

}
