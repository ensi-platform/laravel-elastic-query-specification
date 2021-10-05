<?php

namespace Ensi\LaravelElasticQuerySpecification;

use Generator;
use Ensi\LaravelElasticQuerySpecification\Processors\ConstraintProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\SortProcessor;
use Ensi\LaravelElasticQuery\Search\SearchQuery;

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
