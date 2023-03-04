<?php

namespace App\Manager;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\ResourceNotFoundException;
use App\Exception\InvalidParameterException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\InvalidResourceException;
use App\Exception\ResourceActionException;
use App\Model\ManagedResource;

/**
 * Description of AbstractService
 *
 * @author abouet
 */
abstract class AbstractResourceManager implements ManagerInterface {

    use ManagerTrait;

    protected $resource;

    public function __construct(protected ServiceEntityRepository $repository, protected ?EventDispatcherInterface $dispatcher = null, protected ?ValidatorInterface $validator = null, protected ?FormFactoryInterface $formFactory = null) {
        return;
    }

    public function __call($method, $arguments) {
        $class = new \ReflectionClass($this->repository);
        if ($class->hasMethod($method)) {
            $method = new \ReflectionMethod($this->repository, $method);
            return $method->invokeArgs($this->repository, $arguments);
        }
        $class = new \ReflectionClass($this->getClassName());
        if ($class->hasMethod($method)) {
            $method = new \ReflectionMethod($this->getClassName(), $method);
            return $method->invokeArgs($this->getClassName(), $arguments);
        }
        throw new InvalidParameterException(sprintf('Method "%s" cannot be called from %s.', $method, get_class($this)));
    }

    public function createObject(): ManagedResource {
        $class = $this->getClassName();
        $this->setResource(new $class());
        return $this->getResource();
    }

    public function createForm($data = null, array $options = []): FormInterface {
        if (null === $this->formFactory) {
            throw new ResourceActionException('No FormFactoryInterface set. Form creation not possible');
        }
        if (is_null($data)) {
            $data = $this->createObject();
        }
        return $this->formFactory->create($this->getFormType(), $data, $options);
    }

    public function createFilter(array $options = []): FormInterface {
        $filterType = str_replace('Form', 'Filter', $this->getFormType());
        return $this->formFactory->create($filterType, null, $options);
    }

    public function setResource(ManagedResource $resource) {
        $this->resource = $resource;
    }

    public function &getResource(): ?ManagedResource {
        return $this->resource;
    }

    public function isValid(): bool {
        $class = $this->getClassName();
        return ($this->getResource() instanceof $class);
    }

    public function getClassName(): string {
        return $this->repository->getClassName();
    }

    public function get($id): ManagedResource {
        $obj = $this->retrieve($id);
        if (!$obj) {
            throw new ResourceNotFoundException(sprintf("%s '%s' not found", $this->getResourceName(), $id));
        }
        $this->setResource($obj);
        return $obj;
    }

    public function find() {
        return $this->repository->findAll();
    }

    public function create($properties): ManagedResource {
        $this->checkForAction('create');
        try {
            $class = $this->getClassName();
            if (!($properties instanceof $class)) {
                $this->createObject();
                $this->populate($properties);
            } else {
                $this->setResource($properties);
            }
            $this->repository->save($this->getResource(), true);
            $this->dispatchEvent(strTolower(sprintf('club.%s.create', $this->getResourceName())));
            return $this->getResource();
        } catch (\Exeption $e) {
            $this->dispatchException($e);
            throw $e;
        }
    }

    public function update($properties = null): ManagedResource {
        $this->checkForAction('update');
        try {
            $this->populate($properties);
            try {
                $this->repository->save($this->getResource(), true);
                $this->dispatchEvent(strTolower(sprintf('club.%s.update', $this->getResourceName())));
                return $this->getResource();
            } catch (UniqueConstraintViolationException $ex) {
                throw new UniqueConstraintViolationException(sprintf("%s '%s' already exists", $this->getResourceName(), $doc->getId()));
            } Catch (\Exception $ex) {
                throw new \LogicException($ex->getMessage());
            }
        } catch (\Exeption $e) {
            $this->dispatchException($e);
            throw $e;
        }
    }

    protected function populate($properties): void {
        if ($properties instanceof Request) {
            $valid = $this->processRequest($properties);
        } elseif ($properties instanceof FormInterface) {
            if ($properties->isSubmitted() && $properties->isValid()) {
                $this->setResource($properties->getData());
            }
            $valid = $properties->isValid();
        } else {
            $valid = $this->validate();
        }
        if (true !== $valid) {
            throw new InvalidResourceException();
        }
    }

    public function delete(): bool {
        $this->checkForAction('delete');
        try {
            $this->repository->remove($this->getResource(), true);
            $this->dispatchEvent(strTolower(sprintf('club.%s.delete', $this->getResourceName())));
            return true;
        } catch (\Exeption $e) {
            $this->dispatchException($e);
            throw $e;
        }
    }

    public function count($filters = null): int {
        if (!is_null($filters)) {
            $criteria = $filters;
        } else {
            $criteria = $this->filters['where'];
        }
        return $this->repository->count($criteria);
    }

    /**
     * find an occurence
     * used by self::get($id) | self::delete($id)
     * 
     * @param mixed $id
     * @return ManagedResource
     */
    protected function retrieve($ref): ?ManagedResource {
        $class = $this->getClassName();
        if ($ref instanceof $class) {
            $id = $ref->getId();
        } elseIf (is_object($ref)) {
            throw new InvalidParameterException(sprintf('The "%s()" method expects the argument to be an id or %s resource, "%s" given.', __METHOD__, $class, \gettype($ref)));
        } else {
            $id = $ref;
        }
        return $this->repository->find($id);
    }

    protected function processRequest(Request $request = null) {
        $form = $this->createForm($this->getResource());
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                return true;
            } elseif (!$form->isValid()) {
                return $this->getErrors(true);
            }
        }
        return false;
    }

    protected function getFormType(): string {
        $reflectionClass = new \ReflectionClass(static::class);
        return str_replace('Manager', 'Form', $reflectionClass->getNamespaceName()) . '\\' . str_replace('Manager', 'Type', $reflectionClass->getShortName());
    }

    protected function validate() {
        $this->checkForAction('validate');
        $errors = $this->validator->validate($this->getResource());
        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }

    protected function checkForAction(string $action): bool {
        if (!$this->isValid()) {
            throw new ResourceActionException(sprintf("No %s has been set for %s.", $this->getResourceName(), $action));
        }
        switch ($action) {
            case 'delete':
            case 'update':
            case 'create':
                if (null === $this->dispatcher) {
                    throw new ResourceActionException(sprintf("No EventDispatcherInterface. Not possible to %s .", $action));
                }
                break;
            case 'validate':
                if (null === $this->validator) {
                    throw new ResourceActionException(sprintf("No ValidatorInterface. Not possible to validate."));
                }
                break;
        }
        return true;
    }

    protected abstract function dispatchException(\Exception $ex);

    protected abstract function dispatchEvent(string $name);
}
