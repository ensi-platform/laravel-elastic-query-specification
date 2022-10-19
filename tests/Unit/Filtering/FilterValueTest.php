<?php

use Ensi\LaravelElasticQuerySpecification\Filtering\FilterValue;
use Ensi\LaravelElasticQuerySpecification\Tests\Unit\Filtering\CallableStub;

uses()->group('unit');

test('when', function () {
    FilterValue::make('foo')
        ->when(true, CallableStub::shouldCallWith('foo'));
});

test('when multiple', function () {
    FilterValue::make(['foo', 'bar'])
        ->whenMultiple(CallableStub::shouldCallWith(['foo', 'bar']));
});

test('when single', function (mixed $source, mixed $expected) {
    FilterValue::make($source)
        ->whenSingle(CallableStub::shouldCallWith($expected));
})->with([
    'string' => ['foo', 'foo'],
    'array with one element' => [['foo'], 'foo'],
    'array with one not null element' => [[null, 'foo', null], 'foo'],
]);

test('when same', function (mixed $value) {
    FilterValue::make($value)
        ->whenSame($value, CallableStub::shouldCallWith($value));
})->with([
    'boolean' => [true],
    'integer' => [120],
    'string' => ['foo'],
]);

test('else', function () {
    FilterValue::make('foo')
        ->whenMultiple(CallableStub::shouldNotCall())
        ->orElse(CallableStub::shouldCallWith('foo'));
});

test('call only first callback', function () {
    FilterValue::make('foo')
        ->whenMultiple(CallableStub::shouldNotCall())
        ->whenSame('foo', CallableStub::shouldCall())
        ->whenSingle(CallableStub::shouldNotCall())
        ->orElse(CallableStub::shouldNotCall());
});
