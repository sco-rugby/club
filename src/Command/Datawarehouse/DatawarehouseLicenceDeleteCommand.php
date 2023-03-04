<?php

namespace App\Command\Datawarehouse;

use App\Entity\Jeu\Saison;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Command\SaisonUtility;

#[AsCommand(
            name: 'datawarehouse:licence:delete',
            description: 'Supprimes les données "licences" de l\'entrepôt de données',
    )]
class DatawarehouseLicenceDeleteCommand extends Command {

    use SaisonUtility;

    public function __construct(protected EntityManagerInterface $em) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
                ->addArgument('saison', InputArgument::REQUIRED, 'Année de la saison');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $annee = $this->convertirSaison($input->getArgument('saison'));
        try {
            $saison = new Saison($annee);
            $status = $this->em->getRepository(LicenceSaison::class)->deleteSaison($saison, true);
            $io->success(sprintf('%s licences supprimées', $status));
            return Command::SUCCESS;
        } catch (Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }
    }

}
