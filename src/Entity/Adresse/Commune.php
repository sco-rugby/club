<?php

namespace App\Entity\Adresse;

use App\Repository\Adresse\CommuneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\UnicodeString;

#[ORM\Entity(repositoryClass: CommuneRepository::class, readOnly: true)]
#[ORM\Table(schema: "club")]
class Commune implements \Stringable {

    #[ORM\Id]
    #[ORM\Column(length: 5)]
    private ?string $id = null;

    #[ORM\Column(length: 4)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'communesAssociees')]
    private ?self $parent = null;

    #[ORM\Column(length: 100)]
    private ?string $canonizedNom = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 13, scale: 10, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 13, scale: 10, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 5)]
    private ?string $codeINSEE = null;

    #[ORM\Column]
    private ?bool $regroupement = false;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $communesAssociees;

    #[ORM\OneToMany(mappedBy: 'commune', targetEntity: CodePostal::class)]
    private Collection $codePostaux;

    public function __construct() {
        $this->communesAssociees = new ArrayCollection();
        $this->codePostaux = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getCanonizedNom(): ?string {
        return $this->canonizedNom;
    }

    public function setCanonizedNom(string $nom): self {
        $this->canonizedNom = $nom;

        return $this;
    }

    static public function canonizeNom(string $nom): string {
        /* $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
          'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
          'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
          'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
          'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
          'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
          'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
          'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
          'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť',
          'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ',
          'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
          'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
          $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
          'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
          'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
          'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
          'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
          'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
          'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
          'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
          's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
          'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
          'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
          $nom = str_replace($a, $b, $nom); */
        return (string) (new UnicodeString(self::normalizeNom($nom)))
                        ->folded()
                        ->upper()
                        ->replaceMatches('/SAINT-/', 'ST ')
                        ->replaceMatches('/SAINTE-/', 'STE ');
    }

    static public function normalizeNom(string $nom): string {
        return (string) (new UnicodeString($nom))
                        ->trim()
                        ->title(true)
                        ->replace('S/', 'sur ')
                        ->replace('Sur ', 'sur ')
                        ->replace(' Le ', ' le ')
                        ->replace(' La ', ' la ')
                        ->replace(' Les ', ' les ')
                        ->replace("L'", "l'")
                        ->replace("D'", "d'")
                        ->replace(' De ', ' de ')
                        ->replace(' En ', ' en ')
                        ->replaceMatches('/(St[\s|\-|\.]\s*)|(Saint[\s|\-|\.]\s*)/', 'Saint-')
                        ->replaceMatches('/(Ste[\s|\-|\.]\s*)|(Sainte[\s|\-|\.]\s*)/', 'Sainte-');
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;
        $this->canonizedNom = $this->canonizeNom($nom);
        return $this;
    }

    public function getLatitude(): ?string {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCodeINSEE(): ?string {
        return $this->codeINSEE;
    }

    public function setCodeINSEE(string $codeINSEE): self {
        $this->codeINSEE = $codeINSEE;

        return $this;
    }

    public function isRegroupement(): ?bool {
        return $this->regroupement;
    }

    public function setRegroupement(bool $regroupement): self {
        $this->regroupement = $regroupement;

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
    public function getCommunesAssociees(): Collection {
        return $this->communesAssociees;
    }

    public function addCommuneAssociee(self $commune): self {
        if (!$this->communesAssociees->contains($commune)) {
            $this->communesAssociees->add($commune);
            $commune->setParent($this);
        }

        return $this;
    }

    public function removeCommunesAssociee(self $commune): self {
        if ($this->communesAssociees->removeElement($commune)) {
            // set the owning side to null (unless already changed)
            if ($commune->getParent() === $this) {
                $commune->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CodePostal>
     */
    public function getCodePostaux(): Collection {
        return $this->codePostaux;
    }

    public function addCodePostaux(CodePostal $codePostaux): self {
        if (!$this->codePostaux->contains($codePostaux)) {
            $this->codePostaux->add($codePostaux);
            $codePostaux->setCommune($this);
        }

        return $this;
    }

    public function removeCodePostaux(CodePostal $codePostal): self {
        if ($this->codePostaux->removeElement($codePostal)) {
            // set the owning side to null (unless already changed)
            if ($codePostal->getCommune() === $this) {
                $codePostal->setCommune(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
        return (string) $this->getNom();
    }

}
