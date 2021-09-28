<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\AggregationsBuilder;

interface Aggregate
{
    public function __invoke(AggregationsBuilder $builder): void;
}