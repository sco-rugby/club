<?php

namespace App\Entity\Affilie;

use App\Entity\Contact\Contact as ContactTuteur;
use App\Repository\Affilie\TuteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TuteurRepository::class)]
#[ORM\Table(schema: "club")]
#[ORM\UniqueConstraint(name: "unq_tuteur_affilie", columns: ["tuteur_id", "affilie_id"])]
class Tuteur {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ContactTuteur $tuteur = null;

    #[ORM\ManyToOne(inversedBy: 'tuteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Affilie $affilie = null;

    #[ORM\Column(length: 1)]
    private ?string $type = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getTuteur(): ?ContactTuteur {
        return $this->tuteur;
    }

    public function setTuteur(ContactTuteur $tuteur): self {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getAffilie(): ?Affilie {
        return $this->affilie;
    }

    public function setAffilie(?Affilie $affilie): self {
        $this->affilie = $affilie;

        return $this;
    }

    public function __call($method, $arguments) {
        if (!method_exists(ContactTuteur::class, $method)) {
            throw \InvalidArgumentException(sprintf('La méthode %s n\'existe pas dans la classe %s', $method, ContactTuteur::class));
        }
        return $this->getTuteur()->$method($arguments);
    }

}
