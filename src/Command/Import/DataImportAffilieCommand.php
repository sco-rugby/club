<?php

namespace App\Command\Import;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Model\RapportOvale;
use App\Service\Import\ImportAffilie;

#[AsCommand(
            name: 'data:affilie:import',
            description: 'Importer la liste des affiliés d\'un fichier oval-e',
    )]
class DataImportAffilieCommand extends Command {

    protected function configure(): void {
        parent::configure();
        $this->setHelp(str_replace('<br />', "\n", RapportOvale::OVALE2004->getDescription()));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $fichier = $input->getArgument('fichier');
        //
        $io->info(sprintf('Import des affiliés du fichier %s', $fichier));
        $service = new ImportAffilie($this->em, $this->dispatcher, $this->validator, $this->params, $io->createProgressBar(), $this->logger);
        try {
            //
            $io->block('Initialisation de l\'import');
            $service->init();
            //
            $io->block('Chargement de ' . $fichier);
            $service->load(new \SplFileInfo($fichier));
            //
            $io->block('Import du fichier');
            $status = $service->import();
            //
            $io->block(" ");
            $traitement = $service->getTraitement();
            if (true === $status) {
                $io->success(sprintf('%s importée : %s (%s-%s)', $traitement->getDescription(), $traitement->getMessage(), $traitement->getDebut()->format('H:i:s'), $traitement->getFin()->format('H:i:s')));
            } else {
                $io->warning('Des erreurs sont survenues lors de l\'import. ' . $traitement->getMessage());
            }
            $result = Command::SUCCESS;
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            $result = Command::FAILURE;
        } finally {
            $service->shutdown();
        }
        return $result;
    }

}
