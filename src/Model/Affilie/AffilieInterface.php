<?php

namespace App\Model\Affilie;

use App\Entity\Jeu\Saison;

interface AffilieInterface extends \Stringable {

    public function getId(): ?int;

    public function setId(int $id): self;

    public function getNom(): ?string;

    public function setNom(string $nom): self;

    public function getPrenom(): ?string;

    public function setPrenom(string $prenom): self;

    public function getGenre(): ?string;

    public function getDateNaissance(): ?\DateTimeInterface;

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self;

    public function getPremiereAffiliation(): ?Saison;

    public function setPremiereAffiliation(?Saison $premiereAffiliation): self;
}
