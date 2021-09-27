<?php

use Greensight\LaravelElasticQuery\Aggregating\AggregationsQuery;
use Greensight\LaravelElasticQuery\Search\SearchQuery;
use Greensight\LaravelElasticQuerySpecification\Contracts\QueryParameters;
use Greensight\LaravelElasticQuerySpecification\CustomParameters;
use Greensight\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Greensight\LaravelElasticQuerySpecification\Tests\Integration\TestAggregationResults;
use Greensight\LaravelElasticQuerySpecification\Tests\Integration\TestSearchResults;
use Greensight\LaravelElasticQuerySpecification\Tests\TestCase;
use Greensight\LaravelElasticQuerySpecification\Tests\UnitTestCase;
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
