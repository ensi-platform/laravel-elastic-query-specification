<?php

namespace Ensi\LaravelElasticQuerySpecification\Exceptions;

use Exception;
use Illuminate\Support\Collection;

class FiltersNotFoundException extends Exception
{
    public function __construct(string $facet, Collection $filters)
    {
        $names = $filters->implode(', ');

        parent::__construct("Filters \"{$names}\" for the facet \"{$facet}\" are not found");
    }
}
