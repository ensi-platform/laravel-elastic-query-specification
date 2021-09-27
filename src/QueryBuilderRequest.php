<?php

namespace Greensight\LaravelElasticQuerySpecification;

use Greensight\LaravelElasticQuerySpecification\Concerns\ExtractsQueryParameters;
use Greensight\LaravelElasticQuerySpecification\Contracts\QueryParameters;
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