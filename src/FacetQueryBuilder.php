<?php

namespace Ensi\LaravelElasticQuerySpecification;

use Ensi\LaravelElasticQuerySpecification\Processors\FacetRequestProcessor;
use Ensi\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Generator;

class FacetQueryBuilder extends BaseQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected function processors(): Generator
    {
        yield new FilterProcessor($this->parameters->filters());
        yield new FacetRequestProcessor($this->parameters->facets());
    }
}
