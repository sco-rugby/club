<?php

namespace App\Manager;

use App\Model\ManagedResource;

/**
 * @author abouet
 */
interface ManagerInterface {

    public function get($id): ManagedResource;

    public function count(): int;

    public function create($properties): ManagedResource;

    public function find();

    public function update($properties): ManagedResource;

    public function delete(): bool;

    public function getResourceName(): string;
}
