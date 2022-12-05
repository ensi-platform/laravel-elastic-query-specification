<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuery\Contracts\MultiMatchOptions;
use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;

class MultiMatchFilterAction implements FilterAction
{
    public function __construct(private MultiMatchOptions $options)
    {
    }

    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        $fields = explode(',', $field);

        FilterValue::make($value)
            ->whenSingle(fn (mixed $value) => $query->whereMultiMatch($fields, (string)$value, $this->options))
            ->whenMultiple(fn () => throw InvalidQueryException::notSupportMultipleValues($field));
    }

    public static function encodeFields(array $fields): string
    {
        return implode(',', $fields);
    }
}
