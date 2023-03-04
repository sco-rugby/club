<?php

namespace App\Manager\Affilie;

use App\Manager\AbstractResourceManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\Affilie\AffilieRepository;

final class AffilieManager extends AbstractResourceManager {

    public function __construct(AffilieRepository $repository, ?EventDispatcherInterface $dispatcher = null, ?ValidatorInterface $validator = null, ?FormFactoryInterface $formFactory = null) {
        parent::__construct($repository, $dispatcher, $validator, $formFactory);
    }

    protected function dispatchException(\Exception $ex) {
        return;
    }

    protected function dispatchEvent(string $name) {
        return;
    }

}
