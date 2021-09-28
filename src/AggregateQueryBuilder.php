<?php

namespace Greensight\LaravelElasticQuerySpecification;

use Generator;
use Greensight\LaravelElasticQuerySpecification\Processors\AggregateProcessor;
use Greensight\LaravelElasticQuerySpecification\Processors\ConstraintProcessor;
use Greensight\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Greensight\LaravelElasticQuery\Aggregating\AggregationsQuery;

/**
 * @mixin AggregationsQuery
 * @extends BaseQueryBuilder<AggregationsQuery>
 */
class AggregateQueryBuilder extends BaseQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected function processors(): Generator
    {
        yield new FilterProcessor($this->parameters->filters());
        yield new ConstraintProcessor($this->query);
        yield new AggregateProcessor($this->query, $this->parameters->aggregates());
    }
}