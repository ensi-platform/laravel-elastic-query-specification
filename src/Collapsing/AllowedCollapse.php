<?php

namespace Ensi\LaravelElasticQuerySpecification\Collapsing;

use Ensi\LaravelElasticQuerySpecification\Contracts\Collapse;
use Ensi\LaravelElasticQuery\Contracts\CollapsibleQuery;
use Webmozart\Assert\Assert;

class AllowedCollapse implements Collapse
{
    private string $name;

    protected string $field;

    public function __construct(string $name, ?string $field = null)
    {
        Assert::stringNotEmpty($name);
        Assert::nullOrStringNotEmpty($field);

        $this->name = $name;
        $this->field = $field ?? $this->name;
    }

    public function __invoke(CollapsibleQuery $query): void
    {
        $query->collapse($this->field);
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function field(string $name, ?string $field = null): self
    {
        return new static($name, $field);
    }

    public static function wrap(self|string $source): self
    {
        return $source instanceof self
            ? $source
            : self::field($source);
    }
}
