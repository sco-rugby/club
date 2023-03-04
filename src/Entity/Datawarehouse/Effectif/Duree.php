<?php

namespace App\Entity\Datawarehouse\Effectif;

use App\Entity\Club\Section;
use App\Entity\Jeu\Saison;
use App\Repository\Datawarehouse\Effectif\DureeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DureeRepository::class)]
#[ORM\Table(schema: "datawarehouse")]
#[ORM\UniqueConstraint(name: 'unq_duree', columns: ["saison_id", "section_id", "duree"])]
class Duree {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Section $section = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Embedded(class: StatsPourcentage::class)]
    private StatsPourcentage $encours;

    #[ORM\Embedded(class: StatsPourcentage::class)]
    private StatsPourcentage $arret;

    public function __construct() {
        $this->encours = new StatsPourcentage();
        $this->arret = new StatsPourcentage();
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getSection(): ?Section {
        return $this->section;
    }

    public function setSection(?Section $section): self {
        $this->section = $section;

        return $this;
    }

    public function getDuree(): ?int {
        return $this->duree;
    }

    public function setDuree(int $duree): self {
        $this->duree = $duree;

        return $this;
    }

    public function getArret(): StatsPourcentage {
        return $this->arret;
    }

    public function getEnCours(): StatsPourcentage {
        return $this->encours;
    }

}
