<?php

namespace App\Entity\Affilie;

use App\Entity\Club\Club;
use App\Entity\Jeu\Saison;
use App\Repository\Affilie\DoubleLicenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoubleLicenceRepository::class)]
#[ORM\Table(schema: "club")]
#[ORM\UniqueConstraint(name: "unq_double_licence", columns: ["affilie_id", "saison_id", "club_accueil", "club_pret"])]
class DoubleLicence {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'doubleLicences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Affilie $affilie;

    #[ORM\ManyToOne(inversedBy: 'doublesLicences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison;

    #[ORM\ManyToOne(inversedBy: 'doubleLicences')]
    #[ORM\JoinColumn(name: 'club_accueil', nullable: false)]
    private ?Club $accueil = null;

    #[ORM\ManyToOne(inversedBy: 'pretAffilies')]
    #[ORM\JoinColumn(name: 'club_pret', nullable: false)]
    private ?Club $pret = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getAffilie(): ?Affilie {
        return $this->affilie;
    }

    public function setAffilie(?Affilie $affilie): self {
        $this->affilie = $affilie;

        return $this;
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getAccueil(): ?Club {
        return $this->accueil;
    }

    public function setAccueil(?Club $accueil): self {
        $this->accueil = $accueil;

        return $this;
    }

    public function getPret(): ?Club {
        return $this->pret;
    }

    public function setPret(?Club $pret): self {
        $this->pret = $pret;

        return $this;
    }

}
