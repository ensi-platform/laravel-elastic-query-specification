<?php

namespace Ensi\LaravelElasticQuerySpecification\Processors;

use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;

class FacetConstraintProcessor extends ConstraintProcessor
{
    public function visitRoot(Specification $specification): void
    {
        $filters = $specification->facets()
            ->filter(fn (AllowedFacet $facet) => $facet->isActive())
            ->flatMap(fn (AllowedFacet $facet) => $facet->filters())
            ->each(fn (AllowedFilter $filter) => $filter->disable());

        parent::visitRoot($specification);

        $filters->each(fn (AllowedFilter $filter) => $filter->enable());
    }

    public function visitNested(string $field, Specification $specification): void
    {
        if (!$specification->hasActiveFacet()) {
            parent::visitNested($field, $specification);
        }
    }
}
