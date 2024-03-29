<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuery\Contracts\MatchOptions;
use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;

class MatchFilterAction implements FilterAction
{
    public function __construct(private MatchOptions $options)
    {
    }

    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        FilterValue::make($value)
            ->whenSingle(fn (mixed $value) => $query->whereMatch($field, (string)$value, $this->options))
            ->whenMultiple(fn () => throw InvalidQueryException::notSupportMultipleValues($field));
    }
}
