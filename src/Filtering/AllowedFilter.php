<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuery\Contracts\MatchOptions;
use Ensi\LaravelElasticQuery\Contracts\MultiMatchOptions;
use Ensi\LaravelElasticQuerySpecification\Contracts\Constraint;
use Ensi\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Webmozart\Assert\Assert;

class AllowedFilter implements Constraint
{
    private string $name;

    protected string $field;
    protected FilterAction $action;
    protected mixed $value = null;
    protected mixed $defaultValue = null;
    protected bool $enabled = true;

    public function __construct(string $name, FilterAction $action, ?string $field = null)
    {
        Assert::stringNotEmpty($name);
        Assert::nullOrStringNotEmpty($field);

        $this->name = $name;
        $this->field = $field ?? $this->name;
        $this->action = $action;
    }

    public function __invoke(BoolQuery $query): void
    {
        if (!$this->isActive()) {
            return;
        }

        ($this->action)($query, $this->value(), $this->field);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $this->refineValue($value);

        return $this;
    }

    public function isActive(): bool
    {
        return $this->enabled && $this->value() !== null;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function default(mixed $value): self
    {
        $this->defaultValue = $this->refineValue($value);

        return $this;
    }

    public function value(): mixed
    {
        return $this->value ?? $this->defaultValue;
    }

    public static function wrap(self|string $source): self
    {
        return $source instanceof self
            ? $source
            : self::exact($source);
    }

    public static function custom(string $name, FilterAction $action, ?string $field = null): self
    {
        return new static($name, $action, $field);
    }

    public static function exact(string $name, ?string $field = null): self
    {
        return new static($name, new ExactFilterAction(), $field);
    }

    public static function exists(string $name, ?string $field = null): self
    {
        return new static($name, new ExistsFilterAction(), $field);
    }

    public static function greater(string $name, ?string $field = null): self
    {
        return new static($name, new RangeFilterAction('>'), $field);
    }

    public static function greaterOrEqual(string $name, ?string $field = null): self
    {
        return new static($name, new RangeFilterAction('>='), $field);
    }

    public static function less(string $name, ?string $field = null): self
    {
        return new static($name, new RangeFilterAction('<'), $field);
    }

    public static function lessOrEqual(string $name, ?string $field = null): self
    {
        return new static($name, new RangeFilterAction('<='), $field);
    }

    public static function match(string $name, ?string $field, ?MatchOptions $options = null): self
    {
        return new static($name, new MatchFilterAction($options ?? new MatchOptions()), $field);
    }

    public static function multiMatch(string $name, array $fields, ?MultiMatchOptions $options = null): self
    {
        return new static(
            $name,
            new MultiMatchFilterAction($options ?? new MultiMatchOptions()),
            MultiMatchFilterAction::encodeFields($fields)
        );
    }

    private function refineValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) && !array_filter($value)) {
            return null;
        }

        return $value;
    }
}
