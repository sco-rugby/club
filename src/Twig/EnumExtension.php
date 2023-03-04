<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension {

    public function getFunctions() {
        return [
            new TwigFunction('enum', [$this, 'enum'])
        ];
    }

    public function enum(string $enumFQN): object {

        return new class($enumFQN) {

            public function __construct(private readonly string $enum) {
                
            }

            public function __call(string $name, array $arguments) {
                $enumFQN = sprintf('%s::%s', $this->enum, $name);
                
                if (defined($enumFQN)) {
                    return constant($enumFQN);
                }

                if (method_exists($this->enum, $name)) {
                    return $this->enum::$name(...$arguments);
                }

                throw new \BadMethodCallException("Neither \"{$enumFQN}\" or \"{$enumFQN}::{$name}()\" exist in this runtime.");
            }
        };
    }

}
