<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Manager\Jeu\SaisonManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[AsCommand(
            name: 'saison:creer',
            description: 'Créer une saison'
    )]
final class SaisonCreerCommand extends Command {

    use SaisonUtility;

    public function __construct(private SaisonManager $service) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
                ->setHelp('Cette commande permet de créer une nouvelle saison')
                ->addArgument('debut', InputArgument::OPTIONAL, 'Saison de début')
                ->addArgument('fin', InputArgument::OPTIONAL, 'Saison de fin')
                ->addOption('courante', 'c', InputOption::VALUE_NONE, 'Création automatique de la saison')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        try {
            if ($input->getOption('courante')) {
                $input->setArgument('debut', $this->service->getAnnee());
                $input->setArgument('fin', $this->service->getAnnee());
            } elseif (!$input->getArgument('debut')) {
                $argAnnee = $io->ask($this->getDefinition()->getArgument('debut')->getDescription() . ' ?', $this->service->getAnnee());
                $input->setArgument('debut', $argAnnee);
                $input->setArgument('fin', $argAnnee);
            } elseif ($input->getArgument('debut') && !$input->getArgument('fin')) {
                $input->setArgument('fin', $input->getArgument('debut'));
            }
            $debut = $this->convertirSaison($input->getArgument('debut'));
            $fin = $this->convertirSaison($input->getArgument('fin'));
            for ($i = $debut; $i <= $fin; $i++) {
                try {
                    $this->service->setAnnee($i);
                    $saison = $this->service->create([]);
                    $io->success(sprintf('Saison %s créée du %s au %s', $saison->getId(), $saison->getDebut()->format('d/m/Y'), $saison->getFin()->format('d/m/Y')));
                } catch (UniqueConstraintViolationException $ex) {
                    $io->caution(sprintf('Saison %s a déja été créée', $i));
                }
            }
            return Command::SUCCESS;
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }
    }

}
