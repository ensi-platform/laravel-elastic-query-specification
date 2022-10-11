<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\Constraint;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class FacetQueryProcessor implements Visitor
{
    public function __construct(private AggregationsBuilder $builder, private string $facetName)
    {
    }

    public function visitRoot(Specification $specification): void
    {
        $facets = $specification->activeFacets();
        $constraints = $facets->flatMap(fn (AllowedFacet $facet) => $facet->filters());

        if ($current = $this->findCurrentFacet($facets)) {
            $this->processFacet($current, $this->builder, $constraints);
        } else {
            $this->applyConstraints($this->builder, $constraints);
        }
    }

    public function visitNested(string $field, Specification $specification): void
    {
        $facets = $specification->activeFacets();
        $constraints = $specification->constraints();

        if ($current = $this->findCurrentFacet($facets)) {
            $this->builder->nested(
                $field,
                fn (AggregationsBuilder $builder) => $this->processFacet($current, $builder, $constraints)
            );
        } else {
            $this->builder->whereHas(
                $field,
                fn (BoolQuery $query) => $this->applyConstraints($query, $constraints)
            );
        }
    }

    public function done(): void
    {
    }

    private function findCurrentFacet(Collection $facets): ?AllowedFacet
    {
        return $facets->first(fn (AllowedFacet $facet) => $facet->name() === $this->facetName);
    }

    private function processFacet(AllowedFacet $facet, AggregationsBuilder $builder, Collection $constraints): void
    {
        $facet->disableFilters();

        $this->applyConstraints($builder, $constraints);

        $facet->aggregate()($builder);
        $facet->enableFilters();
    }

    private function applyConstraints(BoolQuery $query, Collection $constraints): void
    {
        $constraints->each(fn (Constraint $constraint) => $constraint($query));
    }
}
