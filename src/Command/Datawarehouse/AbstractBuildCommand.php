<?php

namespace App\Command\Datawarehouse;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Manager\Jeu\SaisonManager;
use App\Command\SaisonUtility;
use App\Exception\CommandWarningException;

abstract class AbstractBuildCommand extends Command {

    use SaisonUtility;

    public function __construct(protected EntityManagerInterface $em, protected SaisonManager $saisonManager, protected ?LoggerInterface $logger) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
                ->addArgument('debut', InputArgument::OPTIONAL, 'Saison de début')
                ->addArgument('fin', InputArgument::OPTIONAL, 'Saison de fin')
                ->addOption('courante', 'c', InputOption::VALUE_NONE, 'Calculer les stats pour saison en cours')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        try {
            if ($input->getOption('courante')) {
                $saison = $this->saisonManager->findEnCours();
                $input->setArgument('debut', $saison->getId());
                $input->setArgument('fin', $saison->getId());
            } elseif (!$input->getArgument('debut')) {
                $saison = $this->saisonManager->findEnCours();
                $argAnnee = $io->ask($this->getDefinition()->getArgument('debut')->getDescription() . ' ?', $saison->getId());
                $input->setArgument('debut', $argAnnee);
                $input->setArgument('fin', $argAnnee);
            } elseif ($input->getArgument('debut') && !$input->getArgument('fin')) {
                $input->setArgument('fin', $input->getArgument('debut'));
            }
            $debut = $this->convertirSaison($input->getArgument('debut'));
            $fin = $this->convertirSaison($input->getArgument('fin'));
            for ($i = $debut; $i <= $fin; $i++) {
                $this->buildProcess($i, $io);
            }
            return Command::SUCCESS;
        } catch (CommandWarningException $ex) {
            $io->warning($ex->getMessage());
            return Command::FAILURE;
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }
    }

    abstract protected function buildProcess(int $annee, SymfonyStyle $io): void;
}
