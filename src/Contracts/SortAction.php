<?php

namespace Greensight\LaravelElasticQuerySpecification\Contracts;

use Greensight\LaravelElasticQuery\Contracts\SortableQuery;

interface SortAction
{
    public function __invoke(SortableQuery $query, string $order, ?string $mode, string $field): void;
}