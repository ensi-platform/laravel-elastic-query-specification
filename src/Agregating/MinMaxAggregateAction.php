<?php

namespace Greensight\LaravelElasticQuerySpecification\Agregating;

use Greensight\LaravelElasticQuerySpecification\Contracts\AggregateAction;
use Greensight\LaravelElasticQuery\Contracts\AggregationsBuilder;

class MinMaxAggregateAction implements AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void
    {
        $builder->minmax($name, $field);
    }
}