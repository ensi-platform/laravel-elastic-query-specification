<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Ensi\LaravelElasticQuery\Contracts\BoolQuery;

class ExistsFilterAction implements FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        $value === true
            ? $query->whereNotNull($field)
            : $query->whereNull($field);
    }
}
