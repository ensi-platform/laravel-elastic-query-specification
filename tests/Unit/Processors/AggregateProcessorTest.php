<?php

use Greensight\LaravelElasticQuery\Contracts\AggregationsBuilder;
use Greensight\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Greensight\LaravelElasticQuerySpecification\Exceptions\NotUniqueNameException;
use Greensight\LaravelElasticQuerySpecification\Processors\AggregateProcessor;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;
use Greensight\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

uses()->group('unit');

test('root aggregate', function () {
    $spec = Specification::new()->allowedAggregates(['foo', 'bar']);

    $query = Mockery::mock(AggregationsBuilder::class);
    $query->expects('terms')
        ->with('foo', 'foo')
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(AggregateProcessor::class, $query, ['foo'])
        ->visitRoot($spec)
        ->done();
});

test('nested aggregate', function () {
    $spec = Specification::new()->allowedAggregates(['foo', 'bar']);

    $query = Mockery::mock(AggregationsBuilder::class);
    $query->expects('nested')
        ->with('field', any())
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(AggregateProcessor::class, $query, ['foo'])
        ->visitNested('field', $spec)
        ->done();
});

test('empty request', function () {
    $spec = Specification::new()->allowedAggregates(['foo', 'bar']);

    $query = Mockery::mock(AggregationsBuilder::class);
    $query->expects('nested')->andReturnSelf()->never();

    FluentProcessor::new(AggregateProcessor::class, $query, [])
        ->visitNested('field', $spec)
        ->done();
});

test('nested constraints', function () {
    $spec = Specification::new()
        ->allowedAggregates(['foo'])
        ->where('bar', 10);

    $query = Mockery::mock(AggregationsBuilder::class);
    $query->allows('nested')
        ->with('field', any())
        ->andReturnUsing(function ($field, callable $callback) use ($query) {
            $callback($query);
            return $query;
        });

    $query->expects('terms')->with('foo', 'foo')->andReturnSelf()->once();
    $query->expects('where')->with('bar', 10)->andReturnSelf()->once();

    FluentProcessor::new(AggregateProcessor::class, $query, ['foo'])
        ->visitNested('field', $spec)
        ->done();
});

test('not allowed aggregate', function () {
    $spec = Specification::new()->allowedAggregates(['foo']);
    $query = Mockery::mock(AggregationsBuilder::class);

    FluentProcessor::new(AggregateProcessor::class, $query, ['bar'])
        ->visitRoot($spec)
        ->done();
})->throws(InvalidQueryException::class);

test('duplicate aggregate names', function () {
    $spec = Specification::new()->allowedAggregates(['foo']);
    $query = Mockery::mock(AggregationsBuilder::class);
    $query->allows('terms')->andReturnSelf();

    FluentProcessor::new(AggregateProcessor::class, $query, ['bar'])
        ->visitRoot($spec)
        ->visitNested('nested', $spec)
        ->done();
})->throws(NotUniqueNameException::class);
