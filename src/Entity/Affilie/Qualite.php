<?php

namespace App\Entity\Affilie;

use App\Repository\Affilie\QualiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTaxonomie;

#[ORM\Entity(repositoryClass: QualiteRepository::class, readOnly: true)]
#[ORM\Table(schema: "club")]
#[ORM\Index(name: "idx_qualite_groupe", columns: ["groupe"])]
#[ORM\UniqueConstraint(name: "unq_qualite_slug", columns: ["slug"])]
class Qualite extends AbstractTaxonomie {

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $groupe = null;

    #[ORM\Column(name: 'dat', nullable: true)]
    private ?bool $accesTerrain = null;

    #[ORM\OneToMany(mappedBy: 'qualite', targetEntity: Licence::class, orphanRemoval: true)]
    private Collection $licences;

    public function __construct() {
        $this->licences = new ArrayCollection();
    }

    public function getGroupe(): ?string {
        return $this->groupe;
    }

    public function setGroupe(?string $groupe): self {
        $this->groupe = $groupe;

        return $this;
    }

    public function hasAccesTerrain(): ?bool {
        return $this->accesTerrain;
    }

    public function setAccesTerrain(?bool $accesTerrain = true): self {
        $this->accesTerrain = $accesTerrain;

        return $this;
    }

    /**
     * @return Collection<int, Licence>
     */
    public function getLicences(): Collection {
        return $this->licences;
    }

    public function addLicence(Licence $licence): self {
        if (!$this->licences->contains($licence)) {
            $this->licences->add($licence);
            $licence->setQualite($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): self {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getQualite() === $this) {
                $licence->setQualite(null);
            }
        }

        return $this;
    }

}
