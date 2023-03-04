<?php

namespace App\Repository;

use App\Entity\Club\Section;

interface SectionRepositoryInterface {

    public function findBySection(Section $section): array;
}
