<?php

namespace Ensi\LaravelElasticQuerySpecification\Exceptions;

use Exception;

class AggregateNotFoundException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("The aggregate for the facet named \"{$name}\" is not registered in the specification");
    }
}
