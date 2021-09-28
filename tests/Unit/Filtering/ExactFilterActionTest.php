<?php

use Greensight\LaravelElasticQuery\Contracts\BoolQuery;
use Greensight\LaravelElasticQuerySpecification\Filtering\ExactFilterAction;

uses()->group('unit');

test('single value', function (mixed $value, mixed $expected) {
    $query = Mockery::mock(BoolQuery::class);
    $query->expects('where')
        ->with('single', $expected)
        ->once()
        ->andReturnSelf();

    (new ExactFilterAction())($query, $value, 'single');
})->with([
    'scalar' => ['foo', 'foo'],
    'single value array' => [[null, 'foo'], 'foo'],
]);

test('multi value', function (mixed $value, mixed $expected) {
    $query = Mockery::mock(BoolQuery::class);
    $query->expects('whereIn')
        ->with('multi', $expected)
        ->once()
        ->andReturnSelf();

    (new ExactFilterAction())($query, $value, 'multi');
})->with([
    'only values' => [[1, 2], [1, 2]],
    'mixed with nulls' => [[null, 1, null, 2], [1, 2]],
]);