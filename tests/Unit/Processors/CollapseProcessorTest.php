<?php

use Ensi\LaravelElasticQuery\Contracts\CollapsibleQuery;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Processors\CollapseProcessor;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

uses()->group('unit');

test('collapses', function () {
    $spec = Specification::new()->allowedCollapses(['foo', 'bar', 'baz']);

    $query = Mockery::mock(CollapsibleQuery::class);
    $query->expects('collapse')
        ->times(1)
        ->andReturnSelf();

    FluentProcessor::new(CollapseProcessor::class, $query, 'bar')
        ->visitRoot($spec)
        ->done();
});

test('not allowed collapse', function () {
    $spec = Specification::new()->allowedCollapses(['foo']);

    $query = Mockery::mock(CollapsibleQuery::class);
    $query->expects('collapse')->andReturnSelf()->never();

    FluentProcessor::new(CollapseProcessor::class, $query, 'bar')
        ->visitRoot($spec)
        ->done();
})->throws(InvalidQueryException::class);
