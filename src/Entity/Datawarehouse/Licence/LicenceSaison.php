<?php

namespace App\Entity\Datawarehouse\Licence;

use App\Entity\Jeu\Saison;
use App\Repository\Datawarehouse\Licence\LicenceSaisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicenceSaisonRepository::class)]
#[ORM\Table(schema: "datawarehouse")]
#[ORM\UniqueConstraint(name: 'unq_licence_saison', columns: ["licence_id", "saison_id"])]
class LicenceSaison {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'saisons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Licence $licence = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $age = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nombreSaisons = null;

    #[ORM\Column]
    private ?bool $debut = false;

    #[ORM\Column]
    private ?bool $arret = false;

    #[ORM\Column]
    private ?bool $joueur = false;

    #[ORM\Column]
    private ?bool $educateur = false;

    #[ORM\Column]
    private ?bool $arbitre = false;

    #[ORM\Column]
    private ?bool $soigneur = false;

    #[ORM\Column]
    private ?bool $dirigeant = false;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLicence(): ?Licence {
        return $this->licence;
    }

    public function setLicence(?Licence $licence): self {
        $this->licence = $licence;

        return $this;
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getAge(): ?int {
        return $this->age;
    }

    public function setAge(int $age): self {
        $this->age = $age;

        return $this;
    }

    public function getNombreSaisons(): ?int {
        return $this->nombreSaisons;
    }

    public function setNombreSaisons(int $nombreSaisons): self {
        $this->nombreSaisons = $nombreSaisons;

        return $this;
    }

    public function isDebut(): bool {
        return $this->debut;
    }

    public function setDebut(bool $bool = true): self {
        $this->debut = $bool;
        return $this;
    }

    public function isArret(): bool {
        return $this->arret;
    }

    public function setArret(bool $bool = true): self {
        $this->arret = $bool;
        return $this;
    }

    public function isJoueur(): ?bool {
        return $this->joueur;
    }

    public function setJoueur(bool $joueur = true): self {
        $this->joueur = $joueur;

        return $this;
    }

    public function isEducateur(): ?bool {
        return $this->educateur;
    }

    public function setEducateur(bool $educateur = true): self {
        $this->educateur = $educateur;

        return $this;
    }

    public function isArbitre(): ?bool {
        return $this->arbitre;
    }

    public function setArbitre(bool $arbitre = true): self {
        $this->arbitre = $arbitre;

        return $this;
    }

    public function isSoigneur(): ?bool {
        return $this->soigneur;
    }

    public function setSoigneur(bool $soigneur = true): self {
        $this->soigneur = $soigneur;

        return $this;
    }

    public function isDirigeant(): ?bool {
        return $this->dirigeant;
    }

    public function setDirigeant(bool $dirigeant = true): self {
        $this->dirigeant = $dirigeant;

        return $this;
    }

}
