<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\BoolQuery;

interface Constraint
{
    public function __invoke(BoolQuery $query): void;
}
