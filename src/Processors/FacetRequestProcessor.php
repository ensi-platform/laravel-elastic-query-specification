<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuerySpecification\Agregating\AllowedAggregate;
use Ensi\LaravelElasticQuerySpecification\Exceptions\AggregateNotFoundException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\NotUniqueNameException;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Support\Collection;

class FacetRequestProcessor implements Visitor
{
    private Collection $allowedFacets;
    private Collection $requestedFacets;

    public function __construct(Collection $requestedFacets)
    {
        $this->requestedFacets = $requestedFacets->flip();
        $this->allowedFacets = new Collection();
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
        $diff = $this->requestedFacets->keys()->diff($this->allowedFacets->keys());

        if ($diff->count() > 0) {
            throw InvalidQueryException::notAllowedFacets($diff);
        }
    }

    private function processSpecification(Specification $specification): void
    {
        $aggs = $specification->aggregates()->keyBy(fn (AllowedAggregate $agg) => $agg->name());

        foreach ($specification->facets() as $name => $facet) {
            $this->addAllowedFacet($name, $facet);
            $this->attachAggregate($facet, $aggs);

            if ($this->requestedFacets->has($name)) {
                $facet->enable();
            }
        }
    }

    private function addAllowedFacet(string $name, AllowedFacet $facet): void
    {
        if ($this->allowedFacets->has($name)) {
            throw NotUniqueNameException::facet($name);
        }

        $this->allowedFacets[$name] = $facet;
    }

    private function attachAggregate(AllowedFacet $facet, Collection $aggregates): void
    {
        if ($facet->aggregate() !== null) {
            return;
        }

        $agg = $aggregates->get($facet->name());

        $agg !== null
            ? $facet->attachAggregate($agg)
            : throw new AggregateNotFoundException($facet->name());
    }
}
