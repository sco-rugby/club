<?php

namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Sonata\MediaBundle\Entity\BaseGallery;

#[ORM\Entity]
#[ORM\Table(name: "media_gallery", schema: "club")]
class Gallery extends BaseGallery {

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    protected $id;

    public function getId() {
        return $this->id;
    }

}
