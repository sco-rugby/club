<?php

namespace App\Entity\Affilie;

use App\Entity\Jeu\Saison;
use App\Repository\Affilie\LicenceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ImportableInterface;
use \App\Entity\ImportableTrait;
use \App\Entity\TimestampBlameableTrait;

#[ORM\Entity(repositoryClass: LicenceRepository::class)]
#[ORM\Table(name: "affilie_licence", schema: "club")]
#[ORM\UniqueConstraint(name: "unq_licence", columns: ["affilie_id", "saison_id", "qualite_id"])]
class Licence implements ImportableInterface {

    use ImportableTrait,
        TimestampBlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $mutation = false;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $PremiereLigne = false;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $formation = false;

    #[ORM\ManyToOne(inversedBy: 'licences', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Affilie $affilie;

    #[ORM\ManyToOne(inversedBy: 'licences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Qualite $qualite;

    #[ORM\ManyToOne(inversedBy: 'licences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison;

    public function getId(): ?int {
        return $this->id;
    }

    public function EnFormation(): ?bool {
        return $this->formation;
    }

    public function setEnFormation(bool $formation = true): self {
        $this->formation = $formation;
        return $this;
    }

    public function isMutation(): ?bool {
        return $this->mutation;
    }

    public function setMutation(bool $mutation = true): self {
        $this->mutation = $mutation;
        return $this;
    }

    public function isPremiereLigne(): ?bool {
        return $this->PremiereLigne;
    }

    public function setPremiereLigne(bool $PremiereLigne = true): self {
        $this->PremiereLigne = $PremiereLigne;
        return $this;
    }

    public function getAffilie(): ?Affilie {
        return $this->affilie;
    }

    public function setAffilie(?Affilie $affilie): self {
        $this->affilie = $affilie;

        return $this;
    }

    public function getQualite(): ?Qualite {
        return $this->qualite;
    }

    public function setQualite(?Qualite $qualite): self {
        $this->qualite = $qualite;

        return $this;
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

}
