<?php

use Greensight\LaravelElasticQuery\Contracts\BoolQuery;
use Greensight\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Greensight\LaravelElasticQuerySpecification\Processors\ConstraintProcessor;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;
use Greensight\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

uses()->group('unit');

test('visit root', function () {
    $spec = Specification::new()->where('foo', 10);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('where')
        ->with('foo', 10)
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(ConstraintProcessor::class, $query)
        ->visitRoot($spec);
});

test('nested constraint', function () {
    $spec = Specification::new()->allowedFilters([
        AllowedFilter::exact('foo')->default(10),
    ]);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('whereHas')
        ->with('nested', any())
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(ConstraintProcessor::class, $query)
        ->visitNested('nested', $spec);
});

test('nested no active filters', function () {
    $spec = Specification::new()->where('foo', 10);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('whereHas')->andReturnSelf()->never();

    FluentProcessor::new(ConstraintProcessor::class, $query)
        ->visitNested('nested', $spec);
});