<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;

class ConstraintProcessor implements Visitor
{
    public function __construct(private BoolQuery $query)
    {
    }

    public function visitRoot(Specification $specification): void
    {
        $this->buildConstraints($this->query, $specification);
    }

    public function visitNested(string $field, Specification $specification): void
    {
        if (!$specification->hasActiveFilter()) {
            return;
        }

        $this->query->whereHas(
            $field,
            fn (BoolQuery $query) => $this->buildConstraints($query, $specification)
        );
    }

    public function done(): void
    {
    }

    private function buildConstraints(BoolQuery $query, Specification $specification): void
    {
        foreach ($specification->constraints() as $constraint) {
            $constraint($query);
        }
    }
}
