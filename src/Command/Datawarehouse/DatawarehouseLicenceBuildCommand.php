<?php

namespace App\Command\Datawarehouse;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Datawarehouse\BuildLicence;
use App\Exception\CommandWarningException;

#[AsCommand(
            name: 'datawarehouse:licence:build',
            description: 'Construit les tables liées aux infos des licences dans l\'entrepôt de données',
    )]
final class DatawarehouseLicenceBuildCommand extends AbstractBuildCommand {

    protected function buildProcess(int $annee, SymfonyStyle $io): void {
        $saison = $this->saisonManager->get($annee);
        //
        $io->info(sprintf('Calcul des données "licence" pour la saison %s', $annee));
        $service = new BuildLicence($this->em, $this->saisonManager, $io->createProgressBar(), $this->logger);
        //
        $io->block('Initialisation du traitement');
        $service->init($saison);
        //
        $io->block(sprintf('Construction des stats %s des licences', $annee));
        $status = $service->build();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Aucune donnée "licence" calculée pour %s', $annee));
        }
        //
        $io->block(" ");
        $io->success(sprintf('Les données "licence" %s sont calculées', $annee));
        $service->shutdown();
    }

}
