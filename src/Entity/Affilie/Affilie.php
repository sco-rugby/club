<?php

namespace App\Entity\Affilie;

use App\Entity\Jeu\Saison;
use App\Entity\Club\Club;
use App\Entity\Contact\Contact;
use App\Entity\Datawarehouse\Licence\Licence as Stats;
use App\Repository\Affilie\AffilieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\ManagedResource;
use App\Model\Affilie\AffilieInterface;
use App\Exception\InvalidParameterException;
use App\Entity\ImportableInterface;
use \App\Entity\ImportableTrait;
use \App\Entity\TimestampBlameableTrait;

#[ORM\Entity(repositoryClass: AffilieRepository::class)]
#[ORM\Table(schema: "club")]
class Affilie implements ManagedResource, AffilieInterface, ImportableInterface {

    use ImportableTrait,
        TimestampBlameableTrait;

    const APPLI_MAITRE = 'oval-e';

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Saison $premiereAffiliation = null;

    #[ORM\OneToOne(inversedBy: 'affilie', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\ManyToOne(inversedBy: 'affilies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Club $club = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'affilie', targetEntity: Licence::class)]
    #[ORM\OrderBy(["saison" => "ASC"])]
    private Collection $licences;

    #[ORM\OneToMany(mappedBy: 'affilie', targetEntity: AffilieSection::class)]
    #[ORM\OrderBy(["saison" => "ASC"])]
    private Collection $sections;

    #[ORM\OneToMany(mappedBy: 'affilie', targetEntity: DoubleLicence::class)]
    #[ORM\OrderBy(["saison" => "ASC"])]
    private Collection $doubleLicences;

    #[ORM\OneToMany(mappedBy: 'affilie', targetEntity: Tuteur::class, orphanRemoval: true)]
    private Collection $tuteurs;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Stats $stats = null;

    #[ORM\OneToMany(mappedBy: 'affilie', targetEntity: AffilieDiplome::class, orphanRemoval: true)]
    private Collection $diplomes;

    public function __construct() {
        $this->licences = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->doubleLicences = new ArrayCollection();
        $this->tuteurs = new ArrayCollection();
        $this->diplomes = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getContact(): ?Contact {
        return $this->contact;
    }

    public function setContact(Contact $contact): self {
        $this->contact = $contact;

        return $this;
    }

    public function getNom(): ?string {
        return $this->getContact()->getNom();
    }

    public function setNom(string $nom): self {
        $this->getContact()->setNom($nom);
    }

    public function getPrenom(): ?string {
        return $this->getContact()->getPrenom();
    }

    public function setPrenom(string $prenom): self {
        $this->getContact()->setPrenom($prenom);
    }

    public function getGenre(): ?string {
        return $this->getContact()->genre;
    }

    public function setGenre(string $genre): self {
        $this->getContact()->setGenre($genre);
        return $this;
    }

    public function getPseudo(): ?string {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPremiereAffiliation(): ?Saison {
        return $this->premiereAffiliation;
    }

    public function setPremiereAffiliation(?Saison $premiereAffiliation): self {
        $this->premiereAffiliation = $premiereAffiliation;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getClub(): ?Club {
        return $this->club;
    }

    public function setClub(?Club $club): self {
        $this->club = $club;

        return $this;
    }

    public function getAppliMaitre(): ?string {
        return self::APPLI_MAITRE;
    }

    public function setAppliMaitre(string $appli): self {
        $this->appliMaitre = self::APPLI_MAITRE;
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
            $licence->setAffilie($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): self {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getAffilie() === $this) {
                $licence->setAffilie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AffilieSection>
     */
    public function getSections(): Collection {
        return $this->sections;
    }

    public function addSection(AffilieSection $section): self {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setAffilie($this);
        }

        return $this;
    }

    public function removeSection(AffilieSection $section): self {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getAffilie() === $this) {
                $section->setAffilie(null);
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
            $doubleLicence->setAffilie($this);
        }

        return $this;
    }

    public function removeDoubleLicence(DoubleLicence $doubleLicence): self {
        if ($this->doubleLicences->removeElement($doubleLicence)) {
            // set the owning side to null (unless already changed)
            if ($doubleLicence->getAffilie() === $this) {
                $doubleLicence->setAffilie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tuteur>
     */
    public function getTuteurs(): Collection {
        return $this->tuteurs;
    }

    public function addTuteur(Tuteur $tuteur): self {
        if (!$this->tuteurs->contains($tuteur)) {
            $this->tuteurs->add($tuteur);
            $tuteur->setAffilie($this);
        }

        return $this;
    }

    public function removeTuteur(Tuteur $tuteur): self {
        if ($this->tuteurs->removeElement($tuteur)) {
            // set the owning side to null (unless already changed)
            if ($tuteur->getAffilie() === $this) {
                $tuteur->setAffilie(null);
            }
        }

        return $this;
    }

    public function hasTuteur(): bool {
        return ($this->tuteurs->count() > 0);
    }

    /**
     * @return Collection<int, AffilieDiplome>
     */
    public function getDiplomes(): Collection {
        return $this->diplomes;
    }

    public function addDiplome(AffilieDiplome $diplome): self {
        if (!$this->diplomes->contains($diplome)) {
            $this->diplomes->add($diplome);
            $diplome->setAffilie($this);
        }

        return $this;
    }

    public function removeDiplome(AffilieDiplome $diplome): self {
        if ($this->diplomes->removeElement($diplome)) {
            // set the owning side to null (unless already changed)
            if ($diplome->getAffilie() === $this) {
                $diplome->setAffilie(null);
            }
        }

        return $this;
    }

    public function getStats(): ?Licence {
        return $this->stats;
    }

    public function setStats(?Licence $stats): self {
        $this->stats = $stats;

        return $this;
    }

    public function __call($method, $arguments) {
        $reflection = new \ReflectionClass(Stats::class);
        if (!$reflection->hasMethod($method)) {
            throw new InvalidParameterException(sprintf('La méthode %s n\'existe pas dans la classe %s', $method, Stats::class));
        }
        return $this->getStats()->$method($arguments);
    }

    public function __toString() {
        return sprintf('%s %s', $this->getPrenom(), $this->getNom());
    }

}
