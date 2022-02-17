<?php

namespace Ensi\LaravelElasticQuerySpecification\Sorting;

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuery\Contracts\SortMode;
use Ensi\LaravelElasticQuery\Contracts\SortOrder;
use Ensi\LaravelElasticQuerySpecification\Contracts\Sort;
use Ensi\LaravelElasticQuerySpecification\Contracts\SortAction;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class AllowedSort implements Sort
{
    private string $name;

    protected SortAction $action;
    protected string $field;
    protected string $defaultOrder;
    protected ?string $mode = null;

    public function __construct(string $name, SortAction $action, ?string $field = null)
    {
        Assert::stringNotEmpty($name);
        Assert::nullOrStringNotEmpty($field);

        [$this->name, $order] = self::parseNameAndOrder($name);

        $this->field = $field ?? $this->name;
        $this->action = $action;
        $this->defaultOrder = $order ?? SortOrder::ASC;
    }

    public static function parseNameAndOrder(string $source): array
    {
        return match (true) {
            Str::startsWith($source, '+') => [ltrim($source, '+'), SortOrder::ASC],
            Str::startsWith($source, '-') => [ltrim($source, '-'), SortOrder::DESC],
            default => [$source, null]
        };
    }

    public function __invoke(SortableQuery $query, ?string $order): void
    {
        $this->action->__invoke($query, $order ?? $this->defaultOrder, $this->mode, $this->field);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function mode(string $value): self
    {
        Assert::oneOf($value, SortMode::cases());

        $this->mode = $value;

        return $this;
    }

    public function byMin(): self
    {
        return $this->mode(SortMode::MIN);
    }

    public function byMax(): self
    {
        return $this->mode(SortMode::MAX);
    }

    public function byAvg(): self
    {
        return $this->mode(SortMode::AVG);
    }

    public function bySum(): self
    {
        return $this->mode(SortMode::SUM);
    }

    public function byMedian(): self
    {
        return $this->mode(SortMode::MEDIAN);
    }

    public static function field(string $name, ?string $field = null): self
    {
        return new static($name, new FieldSortAction(), $field);
    }

    public static function custom(string $name, SortAction $action, ?string $field = null): self
    {
        return new static($name, $action, $field);
    }

    public static function wrap(self|string $source): self
    {
        return $source instanceof self
            ? $source
            : self::field($source);
    }
}
