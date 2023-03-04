<?php

namespace App\Entity\Club;

use App\Repository\Club\FonctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTaxonomie;

#[ORM\Entity(repositoryClass: FonctionRepository::class)]
#[ORM\Table(schema: "club")]
#[ORM\UniqueConstraint(name: "unq_fonction_slug", columns: ["slug"])]
class Fonction extends AbstractTaxonomie {

    #[ORM\OneToMany(mappedBy: 'fonction', targetEntity: FonctionClub::class, orphanRemoval: true)]
    private Collection $fonctionsClub;

    #[ORM\OneToMany(mappedBy: 'fonction', targetEntity: FonctionSection::class, orphanRemoval: true)]
    private Collection $sections;

    public function __construct() {
        $this->fonctionsClub = new ArrayCollection();
        $this->sections = new ArrayCollection();
    }

    /**
     * @return Collection<int, FonctionsClub>
     */
    public function getFonctionsClub(): Collection {
        return $this->fonctionsClub;
    }

    public function addFonctionsClub(FonctionsClub $fonctionsClub): self {
        if (!$this->fonctionsClub->contains($fonctionsClub)) {
            $this->fonctionsClub->add($fonctionsClub);
            $fonctionsClub->setFonction($this);
        }

        return $this;
    }

    public function removeFonctionsClub(FonctionsClub $fonctionsClub): self {
        if ($this->fonctionsClub->removeElement($fonctionsClub)) {
            // set the owning side to null (unless already changed)
            if ($fonctionsClub->getFonction() === $this) {
                $fonctionsClub->setFonction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FonctionSection>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(FonctionSection $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setFonction($this);
        }

        return $this;
    }

    public function removeSection(FonctionSection $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getFonction() === $this) {
                $section->setFonction(null);
            }
        }

        return $this;
    }

}
