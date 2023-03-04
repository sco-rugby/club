<?php

namespace App\Entity\Jeu;

use App\Entity\Affilie\AffilieSection;
use App\Entity\Affilie\DoubleLicence;
use App\Entity\Club\FonctionClub;
use App\Entity\Club\FonctionSection;
use App\Repository\Jeu\SaisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\ManagedResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Affilie\Licence;

#[ORM\Entity(repositoryClass: SaisonRepository::class)]
#[ORM\Table(schema: "club")]
class Saison implements ManagedResource, \Stringable {

    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fin = null;
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: Licence::class, orphanRemoval: true)]
    private Collection $licences;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: FonctionSection::class, orphanRemoval: true)]
    private Collection $fonctionsSection;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: AffilieSection::class, orphanRemoval: true)]
    private Collection $affilieSections;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: DoubleLicence::class)]
    private Collection $doublesLicences;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: FonctionClub::class)]
    private Collection $fonctionsClub;

    public function __construct(?int $annee = null) {
        if (!is_null($annee)) {
            $this->setId($annee);
        }
        /* $this->licences = new ArrayCollection();
          $this->fonctionsSection = new ArrayCollection();
          $this->fonctionsClub = new ArrayCollection();
          $this->affilieSections = new ArrayCollection();
          $this->doublesLicences = new ArrayCollection(); */
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        $this->setDebut(new \DateTime($id . '-07-01'));
        $fin = $this->getDebut()->add(new \DateInterval('P1Y'));
        $fin->sub(new \DateInterval('P1D'));
        $this->setFin($fin);
        return $this;
    }

    public function getLibelle(): ?string {
        if (is_null($this->libelle) & !is_null($this->debut) & !is_null($this->fin)) {
            $this->libelle = $this->getDebut()->format('Y') . '-' . $this->getFin()->format('Y');
        }
        return $this->libelle;
    }

    public function getDebut(): ?\DateTimeInterface {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self {
        $this->fin = $fin;

        return $this;
    }

    public function __toString() {
        return (string) $this->getLibelle();
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
            $licence->setSaison($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): self {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getSaison() === $this) {
                $licence->setSaison(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FonctionSection>
     */
    public function getFonctionsSection(): Collection {
        return $this->fonctionsSection;
    }

    public function addFonctionsSection(FonctionSection $fonctionsSection): self {
        if (!$this->fonctionsSection->contains($fonctionsSection)) {
            $this->fonctionsSection->add($fonctionsSection);
            $fonctionsSection->setSaison($this);
        }

        return $this;
    }

    public function removeFonctionsSection(FonctionSection $fonctionsSection): self {
        if ($this->fonctionsSection->removeElement($fonctionsSection)) {
            // set the owning side to null (unless already changed)
            if ($fonctionsSection->getSaison() === $this) {
                $fonctionsSection->setSaison(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AffilieSection>
     */
    public function getAffilieSections(): Collection {
        return $this->affilieSections;
    }

    public function addAffilieSection(AffilieSection $affilieSection): self {
        if (!$this->affilieSections->contains($affilieSection)) {
            $this->affilieSections->add($affilieSection);
            $affilieSection->setSaison($this);
        }

        return $this;
    }

    public function removeAffilieSection(AffilieSection $affilieSection): self {
        if ($this->affilieSections->removeElement($affilieSection)) {
            // set the owning side to null (unless already changed)
            if ($affilieSection->getSaison() === $this) {
                $affilieSection->setSaison(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DoubleLicence>
     */
    public function getDoublesLicences(): Collection {
        return $this->doublesLicences;
    }

    public function addDoublesLicence(DoubleLicence $doublesLicence): self {
        if (!$this->doublesLicences->contains($doublesLicence)) {
            $this->doublesLicences->add($doublesLicence);
            $doublesLicence->setSaison($this);
        }

        return $this;
    }

    public function removeDoublesLicence(DoubleLicence $doublesLicence): self {
        if ($this->doublesLicences->removeElement($doublesLicence)) {
            // set the owning side to null (unless already changed)
            if ($doublesLicence->getSaison() === $this) {
                $doublesLicence->setSaison(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FonctionClub>
     */
    public function getFonctionsClub(): Collection {
        return $this->fonctionsClub;
    }

    public function addFonctionsClub(FonctionClub $fonctionsClub): self {
        if (!$this->fonctionsClub->contains($fonctionsClub)) {
            $this->fonctionsClub->add($fonctionsClub);
            $fonctionsClub->setSaison($this);
        }

        return $this;
    }

    public function removeFonctionsClub(FonctionClub $fonctionsClub): self {
        if ($this->fonctionsClub->removeElement($fonctionsClub)) {
            // set the owning side to null (unless already changed)
            if ($fonctionsClub->getSaison() === $this) {
                $fonctionsClub->setSaison(null);
            }
        }

        return $this;
    }

}
