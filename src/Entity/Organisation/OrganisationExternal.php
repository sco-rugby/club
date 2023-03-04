<?php

namespace App\Entity\Organisation;

use App\Repository\Organisation\OrganisationExternalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganisationExternalRepository::class)]
class OrganisationExternal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ApplisExternes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organisation $organisation = null;

    #[ORM\Column(length: 10)]
    private ?string $appli = null;

    #[ORM\Column(length: 10)]
    private ?string $extranalId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getAppli(): ?string
    {
        return $this->appli;
    }

    public function setAppli(string $appli): self
    {
        $this->appli = $appli;

        return $this;
    }

    public function getExtranalId(): ?string
    {
        return $this->extranalId;
    }

    public function setExtranalId(string $extranalId): self
    {
        $this->extranalId = $extranalId;

        return $this;
    }
}
