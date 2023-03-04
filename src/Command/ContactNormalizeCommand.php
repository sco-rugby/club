<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Manager\Contact\ContactManager;

#[AsCommand(
            name: 'contact:normalize',
            description: 'Add a short description for your command',
    )]
final class ContactNormalizeCommand extends Command {

    public function __construct(private ContactManager $manager) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
                ->addArgument('contact', InputArgument::OPTIONAL, 'Id contact')
                ->addOption('commune', 'c', InputOption::VALUE_NONE, 'Sélectionner les contacts sans commune')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $contactId = $input->getArgument('contact');

        if ($contactId) {
            $contacts[] = $this->manager->get($contactId);
        } else {
            $contacts = $this->manager->find();
        }

        if ($input->getOption('commune')) {
            // ...
        }
        if (0 == count($contacts)) {
            $io->warning('Aucun contact à normaliser');
        }
        foreach ($contacts as $contact) {
            $communeIid = '';
            $io->block(sprintf('Normalisation de %s %s', $contact->getPrenom(), $contact->getNom()));
            $nom = $this->manager->normalizeNom($contact->getNom());
            $prenom = $this->manager->normalizePrenom($contact->getPrenom());
            $io->writeln(sprintf('   => %s %s', $prenom, $nom));
            $contact->setNom($nom);
            $contact->setPrenom($prenom);
            $this->manager->setResource($contact);
            $ville = $contact->getAdresse()->getVille();
            $this->manager->setAddress($contact->getAdresse());
            if ($contact->getCommune()) {
                $communeIid = $contact->getCommune()->getId();
            }
            $io->writeln(sprintf('   adresse : %s => %s (%s)', $ville, $contact->getAdresse()->getVille(), $communeIid));
            $this->manager->update();
        }
        $io->success(count($contacts) . ' contacts normalisés');

        return Command::SUCCESS;
    }

}
