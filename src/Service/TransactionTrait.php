<?php

namespace App\Service;

trait TransactionTrait {

    public function initTransaction(): void {
        if ($this->hasLogger()) {
            $this->logger->debug('start transaction');
        }
        $this->em->getConnection()->setAutoCommit(false);
        $this->em->getConnection()->beginTransaction();
    }

    protected function shutDownTransaction(): void {
        if ($this->hasLogger()) {
            $this->logger->debug('Fin transaction');
        }
        if (!$this->em->isOpen()) {
            if ($this->hasLogger()) {
                $this->logger->debug('   closed');
            }
            return;
        }
        if (!$this->em->getConnection()->isConnected()) {
            if ($this->hasLogger()) {
                $this->logger->debug('   not connected');
            }
            return;
        }
        if (true === $this->rollback) {
            if ($this->hasLogger()) {
                $this->logger->debug('   rollback');
            }
            $this->em->getConnection()->rollBack();
        } else {
            if ($this->hasLogger()) {
                $this->logger->debug('   commit');
            }
            $this->em->getConnection()->commit();
        }
        //
        if ($this->hasLogger()) {
            $this->logger->debug('   close transation');
        }
        $this->em->getConnection()->close();
        //
        if ($this->hasLogger()) {
            $this->logger->debug('   flush');
        }
        $this->em->flush();
        //
        if ($this->hasLogger()) {
            $this->logger->debug('   close em');
        }
    }

    abstract protected function hasLogger(): bool;
}
