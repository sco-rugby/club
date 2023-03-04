<?php

namespace App\Command\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractDataImportCommand extends Command {

    public function __construct(protected EntityManagerInterface $em, protected ContainerBagInterface $params, protected LoggerInterface $logger, protected ValidatorInterface $validator, protected EventDispatcherInterface $dispatcher) {
        parent::__construct();
    }

    protected function configure(): void {
        $this->addArgument('fichier', InputArgument::REQUIRED, 'Fichier à importer');
    }

}
