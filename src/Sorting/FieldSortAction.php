<?php

namespace Greensight\LaravelElasticQuerySpecification\Sorting;

use Greensight\LaravelElasticQuerySpecification\Contracts\SortAction;
use Greensight\LaravelElasticQuery\Contracts\SortableQuery;

class FieldSortAction implements SortAction
{
    public function __invoke(SortableQuery $query, string $order, ?string $mode, string $field): void
    {
        $query->sortBy($field, $order, $mode);
    }
}