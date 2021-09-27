<?php

namespace Greensight\LaravelElasticQuerySpecification\Filtering;

use Greensight\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Greensight\LaravelElasticQuery\Contracts\BoolQuery;

class ExistsFilterAction implements FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        $value === true
            ? $query->whereNotNull($field)
            : $query->whereNull($field);
    }
}
