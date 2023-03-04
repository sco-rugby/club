<?php

namespace App\Entity\Contact;

use App\Repository\Contact\ContactTelephoneRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\InvalidParameterException;
use Symfony\Component\Intl\Countries;

#[ORM\Entity(repositoryClass: ContactTelephoneRepository::class)]
#[ORM\Table(schema: "club")]
class ContactTelephone {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'telephones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\Column(length: 2, options: ['comment' => 'Code pays ISO 3166-1 alpha-2'])]
    private ?string $code_pays = null;

    #[ORM\Column(length: 20, options: ['comment' => 'sans espace ni autre séparateur'])]
    private ?string $numero = null;

    #[ORM\Column(length: 1, options: ['comment' => 'D domicile,P pro,M mobile,A autre+type_libelle'])]
    private ?string $type = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $type_libelle = null;

    #[ORM\Column]
    private ?bool $prefere = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getContact(): ?Contact {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self {
        $this->contact = $contact;

        return $this;
    }

    public function getCodePays(): ?string {
        return $this->code_pays;
    }

    public function setCodePays(string $pays): self {
        $pays = strtoupper($pays);
        if (!Countries::exists($pays)) {
            throw new InvalidParameterException(sprintf('Le code %s n\'est pas un code pays valide (ISO 3166-1 alpha-2)', $pays));
        }
        $this->code_pays = $pays;

        return $this;
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(string $numero): self {
        $this->numero = $numero;

        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getTypeLibelle(): ?string {
        return $this->type_libelle;
    }

    public function setTypeLibelle(?string $type_libelle): self {
        $this->type_libelle = $type_libelle;

        return $this;
    }

    public function isPrefere(): ?bool {
        return $this->prefere;
    }

    public function setPrefere(bool $prefere): self {
        $this->prefere = $prefere;

        return $this;
    }

}
