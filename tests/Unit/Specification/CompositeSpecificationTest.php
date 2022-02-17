<?php

use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Specification\VisitorStub;

uses()->group('unit');

test('forward calls returns self', function () {
    $spec = new CompositeSpecification();

    expect($spec->allowedFilters(['foo']))->toBe($spec);
});

test('add nested', function () {
    $spec = CompositeSpecification::new()->nested('foo', fn () => null);

    expect(VisitorStub::inspect($spec)->nestedFields)->toEqual(['foo']);
});

test('add nested instance', function () {
    $nested = Specification::new();
    $spec = CompositeSpecification::new()->nested('foo', $nested);

    expect(VisitorStub::inspect($spec)->nestedSpecifications)
        ->toHaveCount(1)
        ->each->toBe($nested);
});

test('accept visit root', function () {
    $spec = new CompositeSpecification();

    expect(VisitorStub::inspect($spec)->rootSpecification)->not->toBeNull();
});

test('accept visit nested', function () {
    $spec = CompositeSpecification::new()->nested('foo', fn () => null);

    expect(VisitorStub::inspect($spec)->nestedSpecifications)->toHaveCount(1);
});

test('accept done', function () {
    $spec = new CompositeSpecification();

    expect(VisitorStub::inspect($spec)->done)->toBeTrue();
});
