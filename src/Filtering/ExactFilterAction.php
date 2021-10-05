<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Ensi\LaravelElasticQuery\Contracts\BoolQuery;

class ExactFilterAction implements FilterAction
{
    public function __invoke(BoolQuery $query, mixed $value, string $field): void
    {
        $value = $this->normalizeValue($value);
        if ($value === null) {
            return;
        }

        is_array($value)
            ? $query->whereIn($field, $value)
            : $query->where($field, $value);
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $normalized = array_filter($value);
        if (!$normalized) {
            return null;
        }

        return count($normalized) === 1 ? head($normalized) : array_values($normalized);
    }
}
