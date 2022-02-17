<?php

namespace Ensi\LaravelElasticQuerySpecification\Sorting;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\Sort;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;

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
            fn (SortableQuery $nestedQuery) => $this->applyNested($nestedQuery, $order)
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
