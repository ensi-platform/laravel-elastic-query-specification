<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;

class ExistsFilterAction implements FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        $value === true
            ? $query->whereNotNull($field)
            : $query->whereNull($field);
    }
}
