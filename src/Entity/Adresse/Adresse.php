<?php

namespace App\Entity\Adresse;

use Doctrine\ORM\Mapping as ORM;
use App\Exception\InvalidParameterException;
use Symfony\Component\Intl\Countries;
use Symfony\Component\String\UnicodeString;

#[ORM\Embeddable]
class Adresse {

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complement = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $pays = null;

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self {
        $this->adresse = $adresse;
        return $this;
    }

    public function getComplement(): ?string {
        return $this->complement;
    }

    public function setComplement(?string $complement): self {
        $this->complement = $complement;
        return $this;
    }

    public function getCodePostal(): ?string {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string {
        return $this->ville;
    }

    public function setVille(?string $ville): self {
        $this->ville = $ville;
        return $this;
    }

    public function getPays(): ?string {
        return $this->pays;
    }

    public function setPays(?string $pays): self {
        $pays = strtoupper($pays);
        if (!Countries::exists($pays)) {
            throw new InvalidParameterException(sprintf('Le code %s n\'est pas un code pays valide (ISO 3166-1 alpha-2)', $pays));
        }
        $this->pays = $pays;
        return $this;
    }

    /* static public function normalizeAddress(Adresse &$adresse): void {
      $elmt['adresse'] = $adresse->getAdresse();
      $elmt['complement'] = $adresse->getComplement();
      $elmt['ville'] = $adresse->getVille();
      //
      foreach ($elmt as $id => $value) {
      if (!empty($value)) {
      $elmt[$id] = (new UnicodeString($value))->trim()
      ->folded()->title(true)
      ->replace(' Le ', ' le ')
      ->replace("L'", "l'")
      ->replace("D'", "d'")
      ->replace(' De ', ' de ')
      ->replace(' En ', ' en ');
      }
      }
      //
      $adresse->setAdresse($elmt['adresse']);
      $adresse->setComplement($elmt['complement']);
      //
      if (!empty($elmt['ville'])) {
      $ville = Commune::normalizeNom($elmt['ville']);
      $adresse->setVille($ville);
      }
      } */
}
