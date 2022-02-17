<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;

interface AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void;
}
