<?php

namespace App\Service\Import;

use App\Service\ProcessServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileImportInterface extends ProcessServiceInterface {

    public function load(UploadedFile $fichier): void;
}
