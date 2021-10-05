<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;

interface Aggregate
{
    public function __invoke(AggregationsBuilder $builder): void;
}