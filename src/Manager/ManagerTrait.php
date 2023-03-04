<?php

namespace App\Manager;

/**
 * Description of ManagerTrait
 */
trait ManagerTrait {

    public function getResourceName(): string {
        if (method_exists($this, 'getClassName')) {
            $reflectionClass = new \ReflectionClass($this->getClassName());
            return $reflectionClass->getShortName();
        } else {
            $reflectionClass = new \ReflectionClass($this);
            return ucfirst(str_replace('manager', '', strtolower($reflectionClass->getShortName())));
        }
    }

    /**
     * Validate a fields list
     * 
     * @param array $fields liste des champs
     * @return bool true if ok, exception thrown otherwise
     * @throws \Exception
     */
    public function validateFields(array $fields): bool {
        $reflectionClass = new \ReflectionClass($this->getClassName());
        forEach ($fields as $field) {
            // Method names are case insensitive
            // http://php.net/manual/en/reflectionclass.hasmethod.php
            if (!$reflectionClass->hasProperty($field)) {
                throw new \Exception(sprintf("invalid field '%s' for the ressource %s", $field, $this->getResourceName()));
            }
        }
        return true;
    }

}
