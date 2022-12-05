<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;

interface SortAction
{
    public function __invoke(SortableQuery $query, string $order, ?string $mode, string $field, ?string $missingValues): void;
}
