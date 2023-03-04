<?php

namespace App\Entity;

interface ImportableInterface {

    public function getAppliMaitre(): ?string;

    public function getImportedAt(): ?\DateTimeInterface;

    public function setImportedAt(\DateTimeInterface $date): self;
}
