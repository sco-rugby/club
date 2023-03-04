<?php

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Event\ImportFichierEvent;
use App\Model\Import\ImportInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Filesystem\Filesystem;
use App\Message\DatawarehouseBuild;
use App\Manager\Jeu\SaisonManager;

final class ImportSubscriber implements EventSubscriberInterface {

    private $rows = [];

    public function __construct(private MessageBusInterface $bus) {

        return;
    }

    public static function getSubscribedEvents() {
        return [
            ImportFichierEvent::AFFILIE => [
                'buildDatawarehouse',
            ],
            ImportFichierEvent::LICENCE => [
                'buildDatawarehouse',
            ],
        ];
    }

    public function buildDatawarehouse(ImportFichierEvent $event) {
        $this->load($event->getType(), $event->getFichier());
        // liste Années
        $listeAnnees = [];
        foreach ($this->rows as $data) {
            if (in_array($data['saison'], $listeAnnees)) {
                $listeAnnees[] = $data['saison'];
            }
        }
        // Construction asynchrone du datawarehouse
        foreach ($listeAnnees as $saison) {
            $annee = SaisonManager::convertirSaison($saison);
            $this->bus->dispatch(new DatawarehouseBuild($annee));
        }
    }

    /**
     * Load file content into self::rows (array)
     * 
     * @param ImportInterface $type
     * @param \SplFileInfo $fichier
     * @return void
     * @throws InvalidParameterException
     */
    private function load(ImportInterface $type, \SplFileInfo $fichier): void {
        if (!$fichier->isReadable()) {
            throw new InvalidParameterException(sprintf('Le fichier %s ne peut pas être lu', $fichier));
        }
        // create temp File
        $tmpFile = $fichier->getPath() . DIRECTORY_SEPARATOR . uniqid() . '.' . $fichier->getExtension();
        $fs = new Filesystem();
        $fs->copy($fichier->getPathname(), $tmpFile);
        //
        $reader = IOFactory::createReaderForFile($tmpFile);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($tmpFile);
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            if ($row->isEmpty(1) || 1 == $row->getRowIndex()) {
                continue;
            }
            $cellIterator = $row->getCellIterator();
            $values = [];
            foreach ($cellIterator as $cell) {
                $values[] = $cell->getValue();
            }
            $this->rows[] = array_combine($type->getMapping(), $values);
        }
        // remove temp. file
        $fs->remove($tmpFile);
    }

}
