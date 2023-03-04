<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Securiry\User;

trait TimestampBlameableTrait {

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'create')]
    protected ?User $createdBy = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'update')]
    protected ?User $updatedBy = null;

    public function setCreatedAt(\DateTimeInterface $date) {
        $this->createdAt = $date;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedBy(User $user): self {
        $this->createdBy = $user;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setUpdatedAt(\DateTimeInterface $date) {
        $this->updatedAt = $date;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable {
        return $this->updatedAt;
    }

    public function setUpdatedBy(User $user) {
        $this->updatedBy = $user;

        return $this;
    }

    public function getUpdatedBy(): ?User {
        return $this->updatedBy;
    }

}
