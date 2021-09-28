<?php

namespace Greensight\LaravelElasticQuerySpecification\Processors;

use Greensight\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;
use Greensight\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class FilterProcessor implements Visitor
{
    private Collection $allowedFilters;

    public function __construct(private Collection $values)
    {
        $this->allowedFilters = new Collection();
    }

    public function visitRoot(Specification $specification): void
    {
        $this->processSpecification($specification);
    }

    public function visitNested(string $field, Specification $specification): void
    {
        $this->processSpecification($specification);
    }

    public function done(): void
    {
        $diff = $this->values->keys()->diff($this->allowedFilters->keys());

        if ($diff->count() > 0) {
            throw InvalidQueryException::notAllowedFilters($diff);
        }
    }

    private function processSpecification(Specification $specification): void
    {
        foreach ($specification->filters() as $name => $filter) {
            $this->allowedFilters[$name] = true;

            if ($this->values->has($name)) {
                $filter->setValue($this->values->get($name));
            }
        }
    }
}