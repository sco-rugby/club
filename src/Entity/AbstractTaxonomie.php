<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\MappedSuperclass]
abstract class AbstractTaxonomie implements \Stringable {

    #[ORM\Id]
    #[ORM\Column(length: 10)]
    private ?string $id = null;

    #[ORM\Column(length: 50)]
    private ?string $libelle = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function getLibelle(): ?string {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function setSlug(string $slug, bool $sluggify = false): self {
        if (true === $sluggify) {
            $this->slug = $this->sluggify($slug);
        } else {
            $this->slug = $slug;
        }
        return $this;
    }

    protected function sluggify(string $slug) {
        return (new AsciiSlugger())->slug($slug);
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string {
        return (string) $this->getLibelle();
    }

}
