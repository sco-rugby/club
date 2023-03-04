<?php

namespace App\Entity\Club;

use App\Entity\Contact\Contact;
use App\Repository\Club\FonctionClubRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Jeu\Saison;

#[ORM\Entity(repositoryClass: FonctionClubRepository::class)]
#[ORM\Table(schema: "club")]
class FonctionClub {

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctionsClub', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctionsClub')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fonction $fonction = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'fonctionsClub')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    public function getNote(): ?string {
        return $this->note;
    }

    public function setNote(?string $note): self {
        $this->note = $note;

        return $this;
    }

    public function getSaison(): ?Saison {
        return $this->saison;
    }

    public function setSaison(Saison $saison): self {
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

}
