<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\SortableQuery;

interface Sort
{
    public function __invoke(SortableQuery $query, ?string $order): void;
}