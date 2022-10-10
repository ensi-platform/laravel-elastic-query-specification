<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Contracts\Constraint;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;

class FacetQueryProcessor implements Visitor
{
    public function __construct(private AggregationsBuilder $builder, private string $facetName)
    {
    }

    public function visitRoot(Specification $specification): void
    {
        $facets = $specification->facets()->filter(fn (AllowedFacet $facet) => $facet->isActive());
        $current = $facets->first(fn (AllowedFacet $facet) => $facet->name() === $this->facetName);

        $current?->disableFilters();

        $facets->flatMap(fn (AllowedFacet $facet) => $facet->filters())
            ->each(fn (AllowedFilter $filter) => $filter($this->builder));

        if ($current !== null) {
            $current->aggregate()($this->builder);
        }

        $current?->enableFilters();
    }

    public function visitNested(string $field, Specification $specification): void
    {
        $current = $specification->facets()
            ->filter(fn (AllowedFacet $facet) => $facet->isActive())
            ->first(fn (AllowedFacet $facet) => $facet->name() === $this->facetName);

        $current?->disableFilters();

        $this->builder->nested($field, function (AggregationsBuilder $builder) use ($specification, $current) {
            $specification->constraints()
                ->each(fn (Constraint $constraint) => $constraint($builder));

            if ($current !== null) {
                $current->aggregate()($builder);
            }
        });

        $current?->enableFilters();
    }

    public function done(): void
    {
    }
}
