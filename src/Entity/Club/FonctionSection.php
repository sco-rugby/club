<?php

namespace App\Entity\Club;

use App\Entity\Contact\Contact;
use App\Entity\Jeu\Saison;
use App\Repository\Club\FonctionSectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FonctionSectionRepository::class)]
#[ORM\Table(schema: "club")]
class FonctionSection {

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctionsSection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctionsSection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fonction $fonction = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $section = null;

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self {
        $this->saison = $saison;

        return $this;
    }

    public function getContact(): ?Contact {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self {
        $this->contact = $contact;

        return $this;
    }

    public function getFonction(): ?Fonction {
        return $this->fonction;
    }

    public function setFonction(?Fonction $fonction): self {
        $this->fonction = $fonction;

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
