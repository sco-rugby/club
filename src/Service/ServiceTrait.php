<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

;

use App\Entity\Traitement;

trait ServiceTrait {

    protected bool $rollback = false;
    protected ?Traitement $traitement = null;
    protected ?string $filename = null;
    protected ?Filesystem $filesystem = null;

    public function initProcess(string $traitement, string $filename = null): void {
        $this->traitement = (new Traitement())
                ->setTraitement($traitement)
                ->setDebut();
        if (null !== $filename) {
            $date = new \DateTimeImmutable();
            $this->filename = sprintf('%s_%s.csv', $filename, $date->format('YmdHis'));
            $this->filesystem = new Filesystem();
        }
    }

    public function shutDownProcess(): void {
        //
        if ($this->hasLogger()) {
            $fin = new \DateTimeImmutable();
            $this->logger->debug('Fin traitement ' . $fin->format('H:i:s'));
        }
        //
        if ($this->hasProgressBar()) {
            $this->progressBar->finish();
        }
        $this->endProcess();
        $this->traitement = null;
    }

    protected function endProcess(): void {
        // Fin du traitement
        if (null === $this->traitement) {
            return;
        }//
        $this->traitement->setFin();
        if (null === $this->traitement->getStatus()) {
            $this->traitement->setStatus(Traitement::ERROR);
            $content = '';
            if (null !== $this->traitement->getMessage()) {
                $content = $this->traitement->getMessage() . "\n";
            }
            $content .= 'Fin anormale';
            $this->traitement->setMessage($content);
        }
        // Enregistrer dans transaction séparée
        $this->em->getConnection()->beginTransaction();
        try {
            $this->em->persist($this->traitement);
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function getTraitement(): ?Traitement {
        return $this->traitement;
    }

    protected function startProcess(int $steps = null): void {
        if (null === $this->traitement) {
            $this->traitement = (new Traitement())->setDebut();
        }
        $msg[] = $this->traitement->getDebut()->format('H:i:s');
        if ($this->hasProgressBar() && null !== $steps) {
            $this->progressBar->start();
            $this->progressBar->setMaxSteps($steps);
            $msg[] = sprintf('progressBar : %s steps', $steps);
        }
        if ($this->hasLogger()) {
            $this->logger->debug('Début traitement ' . implode(' ', $msg));
        }
    }

    protected function nextStep(): void {
        if ($this->hasProgressBar()) {
            $this->progressBar->advance();
        }
    }

    protected function hasProgressBar(): bool {
        return !is_null($this->progressBar);
    }

    protected function hasLogger(): bool {
        return !is_null($this->logger);
    }

    protected function setSuccess(string $message = null): void {
        $this->traitement->setStatus(Traitement::SUCCESS);
        if (null !== $message) {
            $this->traitement->setMessage($message);
        }
    }

    protected function setError(string $message): void {
        $this->rollback = true;
        if (null !== $this->traitement) {
            $this->traitement->setStatus(Traitement::ERROR);
            $this->traitement->setMessage($message);
        }
    }

    protected function setFailure(string $message): void {
        if (null === $this->traitement) {
            return;
        }
        $this->traitement->setStatus(Traitement::FAILURE);
        if (null === $this->filesystem) {
            $content = '';
            if (null !== $this->traitement->getMessage()) {
                $content = $this->traitement->getMessage() . "\n";
            }
            $content .= $message;
            $this->traitement->setMessage($content);
        } else {
            $this->traitement->setMessage(sprintf('voir %s pour plus de détails', $this->filename));
            $this->filesystem->appendToFile($this->filename, $message . "\n");
        }
    }

    public function __destruct() {
        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('%s::__destruct()', static::class));
        }
        $this->shutdown();
    }

}
