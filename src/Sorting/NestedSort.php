<?php

namespace Greensight\LaravelElasticQuerySpecification\Sorting;

use Greensight\LaravelElasticQuerySpecification\Contracts\Sort;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;
use Greensight\LaravelElasticQuery\Contracts\SortableQuery;

class NestedSort implements Sort
{
    public function __construct(
        private string $field,
        private Sort $allowedSort,
        private Specification $specification
    ) {
    }

    public function __invoke(SortableQuery $query, ?string $order): void
    {
        $query->sortByNested(
            $this->field,
            fn(SortableQuery $nestedQuery) => $this->applyNested($nestedQuery, $order)
        );
    }

    private function applyNested(SortableQuery $query, ?string $order): void
    {
        foreach ($this->specification->constraints() as $constraint) {
            $constraint($query);
        }

        ($this->allowedSort)($query, $order);
    }
}