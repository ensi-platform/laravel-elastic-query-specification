<?php

namespace Ensi\LaravelElasticQuerySpecification;

use Ensi\LaravelElasticQuerySpecification\Concerns\ExtractsQueryParameters;
use Ensi\LaravelElasticQuerySpecification\Contracts\QueryParameters;
use Illuminate\Http\Request;

class QueryBuilderRequest extends Request implements QueryParameters
{
    use ExtractsQueryParameters;

    public static function fromRequest(Request $request): static
    {
        return static::createFrom($request, new static());
    }

    protected function extract(string $key): mixed
    {
        return $this->input($key);
    }
}
