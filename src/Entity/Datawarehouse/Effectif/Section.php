<?php

namespace App\Entity\Datawarehouse\Effectif;

use App\Entity\Club\Section as SectionClub;
use App\Entity\Jeu\Saison;
use App\Repository\Datawarehouse\Effectif\SectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ORM\Table(schema: "datawarehouse")]
#[ORM\UniqueConstraint(name: 'unq_section', columns: ["saison_id", "section_id"])]
class Section implements \Stringable {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?SectionClub $section = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $regroupement = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $educateurs = null;

    #[ORM\Embedded(class: StatsEffectif::class)]
    private StatsEffectif $actuel;

    #[ORM\Embedded(class: StatsEffectif::class)]
    private StatsEffectif $debut;

    #[ORM\Embedded(class: StatsEffectif::class)]
    private StatsEffectif $encours;

    #[ORM\Embedded(class: StatsEffectif::class)]
    private StatsEffectif $arret;

    public function __construct() {
        $this->actuel = new StatsEffectif();
        $this->debut = new StatsEffectif();
        $this->encours = new StatsEffectif();
        $this->arret = new StatsEffectif();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getSection(): ?SectionClub {
        return $this->section;
    }

    public function setSection(?SectionClub $section): self {
        $this->section = $section;

        return $this;
    }

    public function getLibelle(): ?string {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCouleur(): ?string {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self {
        $this->couleur = $couleur;

        return $this;
    }

    public function isRegroupement(): ?bool {
        return $this->regroupement;
    }

    public function setRegroupement(bool $regroupement): self {
        $this->regroupement = $regroupement;

        return $this;
    }

    public function getEducateurs(): ?int {
        return $this->educateurs;
    }

    public function setEducateurs(int $educateurs): self {
        $this->educateurs = $educateurs;

        return $this;
    }

    public function getActuel(): StatsEffectif {
        return $this->actuel;
    }

    public function getDebut(): StatsEffectif {
        return $this->debut;
    }

    public function getEnCours(): StatsEffectif {
        return $this->encours;
    }

    public function getArret(): StatsEffectif {
        return $this->arret;
    }

    public function __toString(): string {
        return (string) $this->getLibelle();
    }

}
