<?php

namespace App\Command\Datawarehouse;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Datawarehouse\BuildEffectif;
use App\Exception\CommandWarningException;

#[AsCommand(
            name: 'datawarehouse:effectif:build',
            description: 'Construit les tables liées aux infos des effectifs dans l\'entrepôt de données',
    )]
class DatawarehouseEffectifBuildCommand extends AbstractBuildCommand {

    protected function buildProcess(int $annee, SymfonyStyle $io): void {
        $saison = $this->saisonManager->get($annee);
        //
        $io->info(sprintf('Calcul des données "effectif" pour la saison %s', $annee));
        $service = new BuildEffectif($this->em, $this->saisonManager, $io->createProgressBar(), $this->logger);
        //
        $io->block('Initialisation du traitement');
        $service->init($saison);
        //
        $io->block(sprintf('Construction des stats %s du club', $annee));
        $status = $service->buildStatsClub();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Aucune donnée "effectif" club pour %s', $annee));
        }
        //
        $io->block(" ");
        $io->block(sprintf('Construction des stats %s par section', $annee));
        $status = $service->buildStatsSection();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Aucune donnée "effectif" par section pour %s', $annee));
        }
        //
        $io->block(" ");
        $io->block("Enregistrement des données");
        $status = $service->buildData();
        if (false === $status) {
            throw new CommandWarningException(sprintf('Erreur enregistrements des stats "effectif" pour %s', $annee));
        }
        //
        $io->block(" ");
        $io->success(sprintf('Les stats "effectif" %s sont calculées', $annee));
        $service->shutdown();
    }

}
