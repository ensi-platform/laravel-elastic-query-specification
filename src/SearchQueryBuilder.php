<?php

namespace Greensight\LaravelElasticQuerySpecification;

use Generator;
use Greensight\LaravelElasticQuerySpecification\Processors\ConstraintProcessor;
use Greensight\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Greensight\LaravelElasticQuerySpecification\Processors\SortProcessor;
use Greensight\LaravelElasticQuery\Search\SearchQuery;

/**
 * @mixin SearchQuery
 * @extends BaseQueryBuilder<SearchQuery>
 */
class SearchQueryBuilder extends BaseQueryBuilder
{
    protected function processors(): Generator
    {
        yield new FilterProcessor($this->parameters->filters());
        yield new ConstraintProcessor($this->query);
        yield new SortProcessor($this->query, $this->parameters->sorts());
    }
}
