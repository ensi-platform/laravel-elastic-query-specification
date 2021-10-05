<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;

interface FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void;
}