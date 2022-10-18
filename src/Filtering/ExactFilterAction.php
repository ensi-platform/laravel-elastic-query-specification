<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;

class ExactFilterAction implements FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        FilterValue::make($value)
            ->whenMultiple(fn (array $value) => $query->whereIn($field, $value))
            ->whenSingle(fn (mixed $value) => $query->where($field, $value));
    }
}
