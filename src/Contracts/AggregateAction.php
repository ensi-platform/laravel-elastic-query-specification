<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\AggregationsBuilder;

interface AggregateAction
{
    public function __invoke(AggregationsBuilder $builder, string $name, string $field): void;
}