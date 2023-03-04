<?php

namespace App\Entity\Datawarehouse\Effectif;

use App\Entity\Jeu\Saison;
use App\Repository\Datawarehouse\Effectif\ClubRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
#[ORM\Table(schema: "datawarehouse")]
class Club {

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $licencies = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $joueurs = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $joueuses = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dirigeants = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $arbitres = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $educateurs = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $soigneurs = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $benevoles = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $effectif = 0;

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getLicencies(): ?int {
        return $this->licencies;
    }

    public function setLicencies(int $licencies): self {
        $this->licencies = $licencies;
        $this->calculerEffectif();
        return $this;
    }

    public function getJoueurs(): ?int {
        return $this->joueurs;
    }

    public function setJoueurs(int $joueurs): self {
        $this->joueurs = $joueurs;

        return $this;
    }

    public function getJoueuses(): ?int {
        return $this->joueuses;
    }

    public function setJoueuses(int $joueuses): self {
        $this->joueuses = $joueuses;

        return $this;
    }

    public function getDirigeants(): ?int {
        return $this->dirigeants;
    }

    public function setDirigeants(int $dirigeants): self {
        $this->dirigeants = $dirigeants;

        return $this;
    }

    public function getArbitres(): ?int {
        return $this->arbitres;
    }

    public function setArbitres(int $arbitres): self {
        $this->arbitres = $arbitres;

        return $this;
    }

    public function getEducateurs(): ?int {
        return $this->educateurs;
    }

    public function setEducateurs(int $educateurs): self {
        $this->educateurs = $educateurs;

        return $this;
    }

    public function getSoigneurs(): ?int {
        return $this->soigneurs;
    }

    public function setSoigneurs(int $soigneurs): self {
        $this->soigneurs = $soigneurs;

        return $this;
    }

    public function getBenevoles(): ?int {
        return $this->benevoles;
    }

    public function setBenevoles(int $benevoles): self {
        $this->benevoles = $benevoles;
        $this->calculerEffectif();
        return $this;
    }

    public function getEffectif(): ?int {
        return $this->effectif;
    }

    private function calculerEffectif(): void {
        $this->effectif = $this->getLicencies() + $this->getBenevoles();
    }

}
