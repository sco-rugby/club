<?php

namespace App\Entity\Datawarehouse\Licence;

use App\Repository\Datawarehouse\Licence\LicenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Jeu\Saison;
use App\Model\Affilie\AffilieInterface;

#[ORM\Entity(repositoryClass: LicenceRepository::class)]
#[ORM\Table(schema: "datawarehouse")]
class Licence implements AffilieInterface {

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Saison $premiereAffiliation = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbSaisons = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ageDebut = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ageFin = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $age_actuel = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saisonDebut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Saison $saisonFin = null;

    #[ORM\OneToMany(mappedBy: 'licence', targetEntity: LicenceSaison::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(["saison" => "ASC"])]
    private Collection $saisons;

    public function __construct() {
        $this->saisons = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getGenre(): ?string {
        return match (substr($this->getId(), 5, 1)) {
            '1' => 'M',
            '2' => 'F'
        };
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

    public function getDateNaissance(): ?\DateTimeInterface {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPremiereAffiliation(): ?Saison {
        return $this->premiereAffiliation;
    }

    public function setPremiereAffiliation(?Saison $premiereAffiliation): self {
        $this->premiereAffiliation = $premiereAffiliation;

        return $this;
    }

    public function getNombreSaisons(): ?int {
        return $this->nbSaisons;
    }

    public function setNombreSaisons(int $nbSaisons): self {
        $this->nbSaisons = $nbSaisons;

        return $this;
    }

    public function getAgeDebut(): ?int {
        return $this->ageDebut;
    }

    public function setAgeDebut(int $ageDebut): self {
        $this->ageDebut = $ageDebut;

        return $this;
    }

    public function getAgeFin(): ?int {
        return $this->ageFin;
    }

    public function setAgeFin(?int $ageFin): self {
        $this->ageFin = $ageFin;

        return $this;
    }

    public function getSaisonDebut(): ?Saison {
        return $this->saisonDebut;
    }

    public function setSaisonDebut(Saison $saisonDebut): self {
        $this->saisonDebut = $saisonDebut;

        return $this;
    }

    public function getSaisonFin(): ?Saison {
        return $this->saisonFin;
    }

    public function setSaisonFin(?Saison $saisonFin): self {
        $this->saisonFin = $saisonFin;

        return $this;
    }

    /**
     * @return Collection<int, LicenceSaison>
     */
    public function getSaisons(): Collection {
        return $this->saisons;
    }

    public function addSaison(LicenceSaison $saison): self {
        if (!$this->saisons->contains($saison)) {
            $this->saisons->add($saison);
            $saison->setLicence($this);
        }

        return $this;
    }

    public function removeSaison(LicenceSaison $saison): self {
        if ($this->saisons->removeElement($saison)) {
            // set the owning side to null (unless already changed)
            if ($saison->getLicence() === $this) {
                $saison->setLicence(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return sprintf('%s %s', $this->getPrenom(), $this->getNom());
    }

    public function getAgeActuel(): ?int {
        return $this->age_actuel;
    }

    public function setAgeActuel(int $age_actuel): self {
        $this->age_actuel = $age_actuel;

        return $this;
    }

}
