<?php

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Processors\ConstraintProcessor;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

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
