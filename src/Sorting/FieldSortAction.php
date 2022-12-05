<?php

namespace Ensi\LaravelElasticQuerySpecification\Sorting;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\SortAction;

class FieldSortAction implements SortAction
{
    public function __invoke(SortableQuery $query, string $order, ?string $mode, string $field, ?string $missingValues): void
    {
        $query->sortBy($field, $order, $mode, $missingValues);
    }
}
