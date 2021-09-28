<?php

namespace Greensight\LaravelElasticQuerySpecification\Specification;

use Closure;
use Greensight\LaravelElasticQuerySpecification\Contracts\Constraint;
use Greensight\LaravelElasticQuery\Contracts\BoolQuery;

final class CallbackConstraint implements Constraint
{
    public function __construct(private Closure $callback)
    {
    }

    public function __invoke(BoolQuery $query): void
    {
        ($this->callback)($query);
    }
}