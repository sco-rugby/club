<?php

namespace App\Entity\Club;

use App\Entity\Affilie\Affilie;
use App\Entity\Affilie\DoubleLicence;
use App\Entity\Media\Media;
use App\Entity\Organisation\Organisation;
use App\Repository\Club\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class, readOnly: true)]
#[ORM\Table(schema: "club")]
class Club extends Organisation {

    const APPLI_MAITRE = 'oval-e';

    #[ORM\Column(length: 10)]
    private ?string $club_id = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $couleurs = [];

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $logo = null;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Affilie::class)]
    private Collection $affilies;

    #[ORM\OneToMany(mappedBy: 'accueil', targetEntity: DoubleLicence::class, orphanRemoval: true)]
    private Collection $doubleLicences;

    #[ORM\OneToMany(mappedBy: 'pret', targetEntity: DoubleLicence::class, orphanRemoval: true)]
    private Collection $pretAffilies;

    public function __construct() {
        parent::__construct();
        $this->affilies = new ArrayCollection();
        $this->doubleLicences = new ArrayCollection();
        $this->pretAffilies = new ArrayCollection();
    }

    public function getClubId(): ?int {
        return $this->club_id;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

        return $this;
    }

    public function getAppliMaitre(): ?string {
        return self::APPLI_MAITRE;
    }

    public function setAppliMaitre(string $appli): self {
        $this->appliMaitre = self::APPLI_MAITRE;
        return $this;
    }

    public function getCouleurs(): array {
        return $this->couleurs;
    }

    public function setCouleurs(?array $couleurs): self {
        $this->couleurs = $couleurs;

        return $this;
    }

    public function getLogo(): ?Media {
        return $this->logo;
    }

    public function setLogo(?Media $logo): self {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Affilie>
     */
    public function getAffilies(): Collection {
        return $this->affilies;
    }

    public function addAffilie(Affilie $affilie): self {
        if (!$this->affilies->contains($affilie)) {
            $this->affilies->add($affilie);
            $affilie->setClub($this);
        }

        return $this;
    }

    public function removeAffilie(Affilie $affilie): self {
        if ($this->affilies->removeElement($affilie)) {
            // set the owning side to null (unless already changed)
            if ($affilie->getClub() === $this) {
                $affilie->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DoubleLicence>
     */
    public function getDoubleLicences(): Collection {
        return $this->doubleLicences;
    }

    public function addDoubleLicence(DoubleLicence $doubleLicence): self {
        if (!$this->doubleLicences->contains($doubleLicence)) {
            $this->doubleLicences->add($doubleLicence);
            $doubleLicence->setAccueil($this);
        }

        return $this;
    }

    public function removeDoubleLicence(DoubleLicence $doubleLicence): self {
        if ($this->doubleLicences->removeElement($doubleLicence)) {
            // set the owning side to null (unless already changed)
            if ($doubleLicence->getAccueil() === $this) {
                $doubleLicence->setAccueil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DoubleLicence>
     */
    public function getPretAffilies(): Collection {
        return $this->pretAffilies;
    }

    public function addPretAffilie(DoubleLicence $pretAffiliE): self {
        if (!$this->pretAffilies->contains($pretAffiliE)) {
            $this->pretAffilies->add($pretAffiliE);
            $pretAffiliE->setPret($this);
        }

        return $this;
    }

    public function removePretAffilie(DoubleLicence $pretAffiliE): self {
        if ($this->pretAffilies->removeElement($pretAffiliE)) {
            // set the owning side to null (unless already changed)
            if ($pretAffiliE->getPret() === $this) {
                $pretAffiliE->setPret(null);
            }
        }

        return $this;
    }

}
