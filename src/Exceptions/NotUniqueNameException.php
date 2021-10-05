<?php

namespace Ensi\LaravelElasticQuerySpecification\Exceptions;

use Exception;

class NotUniqueNameException extends Exception
{
    public static function sort(string $name): self
    {
        return new self("Sort name \"$name\" is not unique");
    }

    public static function aggregate(string $name): self
    {
        return new self("Aggregate name \"$name\" is not unique");
    }
}