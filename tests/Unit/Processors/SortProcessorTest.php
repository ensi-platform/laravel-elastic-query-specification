<?php

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuery\Contracts\SortOrder;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\NotUniqueNameException;
use Ensi\LaravelElasticQuerySpecification\Processors\SortProcessor;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

uses()->group('unit');

test('root sorts', function () {
    $spec = Specification::new()->allowedSorts(['foo', 'bar', 'baz']);

    $query = Mockery::mock(SortableQuery::class);
    $query->expects('sortBy')
        ->times(3)
        ->andReturnSelf();

    FluentProcessor::new(SortProcessor::class, $query, ['foo', '+bar', '-baz'])
        ->visitRoot($spec)
        ->done();
});

test('nested sorts', function () {
    $spec = Specification::new()->allowedSorts(['foo']);

    $query = Mockery::mock(SortableQuery::class);
    $query->expects('sortByNested')
        ->with('nested', any())
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(SortProcessor::class, $query, ['-foo'])
        ->visitNested('nested', $spec)
        ->done();
});

test('not allowed sorts', function () {
    $spec = Specification::new()->allowedSorts(['foo']);

    $query = Mockery::mock(SortableQuery::class);
    $query->expects('sortBy')->andReturnSelf()->never();

    FluentProcessor::new(SortProcessor::class, $query, ['+bar'])
        ->visitRoot($spec)
        ->done();
})->throws(InvalidQueryException::class);

test('duplicate sort names', function () {
    $spec = Specification::new()->allowedSorts(['foo']);
    $query = Mockery::mock(SortableQuery::class);

    FluentProcessor::new(SortProcessor::class, $query, ['foo'])
        ->visitRoot($spec)
        ->visitNested('nested', $spec);
})->throws(NotUniqueNameException::class);

test('pass order', function () {
    $spec = Specification::new()->allowedSorts(['foo']);

    $query = Mockery::mock(SortableQuery::class);
    $query->expects('sortBy')
        ->with('foo', SortOrder::DESC, null)
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(SortProcessor::class, $query, ['-foo'])
        ->visitRoot($spec)
        ->done();
});