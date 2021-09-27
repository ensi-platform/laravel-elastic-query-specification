<?php

namespace Greensight\LaravelElasticQuerySpecification;

use Greensight\LaravelElasticQuerySpecification\Concerns\ExtractsQueryParameters;
use Greensight\LaravelElasticQuerySpecification\Contracts\QueryParameters;

class CustomParameters implements QueryParameters
{
    use ExtractsQueryParameters;

    public function __construct(protected array $source)
    {
    }

    protected function extract(string $key): mixed
    {
        return $this->source[$key] ?? null;
    }

    public static function make(array $filter = [], array|string $sort = [], array|string $aggs = []): static
    {
        $source = [
            'filter' => $filter,
            'sort' => $sort,
            'aggregate' => $aggs,
        ];

        return new static($source);
    }
}