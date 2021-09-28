<?php

use Greensight\LaravelElasticQuery\Contracts\BoolQuery;
use Greensight\LaravelElasticQuerySpecification\Contracts\FilterAction;
use Greensight\LaravelElasticQuerySpecification\Filtering\AllowedFilter;

uses()->group('unit');

test('construct sets field', function (string $name, ?string $field, string $expected) {
    $filter = expectInvoke(FilterAction::class, 1, any(), any(), $expected);

    $allowedFilter = AllowedFilter::custom($name, $filter, $field)->setValue(1);
    $allowedFilter(Mockery::mock(BoolQuery::class));
})->with([
    'only name' => ['foo', null, 'foo'],
    'name and field' => ['foo', 'bar', 'bar'],
]);

test('apply', function () {
    $filter = expectInvoke(FilterAction::class, 1, any(), 1, any());

    $allowedFilter = AllowedFilter::custom('foo', $filter)->setValue(1);
    $allowedFilter(Mockery::mock(BoolQuery::class));
});

test('apply not called when not active', function () {
    $filter = expectInvoke(FilterAction::class, 0);

    $allowedFilter = AllowedFilter::custom('foo', $filter);
    $allowedFilter(Mockery::mock(BoolQuery::class));
});

test('set value', function (mixed $value, mixed $expected) {
    $allowedFilter = AllowedFilter::custom('foo', Mockery::mock(FilterAction::class))
        ->setValue($value);

    expect($allowedFilter->value())->toBe($expected);
})->with([
    'string' => ['foo', 'foo'],
    'boolean' => [false, false],
    'filled array' => [[1, null], [1, null]],
    'empty array' => [[], null],
    'array of only nulls' => [[null, null], null],
]);

test('is active', function (bool $enabled, mixed $value, bool $expected) {
    $allowedFilter = AllowedFilter::custom('foo', Mockery::mock(FilterAction::class))
        ->setValue($value);

    $enabled ? $allowedFilter->enable() : $allowedFilter->disable();

    expect($allowedFilter->isActive())->toBe($expected);
})->with([
    'enabled and has value' => [true, 100, true],
    'enabled and no value' => [true, null, false],
    'disabled and has value' => [false, 100, false],
    'disabled and no value' => [false, null, false],
]);

test('value', function (mixed $value, mixed $default, mixed $expected) {
    $allowedFilter = AllowedFilter::custom('foo', Mockery::mock(FilterAction::class))
        ->default($default)
        ->setValue($value);

    expect($allowedFilter->value())->toBe($expected);
})->with([
    'only value' => [35, null, 35],
    'only default' => [null, 100, 100],
    'both value and default' => [35, 100, 35],
    'nothing' => [null, null, null],
]);