<?php

namespace Ensi\LaravelElasticQuerySpecification\Specification;

use Ensi\LaravelElasticQuerySpecification\Specification\Specification;

interface Visitor
{
    public function visitRoot(Specification $specification): void;

    public function visitNested(string $field, Specification $specification): void;

    public function done(): void;
}