<?php

namespace Ensi\LaravelElasticQuerySpecification\Specification;

use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class NestedSpecification
{
    public function __construct(
        private string $field,
        private Specification $specification
    ) {
        Assert::stringNotEmpty($field);
    }

    public function accept(Visitor $visitor): void
    {
        $visitor->visitNested($this->field, $this->specification);
    }
}