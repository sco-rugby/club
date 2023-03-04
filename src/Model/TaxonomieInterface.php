<?php

namespace App\Model;

interface TaxonomieInterface {

    public function getId(): ?string;

    public function setId(string $id): self;

    public function getLibelle(): ?string;

    public function setLibelle(string $libelle): self;

    public function getSlug(): ?string;

    public function setSlug(string $slug): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;
}
