<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;

interface Constraint
{
    public function __invoke(BoolQuery $query): void;
}
