<?php

namespace Ensi\LaravelElasticQuerySpecification;

use Ensi\LaravelElasticQuery\Aggregating\AggregationsQuery;
use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetCompositeProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetConstraintProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetQueryProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetRequestProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Generator;

/**
 * @mixin AggregationsQuery
 * @extends BaseQueryBuilder<AggregationsQuery>
 */
class FacetQueryBuilder extends BaseQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected function processors(): Generator
    {
        yield new FilterProcessor($this->parameters->filters());
        yield new FacetRequestProcessor($this->parameters->facets());
        yield new FacetConstraintProcessor($this->query);
        yield new FacetCompositeProcessor(
            $this->query,
            $this->parameters->facets(),
            fn (string $facet, AggregationsBuilder $builder) => new FacetQueryProcessor($builder, $facet)
        );
    }
}
