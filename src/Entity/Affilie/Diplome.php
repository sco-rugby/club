<?php

namespace App\Entity\Affilie;

use App\Repository\Affilie\DiplomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTaxonomie;

#[ORM\Entity(repositoryClass: DiplomeRepository::class, readOnly: true)]
#[ORM\Table(schema: "club")]
#[ORM\UniqueConstraint(name: "unq_diplome_slug", columns: ["slug"])]
class Diplome extends AbstractTaxonomie {

    #[ORM\OneToMany(mappedBy: 'diplome', targetEntity: AffilieDiplome::class)]
    private Collection $affilies;

    #[ORM\Column(length: 100)]
    private ?string $canonized_libelle = null;

    public function __construct() {
        $this->affilies = new ArrayCollection();
    }

    /**
     * @return Collection<int, AffilieDiplome>
     */
    public function getAffilies(): Collection {
        return $this->affilies;
    }

    public function addAffily(AffilieDiplome $affily): self {
        if (!$this->affilies->contains($affily)) {
            $this->affilies->add($affily);
            $affily->setDiplome($this);
        }

        return $this;
    }

    public function removeAffily(AffilieDiplome $affily): self {
        if ($this->affilies->removeElement($affily)) {
            // set the owning side to null (unless already changed)
            if ($affily->getDiplome() === $this) {
                $affily->setDiplome(null);
            }
        }

        return $this;
    }

}
