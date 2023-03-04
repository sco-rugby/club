<?php

namespace App\Entity\Affilie;

use App\Repository\Affilie\AffilieDiplomeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffilieDiplomeRepository::class)]
#[ORM\Table(schema: "club")]
class AffilieDiplome implements \Stringable {

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'diplomes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Affilie $affilie = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'affilies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Diplome $diplome = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEntree = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRecyclage = null;

    public function getDateInscription(): ?\DateTimeInterface {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): self {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface {
        return $this->dateEntree;
    }

    public function setDateEntree(?\DateTimeInterface $dateEntree): self {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): self {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDateRecyclage(): ?\DateTimeInterface {
        return $this->dateRecyclage;
    }

    public function setDateRecyclage(?\DateTimeInterface $dateRecyclage): self {
        $this->dateRecyclage = $dateRecyclage;

        return $this;
    }

    public function getAffilie(): ?Affilie {
        return $this->affilie;
    }

    public function setAffilie(?Affilie $affilie): self {
        $this->affilie = $affilie;

        return $this;
    }

    public function getDiplome(): ?Diplome {
        return $this->diplome;
    }

    public function setDiplome(?Diplome $diplome): self {
        $this->diplome = $diplome;

        return $this;
    }

    public function __toString(): string {
        return (string) $this->getDiplome();
    }

}
