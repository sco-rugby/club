<?php

namespace App\Entity\Club;

use App\Entity\Affilie\AffilieSection;
use App\Repository\Club\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTaxonomie;

#[ORM\Entity(repositoryClass: SectionRepository::class, readOnly: true)]
#[ORM\Table(schema: "club")]
#[ORM\UniqueConstraint(name: "unq_section_slug", columns: ["slug"])]
class Section extends AbstractTaxonomie {

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $couleur = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'sousSections')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'section', targetEntity: AffilieSection::class)]
    private Collection $affilies;

    #[ORM\OneToMany(mappedBy: 'section', targetEntity: FonctionSection::class)]
    private Collection $fonctions;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $sousSections;

    public function __construct() {
        $this->affilies = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
        $this->sousSections = new ArrayCollection();
    }

    public function getCouleur(): ?string {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self {
        if (null == $couleur) {
            $this->couleur = null;
            return $this;
        }
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $couleur)) {
            $this->couleur = $couleur;
            return $this;
        } else {
            throw new \InvalidArgumentException(sprintf('La couleur doit être au format Hexadécimal. "%s" fourni', $couleur));
        }
    }

    /**
     * @return Collection<int, AffilieSection>
     */
    public function getAffilies(): Collection {
        return $this->affilies;
    }

    public function addAffilie(AffilieSection $affilie): self {
        if (!$this->affilies->contains($affilie)) {
            $this->affilies->add($affilie);
            $affilie->setSection($this);
        }

        return $this;
    }

    public function removeAffilie(AffilieSection $affilie): self {
        if ($this->affilies->removeElement($affilie)) {
            // set the owning side to null (unless already changed)
            if ($affilie->getSection() === $this) {
                $affilie->setSection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FonctionSection>
     */
    public function getFonctions(): Collection {
        return $this->fonctions;
    }

    public function addFonction(FonctionSection $fonction): self {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions->add($fonction);
            $fonction->setSection($this);
        }

        return $this;
    }

    public function removeFonction(FonctionSection $fonction): self {
        if ($this->fonctions->removeElement($fonction)) {
            // set the owning side to null (unless already changed)
            if ($fonction->getSection() === $this) {
                $fonction->setSection(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSousSections(): Collection {
        return $this->sousSections;
    }

    public function addSousSection(self $sousSection): self {
        if (!$this->sousSections->contains($sousSection)) {
            $this->sousSections->add($sousSection);
            $sousSection->setParent($this);
        }

        return $this;
    }

    public function removeSousSection(self $sousSection): self {
        if ($this->sousSections->removeElement($sousSection)) {
            // set the owning side to null (unless already changed)
            if ($sousSection->getParent() === $this) {
                $sousSection->setParent(null);
            }
        }

        return $this;
    }

}
