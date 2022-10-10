<?php

namespace Ensi\LaravelElasticQuerySpecification\Faceting;

use Ensi\LaravelElasticQuerySpecification\Agregating\AllowedAggregate;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class AllowedFacet
{
    private string $name;
    private array $filterNames;

    private bool $enabled = false;
    private array $filters = [];
    private ?AllowedAggregate $aggregate = null;

    public function __construct(string $name, array $filterNames)
    {
        Assert::stringNotEmpty($name);
        Assert::notEmpty($filterNames);
        Assert::allStringNotEmpty($filterNames);

        $this->name = $name;
        $this->filterNames = $filterNames;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int,string>
     */
    public function filterNames(): Collection
    {
        return new Collection($this->filterNames);
    }

    public function isActive(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * @return array<int,AllowedFilter>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    public function attachFilter(AllowedFilter $filter): void
    {
        $this->filters[] = $filter;
    }

    public function aggregate(): ?AllowedAggregate
    {
        return $this->aggregate;
    }

    public function attachAggregate(AllowedAggregate $aggregate): self
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    public static function wrap(self|string $source): self
    {
        return $source instanceof self
            ? $source
            : self::new($source);
    }

    public static function fromAggregate(string $aggregateName, string|array|null $filterNames = null): self
    {
        return self::new($aggregateName, $filterNames);
    }

    public static function terms(string $name, string|array|null $filterNames = null, ?string $field = null): self
    {
        return self::new($name, $filterNames)
            ->attachAggregate(AllowedAggregate::terms($name, $field));
    }

    public static function minmax(string $name, string|array|null $filterNames = null, ?string $field = null): self
    {
        return self::new($name, $filterNames)
            ->attachAggregate(AllowedAggregate::minmax($name, $field));
    }

    protected static function new(string $name, string|array|null $filterNames = null): self
    {
        return new self(
            $name,
            self::normalizeFilterNames($filterNames) ?? [$name]
        );
    }

    protected static function normalizeFilterNames(string|array|null $filterNames): ?array
    {
        if ($filterNames === null) {
            return null;
        }

        return is_array($filterNames) ? $filterNames : (array)$filterNames;
    }
}
