<?php

namespace App\Entity\Contact;

use App\Repository\Contact\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
#[ORM\Table(schema: "club")]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?int $cal_id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $emailList = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: GroupeContact::class, orphanRemoval: true)]
    private Collection $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCalId(): ?int
    {
        return $this->cal_id;
    }

    public function setCalId(?int $cal_id): self
    {
        $this->cal_id = $cal_id;

        return $this;
    }

    public function getEmailList(): ?string
    {
        return $this->emailList;
    }

    public function setEmailList(?string $emailList): self
    {
        $this->emailList = $emailList;

        return $this;
    }

    /**
     * @return Collection<int, GroupeContact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(GroupeContact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setGroupe($this);
        }

        return $this;
    }

    public function removeContact(GroupeContact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getGroupe() === $this) {
                $contact->setGroupe(null);
            }
        }

        return $this;
    }
}
