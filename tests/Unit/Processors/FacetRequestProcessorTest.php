<?php

use Ensi\LaravelElasticQuerySpecification\Agregating\AllowedAggregate;
use Ensi\LaravelElasticQuerySpecification\Exceptions\AggregateNotFoundException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Exceptions\NotUniqueNameException;
use Ensi\LaravelElasticQuerySpecification\Faceting\AllowedFacet;
use Ensi\LaravelElasticQuerySpecification\Processors\FacetRequestProcessor;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;
use Illuminate\Support\Collection;

uses()->group('unit');

test('not allowed facets', function () {
    $spec = Specification::new()->allowedFacets([AllowedFacet::terms('foo')]);

    $processor = new FacetRequestProcessor(new Collection(['foo', 'bar']));
    $processor->visitRoot($spec);

    expect(fn () => $processor->done())
        ->toThrow(InvalidQueryException::class);
});

test('attach aggregate', function () {
    $facet = AllowedFacet::fromAggregate('foo');
    $agg = AllowedAggregate::minmax('foo');

    $spec = Specification::new()
        ->allowedFacets([$facet])
        ->allowedAggregates([$agg]);

    FluentProcessor::new(FacetRequestProcessor::class, [])
        ->visitRoot($spec)
        ->done();

    expect($facet->aggregate())->toBe($agg);
});

test('missing aggregate', function () {
    $spec = Specification::new()->allowedFacets(['foo']);
    $processor = new FacetRequestProcessor(new Collection());

    expect(fn () => $processor->visitRoot($spec))
        ->toThrow(AggregateNotFoundException::class);
});

test('enable requested facet', function () {
    $facet = AllowedFacet::terms('foo');
    $spec = Specification::new()->allowedFacets([$facet]);

    FluentProcessor::new(FacetRequestProcessor::class, ['foo'])
        ->visitRoot($spec)
        ->done();

    expect($facet->isActive())->toBeTrue();
});

test('disable not requested facet', function () {
    $facet = AllowedFacet::terms('foo');
    $spec = Specification::new()->allowedFacets([$facet]);

    FluentProcessor::new(FacetRequestProcessor::class, [])
        ->visitRoot($spec)
        ->done();

    expect($facet->isActive())->toBeFalse();
});

test('not unique name', function () {
    $spec = Specification::new()->allowedFacets([AllowedFacet::terms('foo')]);

    $processor = new FacetRequestProcessor(new Collection([]));
    $processor->visitRoot($spec);

    expect(fn () => $processor->visitNested('bar', $spec))
        ->toThrow(NotUniqueNameException::class);
});
