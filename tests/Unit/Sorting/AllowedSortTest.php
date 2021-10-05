<?php

use Ensi\LaravelElasticQuery\Contracts\SortableQuery;
use Ensi\LaravelElasticQuery\Contracts\SortMode;
use Ensi\LaravelElasticQuery\Contracts\SortOrder;
use Ensi\LaravelElasticQuerySpecification\Contracts\SortAction;
use Ensi\LaravelElasticQuerySpecification\Sorting\AllowedSort;

uses()->group('unit');

test('parse name', function (string $source, string $expected) {
    [$name] = AllowedSort::parseNameAndOrder($source);

    expect($name)->toBe($expected);
})->with([
    'without order' => ['foo', 'foo'],
    'with order' => ['-foo', 'foo'],
]);

test('parse order', function (string $source, ?string $expected) {
    [, $order] = AllowedSort::parseNameAndOrder($source);

    expect($order)->toBe($expected);
})->with([
    'without order' => ['foo', null],
    'ascending' => ['+foo', SortOrder::ASC],
    'descending' => ['-foo', SortOrder::DESC],
]);

test('construct sets default order', function () {
    $action = expectInvoke(SortAction::class, 1, any(), SortOrder::DESC, any(), 'foo');

    $allowedSort = AllowedSort::custom('-foo', $action);
    $allowedSort(Mockery::mock(SortableQuery::class), null);
});

test('construct sets field', function (string $name, ?string $field, string $expected) {
    $action = expectInvoke(SortAction::class, 1, any(), any(), any(), $expected);

    $allowedSort = AllowedSort::custom($name, $action, $field);
    $allowedSort(Mockery::mock(SortableQuery::class), null);
})->with([
    'only name' => ['foo', null, 'foo'],
    'name and field' => ['foo', 'bar', 'bar'],
]);

test('mode', function () {
    $action = expectInvoke(SortAction::class, 1, any(), any(), SortMode::MEDIAN, any());

    $allowedSort = AllowedSort::custom('-foo', $action)->mode(SortMode::MEDIAN);
    $allowedSort(Mockery::mock(SortableQuery::class), null);
});