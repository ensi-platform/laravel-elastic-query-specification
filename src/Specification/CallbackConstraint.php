<?php

namespace Ensi\LaravelElasticQuerySpecification\Specification;

use Closure;
use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Contracts\Constraint;

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
