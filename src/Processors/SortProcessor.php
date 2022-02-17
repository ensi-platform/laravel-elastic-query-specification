<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\Sort;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\NotUniqueNameException;
use Ensi\LaravelElasticQuerySpecification\Sorting\AllowedSort;
use Ensi\LaravelElasticQuerySpecification\Sorting\NestedSort;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class SortProcessor implements Visitor
{
    private Collection $requestedSorts;

    /** @var Collection|Sort[] */
    private Collection $allowedSorts;

    public function __construct(private SortableQuery $query, Collection $requestedSorts)
    {
        $this->requestedSorts = new Collection();
        $this->allowedSorts = new Collection();

        foreach ($requestedSorts as $sort) {
            [$field, $order] = AllowedSort::parseNameAndOrder($sort);

            $this->requestedSorts[$field] = $order;
        }
    }

    public function visitRoot(Specification $specification): void
    {
        $allowedSorts = $specification->sorts()->intersectByKeys($this->requestedSorts);

        foreach ($allowedSorts as $name => $allowedSort) {
            $this->addAllowedSort($name, $allowedSort);
        }
    }

    public function visitNested(string $field, Specification $specification): void
    {
        $allowedSorts = $specification->sorts()->intersectByKeys($this->requestedSorts);

        foreach ($allowedSorts as $name => $allowedSort) {
            $this->addAllowedSort($name, new NestedSort($field, $allowedSort, $specification));
        }
    }

    public function done(): void
    {
        $diff = $this->requestedSorts->keys()->diff($this->allowedSorts->keys());

        if ($diff->isNotEmpty()) {
            throw InvalidQueryException::notAllowedSorts($diff);
        }

        foreach ($this->requestedSorts as $field => $order) {
            $this->allowedSorts[$field]($this->query, $order);
        }
    }

    private function addAllowedSort(string $name, Sort $allowedSort): void
    {
        if ($this->allowedSorts->has($name)) {
            throw NotUniqueNameException::sort($name);
        }

        $this->allowedSorts[$name] = $allowedSort;
    }
}
