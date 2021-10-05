<?php

use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Concerns\ExtractsQueryParametersStub;

uses()->group('unit');

test('convert filter values', function (mixed $value, mixed $expected) {
    $parameters = new ExtractsQueryParametersStub([
        'filter' => ['name' => $value],
    ]);

    $this->assertEquals(['name' => $expected], $parameters->filters()->all());
})->with([
    'true' => ['true', true],
    'false' => ['false', false],
    'array of boolean' => [['true', 'false'], [true, false]],
    'assoc array' => [['foo' => 'bar', 'baz' => 'true'], ['foo' => 'bar', 'baz' => true]],
]);

test('sorts', function (mixed $value, array $expected) {
    $parameters = new ExtractsQueryParametersStub(['sort' => $value]);

    $this->assertEquals($expected, $parameters->sorts()->all());
})->with([
    'array' => [['foo', '-bar'], ['foo', '-bar']],
    'string' => ['-foo,+bar, baz', ['-foo', '+bar', 'baz']],
    'with empty' => ['foo,,bar', ['foo', 'bar']],
]);
