<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Ensi\LaravelElasticQuery\Contracts\CollapsibleQuery;

interface Collapse
{
    public function __invoke(CollapsibleQuery $query): void;
}
