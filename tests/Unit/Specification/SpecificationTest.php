<?php

use Greensight\LaravelElasticQuerySpecification\Agregating\AllowedAggregate;
use Greensight\LaravelElasticQuerySpecification\Contracts\Constraint;
use Greensight\LaravelElasticQuerySpecification\Exceptions\ComponentExistsException;
use Greensight\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Greensight\LaravelElasticQuerySpecification\Sorting\AllowedSort;
use Greensight\LaravelElasticQuerySpecification\Specification\CallbackConstraint;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;

uses()->group('unit');

test('allowed filters', function () {
    $spec = Specification::new()
        ->allowedFilters(['foo', AllowedFilter::exact('bar')]);

    expect($spec->filters())->toHaveCount(2);
});

test('add custom constraint', function () {
    $spec = Specification::new()
        ->addConstraint(Mockery::mock(Constraint::class));

    expect($spec->constraints())->toHaveCount(1);
});

test('add callback constraint', function () {
    $spec = Specification::new()->addConstraint(fn() => null);

    expect($spec->constraints())
        ->each()
        ->toBeInstanceOf(CallbackConstraint::class);
});

test('constraints includes filters', function () {
    $spec = Specification::new()
        ->allowedFilters(['foo'])
        ->addConstraint(fn() => null);

    expect($spec->constraints())->toHaveCount(2);
});

test('allowed sorts', function () {
    $spec = Specification::new()
        ->allowedSorts(['foo', AllowedSort::field('bar')]);

    expect($spec->sorts())->toHaveCount(2);
});

test('allowed aggregates', function () {
    $spec = Specification::new()
        ->allowedAggregates(['foo', AllowedAggregate::terms('bar')]);

    expect($spec->aggregates())->toHaveCount(2);
});

test('duplicate component name', function (string $method) {
    expect(fn() => Specification::new()->{$method}(['foo', 'bar', 'foo']))
        ->toThrow(ComponentExistsException::class);
})->with([
    'filter' => ['allowedFilters'],
    'sort' => ['allowedSorts'],
    'aggregate' => ['allowedAggregates'],
]);
