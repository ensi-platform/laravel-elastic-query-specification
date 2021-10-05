<?php

namespace Ensi\LaravelElasticQuerySpecification\Exceptions;

use Exception;

class ComponentExistsException extends Exception
{
    public static function filter(string $name): self
    {
        return new self("A filter named $name is already registered in the specification");
    }

    public static function sort(string $name): self
    {
        return new self("A sort named $name is already registered in the specification");
    }

    public static function aggregate(string $name): self
    {
        return new self("An aggregate named $name is already registered in the specification");
    }
}