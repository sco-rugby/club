<?php

namespace App\Entity\Datawarehouse;

use App\Entity\Adresse\Commune;
use App\Entity\Club\Section;
use App\Entity\Jeu\Saison;
use App\Repository\Datawarehouse\GeomapRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeomapRepository::class)]
#[ORM\Table(schema: 'datawarehouse')]
#[ORM\UniqueConstraint(name: 'unq_geomap', columns: ['saison_id', 'section_id', 'commune_id'])]
class Geomap {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    //#[ORM\ManyToOne]
    //private ?Section $section = null;
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $section_id = null;

    /* [ORM\ManyToOne]
      [ORM\JoinColumn(nullable: false)]
      private ?Commune $commune = null; */

    #[ORM\Column(length: 5)]
    private ?string $commune_id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 13, scale: 10)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 13, scale: 10)]
    private ?string $longitude = null;

    #[ORM\Column]
    private ?int $nb = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourcentage = null;

    #[ORM\Column]
    private ?int $joueurs = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_joueurs = null;

    #[ORM\Column]
    private ?int $joueuses = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_joueuses = null;

    #[ORM\Column]
    private ?int $educateurs = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_educateurs = null;

    #[ORM\Column]
    private ?int $debutants = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_debutants = null;

    #[ORM\Column]
    private ?int $encours = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_encours = null;

    #[ORM\Column]
    private ?int $arrets = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $pourc_arrets = null;

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

    public function getSection(): ?string {
        return $this->section_id;
    }

    public function setSection(?string $section): self {
        $this->section_id = $section;

        return $this;
    }

    public function getCommune(): ?string {
        return $this->commune_id;
    }

    public function setCommune(?string $commune): self {
        $this->commune_id = $commune;

        return $this;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getLatitude(): ?string {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self {
        $this->longitude = $longitude;

        return $this;
    }

    public function getNb(): ?int {
        return $this->nb;
    }

    public function setNb(int $nb): self {
        $this->nb = $nb;

        return $this;
    }

    public function getPourcentage(): ?string {
        return $this->pourcentage;
    }

    public function setPourcentage(string $pourcentage): self {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getJoueurs(): ?int {
        return $this->joueurs;
    }

    public function setJoueurs(int $joueurs): self {
        $this->joueurs = $joueurs;

        return $this;
    }

    public function getPourcentageJoueurs(): ?string {
        return $this->pourc_joueurs;
    }

    public function setPourcentageJoueurs(string $pourc_joueurs): self {
        $this->pourc_joueurs = $pourc_joueurs;

        return $this;
    }

    public function getJoueuses(): ?int {
        return $this->joueuses;
    }

    public function setJoueuses(int $joueuses): self {
        $this->joueuses = $joueuses;

        return $this;
    }

    public function getPourcentageJoueuses(): ?string {
        return $this->pourc_joueuses;
    }

    public function setPourcentageJoueuses(string $pourc_joueuses): self {
        $this->pourc_joueuses = $pourc_joueuses;

        return $this;
    }

    public function getEducateurs(): ?int {
        return $this->educateurs;
    }

    public function setEducateurs(int $educateurs): self {
        $this->educateurs = $educateurs;

        return $this;
    }

    public function getPourcentageEducateurs(): ?string {
        return $this->pourc_educateurs;
    }

    public function setPourcentageEducateurs(string $pourc_educateurs): self {
        $this->pourc_educateurs = $pourc_educateurs;

        return $this;
    }

    public function getDebutants(): ?int {
        return $this->debutants;
    }

    public function setDebutants(int $debutants): self {
        $this->debutants = $debutants;

        return $this;
    }

    public function getPourcentageDebutants(): ?string {
        return $this->pourc_debutants;
    }

    public function setPourcentageDebutants(string $pourc_debutants): self {
        $this->pourc_debutants = $pourc_debutants;

        return $this;
    }

    public function getEncours(): ?int {
        return $this->encours;
    }

    public function setEncours(int $encours): self {
        $this->encours = $encours;

        return $this;
    }

    public function getPourcentageEncours(): ?string {
        return $this->pourc_encours;
    }

    public function setPourcentageEncours(string $pourc_encours): self {
        $this->pourc_encours = $pourc_encours;

        return $this;
    }

    public function getArrets(): ?int {
        return $this->arrets;
    }

    public function setArrets(int $arrets): self {
        $this->arrets = $arrets;

        return $this;
    }

    public function getPourcentageArrets(): ?string {
        return $this->pourc_arrets;
    }

    public function setPourcentageArrets(string $pourc_arrets): self {
        $this->pourc_arrets = $pourc_arrets;

        return $this;
    }

}
