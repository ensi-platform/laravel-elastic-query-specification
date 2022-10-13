<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Closure;
use Ensi\LaravelElasticQuery\Aggregating\AggregationsQuery;
use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class FacetCompositeProcessor implements Visitor
{
    private Collection $callbacks;

    public function __construct(
        private AggregationsQuery $query,
        private Collection $facets,
        private Closure $facetProcessorFactory
    ) {
        $this->callbacks = new Collection();
    }

    public function visitRoot(Specification $specification): void
    {
        if ($specification->hasActiveFacet()) {
            $this->callbacks->push(fn (Visitor $visitor) => $visitor->visitRoot($specification));
        }
    }

    public function visitNested(string $field, Specification $specification): void
    {
        if ($specification->hasActiveFacet()) {
            $this->callbacks->push(fn (Visitor $visitor) => $visitor->visitNested($field, $specification));
        }
    }

    public function done(): void
    {
        if ($this->callbacks->isEmpty()) {
            return;
        }

        foreach ($this->facets as $facet) {
            $this->query->composite(
                fn (AggregationsBuilder $builder) => $this->processFacet($facet, $builder)
            );
        }
    }

    private function processFacet(string $facet, AggregationsBuilder $builder): void
    {
        $processor = $this->createFacetProcessor($facet, $builder);

        $this->callbacks->each(fn (callable $callback) => $callback($processor));
        $processor->done();
    }

    private function createFacetProcessor(string $facet, AggregationsBuilder $builder): Visitor
    {
        return ($this->facetProcessorFactory)($facet, $builder);
    }
}
