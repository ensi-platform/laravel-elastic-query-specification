<?php

namespace Ensi\LaravelElasticQuerySpecification\Agregating;

use Ensi\LaravelElasticQuerySpecification\Contracts\AggregateAction;
use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;

class MinMaxAggregateAction implements AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void
    {
        $builder->minmax($name, $field);
    }
}