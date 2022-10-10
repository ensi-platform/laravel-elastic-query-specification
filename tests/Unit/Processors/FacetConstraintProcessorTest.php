<?php

use Ensi\LaravelElasticQuery\Contracts\BoolQuery;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetConstraintProcessor;
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

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
        ->visitRoot($spec);
});

test('skip active facet filters in root specification', function () {
    $filter = AllowedFilter::exact('foo')->setValue(10);
    $facet = AllowedFacet::minmax('foo');
    $facet->attachFilter($filter);
    $facet->enable();

    $spec = Specification::new()
        ->allowedFilters([$filter])
        ->allowedFacets([$facet]);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('where')
        ->with('foo', 10)
        ->never();

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
        ->visitRoot($spec);
});

test('root specification has inactive facet filter', function () {
    $filter = AllowedFilter::exact('foo')->setValue(10);
    $facet = AllowedFacet::minmax('foo');
    $facet->attachFilter($filter);

    $spec = Specification::new()
        ->allowedFilters([$filter])
        ->allowedFacets([$facet]);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('where')
        ->with('foo', 10)
        ->once()
        ->andReturnSelf();

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
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

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
        ->visitNested('nested', $spec);
});

test('nested specification has active facet', function () {
    $facet = AllowedFacet::minmax('foo');
    $facet->enable();

    $spec = Specification::new()->allowedFacets([$facet]);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('whereHas')->andReturnSelf()->never();

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
        ->visitNested('nested', $spec);
});

test('nested specification does not contain active filters', function () {
    $spec = Specification::new()->where('foo', 10);

    $query = Mockery::mock(BoolQuery::class);
    $query->expects('whereHas')->andReturnSelf()->never();

    FluentProcessor::new(FacetConstraintProcessor::class, $query)
        ->visitNested('nested', $spec);
});
