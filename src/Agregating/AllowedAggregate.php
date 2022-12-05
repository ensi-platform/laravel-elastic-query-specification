<?php

namespace Ensi\LaravelElasticQuerySpecification\Agregating;

use Ensi\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Ensi\LaravelElasticQuerySpecification\Contracts\Aggregate;
use Ensi\LaravelElasticQuerySpecification\Contracts\AggregateAction;
use Webmozart\Assert\Assert;

class AllowedAggregate implements Aggregate
{
    private string $name;

    protected string $field;
    protected AggregateAction $action;

    public function __construct(string $name, AggregateAction $action, ?string $field = null)
    {
        Assert::stringNotEmpty($name);
        Assert::nullOrStringNotEmpty($field);

        $this->name = $name;
        $this->field = $field ?? $this->name;
        $this->action = $action;
    }

    public function __invoke(AggregationsBuilder $builder): void
    {
        ($this->action)($builder, $this->name, $this->field);
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function wrap(self|string $source): self
    {
        return $source instanceof self
            ? $source
            : self::terms($source);
    }

    public static function terms(string $name, ?string $field = null, ?int $size = null): self
    {
        return new static($name, new TermsAggregateAction($size), $field);
    }

    public static function minmax(string $name, ?string $field = null): self
    {
        return new static($name, new MinMaxAggregateAction(), $field);
    }

    public static function count(string $name, ?string $field = null): self
    {
        return new static($name, new ValueCountAggregateAction(), $field);
    }
}
