<?php

namespace App\Entity\Contact;

use App\Entity\Adresse\Commune;
use App\Entity\Club\FonctionSection;
use App\Entity\Club\FonctionClub;
use App\Entity\Media\Media;
use App\Entity\Organisation\Organisation;
use App\Repository\Contact\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\ManagedResource;
use App\Entity\Adresse\Adresse;
use App\Entity\Affilie\Affilie;
use App\Entity\ImportableInterface;
use \App\Entity\ImportableTrait;
use \App\Entity\TimestampBlameableTrait;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(schema: "club")]
#[ORM\HasLifecycleCallbacks]
class Contact implements ManagedResource, ImportableInterface {

    use ImportableTrait,
        TimestampBlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: Adresse::class, columnPrefix: false)]
    private Adresse $adresse;

    #[ORM\Column]
    private bool $public = false;

    #[ORM\Column]
    private bool $listeRouge = true;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 1)]
    private ?string $genre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $photo = null;

    #[ORM\ManyToOne]
    private ?Commune $commune = null;

    #[ORM\OneToOne(mappedBy: 'contact', cascade: ['persist', 'remove'])]
    private ?Affilie $affilie = null;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: FonctionClub::class, orphanRemoval: true)]
    private Collection $fonctionsClub;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: FonctionSection::class, orphanRemoval: true)]
    private Collection $fonctionsSection;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: GroupeContact::class, orphanRemoval: true)]
    private Collection $groupes;

    #[ORM\ManyToMany(targetEntity: Organisation::class, inversedBy: 'contacts')]
    #[ORM\JoinTable(name: "club.contact_organisation")]
    #[ORM\JoinColumn(unique: true)]
    private Collection $organisations;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: ContactTelephone::class, orphanRemoval: true, cascade: ['all'])]
    private Collection $telephones;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: ContactEmail::class, orphanRemoval: true, cascade: ['all'])]
    private Collection $emails;

    public function __construct() {
        $this->adresse = new Adresse();
        $this->fonctionsClub = new ArrayCollection();
        $this->fonctionsSection = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->organisations = new ArrayCollection();
        $this->telephones = new ArrayCollection();
        $this->emails = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getAdresse(): Adresse {
        return $this->adresse;
    }

    public function setAdresse(Adresse $adresse): self {
        $this->adresse = $adresse;
        return $this;
    }

    public function getAffilie(): ?Affilie {
        return $this->affilie;
    }

    public function setAffilie(Affilie $affilie): self {
        // set the owning side of the relation if necessary
        if ($affilie->getContact() !== $this) {
            $affilie->setContact($this);
        }

        $this->affilie = $affilie;

        return $this;
    }

    public function isAffilie(): bool {
        return (null !== $this->affilie);
    }

    public function isPublic(): ?bool {
        return $this->public;
    }

    public function setPublic(bool $public = true): self {
        $this->public = $public;

        return $this;
    }

    public function isListeRouge(): ?bool {
        return $this->listeRouge;
    }

    public function setListeRouge(bool $listeRouge = true): self {
        $this->listeRouge = $listeRouge;

        return $this;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = $prenom;

        return $this;
    }

    public function getGenre(): ?string {
        return $this->genre;
    }

    public function setGenre(string $genre): self {
        $this->genre = $genre;

        return $this;
    }

    public function getNote(): ?string {
        return $this->note;
    }

    public function setNote(?string $note): self {
        $this->note = $note;

        return $this;
    }

    public function getPhoto(): ?Media {
        return $this->photo;
    }

    public function setPhoto(?Media $photo): self {
        $this->photo = $photo;

        return $this;
    }

    public function getAppliMaitre(): ?string {
        return $this->appliMaitre;
    }

    public function setAppliMaitre(?string $appliMaitre): self {
        $this->appliMaitre = $appliMaitre;

        return $this;
    }

    public function getCommune(): ?Commune {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self {
        $this->commune = $commune;

        return $this;
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
            $fonctionsClub->setContact($this);
        }

        return $this;
    }

    public function removeFonctionsClub(FonctionsClub $fonctionsClub): self {
        if ($this->fonctionsClub->removeElement($fonctionsClub)) {
            // set the owning side to null (unless already changed)
            if ($fonctionsClub->getContact() === $this) {
                $fonctionsClub->setContact(null);
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
            $fonctionsSection->setContact($this);
        }

        return $this;
    }

    public function removeFonctionsSection(FonctionSection $fonctionsSection): self {
        if ($this->fonctionsSection->removeElement($fonctionsSection)) {
            // set the owning side to null (unless already changed)
            if ($fonctionsSection->getContact() === $this) {
                $fonctionsSection->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupeContact>
     */
    public function getGroupes(): Collection {
        return $this->groupes;
    }

    public function Groupe(GroupeContact $groupe): self {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setContact($this);
        }

        return $this;
    }

    public function removeGroupe(GroupeContact $groupe): self {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getContact() === $this) {
                $groupe > setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Organisation>
     */
    public function getOrganisations(): Collection {
        return $this->organisations;
    }

    public function addOrganisation(Organisation $organisation): self {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations->add($organisation);
        }

        return $this;
    }

    public function removeOrganisation(Organisation $organisation): self {
        $this->organisations->removeElement($organisation);

        return $this;
    }

    /**
     * @return Collection<int, ContactTelephone>
     */
    public function getTelephones(): Collection {
        return $this->telephones;
    }

    public function addTelephone(ContactTelephone $telephone): self {
        if (!$this->telephones->contains($telephone)) {
            $this->telephones->add($telephone);
            $telephone->setContact($this);
        }

        return $this;
    }

    public function removeTelephone(ContactTelephone $telephone): self {
        if ($this->telephones->removeElement($telephone)) {
            // set the owning side to null (unless already changed)
            if ($telephone->getContact() === $this) {
                $telephone->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactTelephone>
     */
    public function getEmails(): Collection {
        return $this->emails;
    }

    public function addEmail(ContactEmail $email): self {
        if (!$this->emails->contains($email)) {
            $this->emails->add($email);
            $email->setContact($this);
        }

        return $this;
    }

    public function removeEmail(ContactEmail $email): self {
        if ($this->emails->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getContact() === $this) {
                $email->setContact(null);
            }
        }

        return $this;
    }

}
