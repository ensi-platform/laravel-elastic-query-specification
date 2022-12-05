<?php

namespace Ensi\LaravelElasticQuerySpecification\Agregating;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Contracts\AggregateAction;

class ValueCountAggregateAction implements AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void
    {
        $builder->count($name, $field);
    }
}
