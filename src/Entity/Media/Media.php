<?php

namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Sonata\MediaBundle\Entity\BaseMedia;

#[ORM\Entity]
#[ORM\Table(name: "media", schema: "club")]
class Media extends BaseMedia {

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    protected $id;

    public function getId() {
        return $this->id;
    }

}
