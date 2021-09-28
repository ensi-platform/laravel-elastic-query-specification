<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\BoolQuery;

interface FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void;
}