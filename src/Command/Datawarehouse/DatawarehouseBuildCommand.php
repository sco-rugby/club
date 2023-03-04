<?php

namespace App\Command\Datawarehouse;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'datawarehouse:build',
    description: 'Construit l\'entrepôt de données',
)]
class DatawarehouseBuildCommand extends AbstractBuildCommand {

    protected function buildProcess(int $annee, SymfonyStyle $io): void {
        $saison = $this->saisonManager->get($annee);
        $io->info(sprintf('Calcul des données "licence" pour la saison %s', $annee));
        $service = new BuildLicence($this->em, $this->saisonManager, $io->createProgressBar(), $this->logger);
        $io->block('Initialisation du traitement');
        $service->init($saison);
        if ($service->build()) {
            $io->block(' ');
            $io->success(sprintf('Les données "licence" %s sont calculées', $annee));
        } else {
            $io->block(' ');
            $io->warning(sprintf('Aucune donnée "licence" calculée pour %s', $annee));
        }
    }
}
