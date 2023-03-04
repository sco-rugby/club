<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class ClassExtension extends AbstractExtension {

    public function getFilters() {
        return [
            new TwigFilter('name', [$this, 'getShortName'])
        ];
    }

    public function getTest() {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceof'])
        ];
    }

    public function isInstanceof($obj, $instance): bool {
        return $obj instanceof $instance;
    }

    public function getShortName($obj): string {
        $reflectionClass = new \ReflectionClass($obj);
        return $reflectionClass->getShortName();
    }

}
