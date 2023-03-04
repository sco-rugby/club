<?php

namespace App\Entity;

use App\Repository\TraitementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraitementRepository::class)]
class Traitement implements \Stringable {

    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const ERROR = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $traitement = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $debut = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $fin = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getTraitement(): ?string {
        return $this->traitement;
    }

    public function setTraitement(string $traitement): self {
        $this->traitement = $traitement;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface {
        return $this->debut;
    }

    public function setDebut(?\DateTimeInterface $debut = null): self {
        if (null === $debut) {
            $debut = new \DateTimeImmutable();
        }
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin = null): self {
        if (null === $fin) {
            $fin = new \DateTimeImmutable();
        }
        $this->fin = $fin;

        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(string $status): self {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): self {
        $this->message = $message;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }


    public function __toString(): string {
        return sprintf('%s %s-%s : %s', $this->getDescription(), $this->getDebut()->format('H:i:s'), $this->getFin()->format('H:i:s'), $this->getMessage());
    }
}
