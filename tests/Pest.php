<?php

use Ensi\LaravelElasticQuery\Aggregating\AggregationsQuery;
use Ensi\LaravelElasticQuery\Search\SearchQuery;
use Ensi\LaravelElasticQuerySpecification\CustomParameters;
use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Ensi\LaravelElasticQuerySpecification\Tests\Integration\TestAggregationResults;
use Ensi\LaravelElasticQuerySpecification\Tests\Integration\TestSearchResults;
use Ensi\LaravelElasticQuerySpecification\Tests\TestCase;
use Ensi\LaravelElasticQuerySpecification\Tests\UnitTestCase;
use Mockery\Matcher\Any;
use Mockery\MockInterface;

uses(UnitTestCase::class)->in(__DIR__.'/Unit');
uses(TestCase::class)->in(__DIR__.'/Integration');

function any(): Any
{
    return Mockery::any();
}

/**
 * @template T
 * @class-string<T> string $className
 * @return T
 */
function expectInvoke(string $className, int $times, mixed ...$parameters): MockInterface
{
    $mock = Mockery::mock($className);
    $expectation = $mock->expects('__invoke');

    if (count($parameters) > 0) {
        $expectation->with(...$parameters);
    }

    $expectation->times($times);

    return $mock;
}

function searchQuery(CompositeSpecification $spec, array $parameters, ?SearchQuery $query = null): TestSearchResults
{
    $queryParameters = new CustomParameters($parameters);

    return TestSearchResults::make($spec, $queryParameters, $query);
}

function aggQuery(CompositeSpecification $spec, array $parameters, ?AggregationsQuery $query = null): TestAggregationResults
{
    $queryParameters = new CustomParameters($parameters);

    return TestAggregationResults::make($spec, $queryParameters, $query);
}
