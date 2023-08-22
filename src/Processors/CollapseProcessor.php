<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\CollapsibleQuery;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class CollapseProcessor implements Visitor
{
    private ?string $collapseField;
    private Collection $allowedCollapses;

    public function __construct(private CollapsibleQuery $query, ?string $field)
    {
        $this->collapseField = $field;
        $this->allowedCollapses = new Collection();
    }

    public function visitRoot(Specification $specification): void
    {
        $this->allowedCollapses = $specification->collapses();
    }

    public function visitNested(string $field, Specification $specification): void
    {
    }

    public function done(): void
    {
        $field = $this->collapseField;

        if ($field === null) {
            return;
        }

        $isAllowedCollapse = $this->allowedCollapses->has($field);

        if (!$isAllowedCollapse) {
            throw InvalidQueryException::notAllowedCollapse($field);
        }

        $this->allowedCollapses[$field]($this->query);
    }
}
