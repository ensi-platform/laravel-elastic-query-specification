<?php

namespace Ensi\LaravelElasticQuerySpecification\Sorting;

use Ensi\LaravelElasticQuerySpecification\Contracts\SortAction;
use Ensi\LaravelElasticQuery\Contracts\SortableQuery;

class FieldSortAction implements SortAction
{
    public function __invoke(SortableQuery $query, string $order, ?string $mode, string $field): void
    {
        $query->sortBy($field, $order, $mode);
    }
}