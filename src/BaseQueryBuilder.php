<?php

namespace Greensight\LaravelElasticQuerySpecification;

use Generator;
use Greensight\LaravelElasticQuerySpecification\Contracts\QueryParameters;
use Greensight\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Greensight\LaravelElasticQuerySpecification\Specification\Visitor;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @template T
 */
abstract class BaseQueryBuilder implements ValidatesWhenResolved
{
    use ForwardsCalls;

    private bool $built = false;

    /** @var T */
    protected mixed $query;

    public function __construct(
        mixed $query,
        protected CompositeSpecification $specification,
        protected QueryParameters $parameters
    ) {
        $this->query = $query;
    }

    /**
     * @return T
     */
    public function query(): mixed
    {
        $this->build();

        return $this->query;
    }

    /**
     * @return Generator|Visitor[]
     */
    abstract protected function processors(): Generator;

    /**
     * @inheritDoc
     */
    public function validateResolved(): void
    {
        $this->build();
    }

    protected function build(): void
    {
        if ($this->built) {
            return;
        }

        foreach ($this->processors() as $processor) {
            $this->specification->accept($processor);
        }

        $this->built = true;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->query(), $name, $arguments);
    }
}