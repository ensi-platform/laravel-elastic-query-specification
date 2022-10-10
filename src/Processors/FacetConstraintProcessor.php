<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;

class FacetConstraintProcessor implements Visitor
{
    public function __construct(private BoolQuery $query)
    {
    }

    public function visitRoot(Specification $specification): void
    {
        $filters = $specification->facets()
            ->filter(fn (AllowedFacet $facet) => $facet->isActive())
            ->flatMap(fn (AllowedFacet $facet) => $facet->filters())
            ->each(fn (AllowedFilter $filter) => $filter->disable());

        $this->buildConstraints($this->query, $specification);

        $filters->each(fn (AllowedFilter $filter) => $filter->enable());
    }

    public function visitNested(string $field, Specification $specification): void
    {
        if ($specification->hasActiveFacet()) {
            return;
        }

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
