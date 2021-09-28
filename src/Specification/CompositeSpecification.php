<?php

namespace Greensight\LaravelElasticQuerySpecification\Specification;

use Closure;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin Specification
 */
class CompositeSpecification
{
    use ForwardsCalls;

    private Specification $rootSpecification;

    /** @var array|NestedSpecification[] */
    private array $nestedSpecifications = [];

    public function __construct()
    {
        $this->rootSpecification = new Specification();
    }

    public static function new(): static
    {
        return new static();
    }

    public function __call(string $name, array $arguments)
    {
        $result = $this->forwardCallTo($this->rootSpecification, $name, $arguments);

        return $result === $this->rootSpecification ? $this : $result;
    }

    public function nested(string $field, Specification|Closure $factory = null): static
    {
        $spec = $factory instanceof Specification
            ? $factory
            : tap(Specification::new(), $factory);

        $this->nestedSpecifications[] = new NestedSpecification($field, $spec);

        return $this;
    }

    public function accept(Visitor $visitor): void
    {
        $visitor->visitRoot($this->rootSpecification);

        foreach ($this->nestedSpecifications as $nestedSpecification) {
            $nestedSpecification->accept($visitor);
        }

        $visitor->done();
    }
}