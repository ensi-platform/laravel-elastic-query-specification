<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;

interface Sort
{
    public function __invoke(SortableQuery $query, ?string $order): void;
}