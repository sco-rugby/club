<?php

namespace App\Service\Datawarehouse;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Jeu\Saison;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Console\Helper\ProgressBar;
use Psr\Log\LoggerInterface;
use App\Manager\Jeu\SaisonManager;
use App\Service\ServiceTrait;
use App\Service\TransactionTrait;

Abstract class AbstractBuildService implements DatawarehouseBuilderInterface {

    use ServiceTrait,
        TransactionTrait;

    protected ?Saison $saison;

    public function __construct(protected EntityManagerInterface $em, protected SaisonManager $saisonManager, protected ?ProgressBar $progressBar = null, protected ?LoggerInterface $logger = null) {
        return;
    }

    public function init(Saison $saison): void {
        $saisonArray = (array) $saison; // cast to array to check if empty
        if (empty($saisonArray)) {
            $this->rollback = true;
            throw new \InvalidArgumentException('La saison doit être renseignée');
        } elseIf (null === $this->saison = $this->em->getRepository(Saison::class)->find($saison->getId())) {
            $this->rollback = true;
            throw new EntityNotFoundException(sprintf('La saison %s n\'existe pas', $saison->getId()));
        }
        try {
            $this->initProcess(static::class);
            $this->initTransaction();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    public function shutdown(): void {
        // Clore le traitement
        $this->shutDownProcess();
        // commit or rollbach transaction
        $this->shutDownTransaction();
        /* if (false === $this->rollback) {
          if ($this->hasLogger()) {
          $this->logger->debug('commit');
          }
          $this->em->flush();
          $this->em->getConnection()->commit();
          } else {
          if ($this->hasLogger()) {
          $this->logger->debug('rollback');
          }
          $this->em->flush();
          $this->em->getConnection()->rollBack();
          } */
    }

}
