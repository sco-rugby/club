<?php

namespace App\Entity\Organisation;

use App\Entity\Club\Club;
use App\Entity\Adresse\Adresse;
use App\Entity\Contact\Contact;
use App\Repository\Organisation\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Model\ManagedResource;
use App\Entity\ImportableInterface;
use \App\Entity\ImportableTrait;
use \App\Entity\TimestampBlameableTrait;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[ORM\Table(schema: "club")]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 5)]
#[ORM\DiscriminatorMap(['club' => Club::class])]
class Organisation implements ManagedResource, ImportableInterface {

    use ImportableTrait,
        TimestampBlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Embedded(class: Adresse::class, columnPrefix: false)]
    private Adresse $adresse;

    #[ORM\Column(length: 1)]
    private ?string $etat = 'A';

    #[ORM\ManyToMany(targetEntity: Contact::class, mappedBy: 'organisations')]
    #[ORM\JoinTable(name: "club.contact_organisation")]
    #[ORM\JoinColumn(unique: true)]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: OrganisationExternal::class)]
    private Collection $ApplisExternes;

    public function __construct() {
        $this->adresse = new Adresse();
        $this->contacts = new ArrayCollection();
        $this->ApplisExternes = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getAdresse(): Adresse {
        return $this->adresse;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getEtat(): ?string {
        return $this->etat;
    }

    public function setEtat(string $etat): self {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->addOrganisation($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self {
        if ($this->contacts->removeElement($contact)) {
            $contact->removeOrganisation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrganisationExternal>
     */
    public function getApplisExternes(): Collection
    {
        return $this->ApplisExternes;
    }

    public function addApplisExterne(OrganisationExternal $applisExterne): self
    {
        if (!$this->ApplisExternes->contains($applisExterne)) {
            $this->ApplisExternes->add($applisExterne);
            $applisExterne->setOrganisation($this);
        }

        return $this;
    }

    public function removeApplisExterne(OrganisationExternal $applisExterne): self
    {
        if ($this->ApplisExternes->removeElement($applisExterne)) {
            // set the owning side to null (unless already changed)
            if ($applisExterne->getOrganisation() === $this) {
                $applisExterne->setOrganisation(null);
            }
        }

        return $this;
    }

}
