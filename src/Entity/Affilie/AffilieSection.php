<?php

namespace App\Entity\Affilie;

use App\Entity\Club\Section;
use App\Entity\Jeu\Saison;
use App\Repository\Affilie\AffilieSectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffilieSectionRepository::class)]
#[ORM\Table(schema: "club")]
class AffilieSection {

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'sections', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Affilie $affilie = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'affilieSections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'affilies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $section = null;

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

    public function getSection(): ?Section {
        return $this->section;
    }

    public function setSection(?Section $section): self {
        $this->section = $section;

        return $this;
    }

}
