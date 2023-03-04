<?php

namespace App\Message\Handler;

use App\Message\DatawarehouseBuild;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Psr\Log\LoggerInterface;
use App\Manager\Jeu\SaisonManager;
use App\Entity\Jeu\Saison;
use App\Service\Datawarehouse\BuildDatawarehouse;

#[AsMessageHandler]
class DatawarehouseBuildHandler {

    public function __construct(private EntityManagerInterface $em, private ContainerBagInterface $params, private LoggerInterface $logger) {
        return;
    }

    public function __invoke(DatawarehouseBuild $message) {
        $saisonManager = new SaisonManager($this->em->getRepository(Saison::class), $this->params);
        $service = new BuildDatawarehouse($this->em, $saisonManager, null, $this->logger);
        $saison = $saisonManager->get($message->getAnnee());
        $service->init($saison);
        $service->build();
        $service->shutdown();
    }

}
