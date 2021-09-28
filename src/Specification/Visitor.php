<?php

namespace Greensight\LaravelElasticQuerySpecification\Specification;

use Greensight\LaravelElasticQuerySpecification\Specification\Specification;

interface Visitor
{
    public function visitRoot(Specification $specification): void;

    public function visitNested(string $field, Specification $specification): void;

    public function done(): void;
}