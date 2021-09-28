<?php

namespace Greensight\LaravelElasticQuerySpecification\Tests\Integration;

use Greensight\LaravelElasticQuery\Aggregating\AggregationsQuery;
use Greensight\LaravelElasticQuery\Aggregating\MinMax;
use Greensight\LaravelElasticQuerySpecification\AggregateQueryBuilder;
use Greensight\LaravelElasticQuerySpecification\Contracts\QueryParameters;
use Greensight\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Greensight\LaravelElasticQuerySpecification\Tests\Data\ProductsIndex;
use Illuminate\Support\Collection;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class TestAggregationResults
{
    public function __construct(private Collection $results)
    {
    }

    public static function make(
        CompositeSpecification $spec,
        QueryParameters $parameters,
        ?AggregationsQuery $query = null
    ): self {
        $builder = new AggregateQueryBuilder($query ?? ProductsIndex::aggregate(), $spec, $parameters);
        $builder->validateResolved();

        return new self($builder->get());
    }

    public function all(): Collection
    {
        return $this->results;
    }

    public function get(string $key): mixed
    {
        return $this->results->get($key);
    }

    public function assertBucketKeys(string $aggName, array $expected): self
    {
        assertEqualsCanonicalizing(
            $expected,
            $this->get($aggName)?->pluck('key')?->all()
        );

        return $this;
    }

    public function assertMinMax(string $aggName, mixed $min, mixed $max): self
    {
        assertEquals(new MinMax($min, $max), $this->get($aggName));

        return $this;
    }
}