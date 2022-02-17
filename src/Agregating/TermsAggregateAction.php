<?php

namespace Ensi\LaravelElasticQuerySpecification\Agregating;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Contracts\AggregateAction;

class TermsAggregateAction implements AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void
    {
        $builder->terms($name, $field);
    }
}
