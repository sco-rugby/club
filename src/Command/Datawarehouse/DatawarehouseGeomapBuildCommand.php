<?php

namespace App\Command\Datawarehouse;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Datawarehouse\BuildGeomap;
use Psr\Log\LoggerInterface;
use App\Command\SaisonUtility;
use App\Manager\Jeu\SaisonManager;
use App\Exception\CommandWarningException;

#[AsCommand(
            name: 'datawarehouse:geomap:build',
            description: 'Construit les tables pour la geomap dans l\'entrepôt de données',
    )]
class DatawarehouseGeomapBuildCommand extends AbstractBuildCommand {

    protected function buildProcess(int $annee, SymfonyStyle $io): void {
        $saison = $this->saisonManager->get($annee);
        //
        $io->info(sprintf('Calcul des données "geomap" pour la saison %s', $annee));
        $service = new BuildGeomap($this->em, $this->saisonManager, $io->createProgressBar(), $this->logger);
        $io->block('Initialisation du traitement');
        $service->init($saison);
        //
        $io->block(sprintf('Construction des stats %s', $annee));
        $status = $service->buildStats();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Aucune donnée "geomap" calculée pour %s', $annee));
        }
        //
        $io->block(" ");
        $io->block("Enregistrement des données");
        $status = $service->buildData();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Erreur enregistrements des données "geomap" pour %s', $annee));
        }
        //
        $service->shutdown();
        $io->block(" ");
        $io->success(sprintf('Les données "geomap" %s sont calculées', $annee));
    }

}
