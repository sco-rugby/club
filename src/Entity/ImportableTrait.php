<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Securiry\User;

trait ImportableTrait {

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $appliMaitre = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $importedAt = null;

    #[ORM\Column(nullable: true)]
    protected ?User $importedBy = null;

    public function getAppliMaitre(): ?string {
        return $this->appliMaitre;
    }

    public function setAppliMaitre(string $appli): self {
        $this->appliMaitre = $appli;
        return $this;
    }

    public function getImportedAt(): ?\DateTimeImmutable {
        return $this->importedAt;
    }

    public function setImportedAt(\DateTimeInterface $date): self {
        $this->importedAt = $date;
        return $this;
    }

    public function getImportedBy(): ?User {
        return $this->importedBy;
    }

    public function setImportedBy(User $user) {
        $this->importedBy = $user;

        return $this;
    }

}
