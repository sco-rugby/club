<?php

namespace App\Provider\Media;

use Sonata\MediaBundle\Provider\FileProvider as BaseFileProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Validator\ErrorElement;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SVGProvider extends BaseFileProvider {

    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, protected array $allowedExtensions = array(), protected array $allowedMimeTypes = array(), protected ?MetadataBuilderInterface $metadata = null) {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);
    }

    public function buildCreateForm(FormMapper $formMapper): void {
        $formMapper->add('binaryContent', 'file', array(
            'label' => 'Upload SVG file only',
            'constraints' => array(
                new NotBlank(),
                new NotNull(),
            ),
        ));
    }

    public function buildEditForm(FormMapper $form): void {
        $form->add('name');
        $form->add('enabled');
        $form->add('authorName');
        $form->add('cdnIsFlushable');
        $form->add('description');
        $form->add('copyright');
        $form->add('binaryContent', [], ['type' => 'string']);
    }

    private function getMetadata(MediaInterface $media) {
        if (!$media->getBinaryContent()) {
            return;
        }

        $url = sprintf('https://vimeo.com/api/oembed.json?url=http://vimeo.com/%s', $media->getBinaryContent());
        $metadata = @file_get_contents($url);

        if (!$metadata) {
            throw new \RuntimeException(sprintf('Unable to retrieve vimeo video information for: %s', $url));
        }

        $metadata = json_decode($metadata, true);

        if (!$metadata) {
            throw new \RuntimeException(sprintf('Unable to decode vimeo video information for: %s', $url));
        }

        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, MediaInterface $media): void {

        if (!$media->getBinaryContent() instanceof \SplFileInfo) {
            return;
        }

        if ($media->getBinaryContent() instanceof UploadedFile) {
            $fileName = $media->getBinaryContent()->getClientOriginalName();
        } elseif ($media->getBinaryContent() instanceof File) {
            $fileName = $media->getBinaryContent()->getFilename();
        } else {
            throw new \RuntimeException(sprintf('Invalid binary content type: %s', get_class($media->getBinaryContent())));
        }

        if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->allowedExtensions)) {
            $errorElement
                    ->with('binaryContent')
                    ->addViolation('Invalid extensions')
                    ->end();
        }

        if (!in_array($media->getBinaryContent()->getMimeType(), $this->allowedMimeTypes)) {
            $errorElement
                    ->with('binaryContent')
                    ->addViolation('Invalid mime type : ' . $media->getBinaryContent()->getMimeType())
                    ->end();
        }
    }

}
