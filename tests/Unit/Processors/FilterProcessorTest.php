<?php

use Greensight\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Greensight\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Greensight\LaravelElasticQuerySpecification\Processors\FilterProcessor;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;
use Greensight\LaravelElasticQuerySpecification\Tests\Unit\Processors\FluentProcessor;

uses()->group('unit');

test('not allowed filters', function () {
    $spec = Specification::new()->allowedFilters(['foo', 'bar']);

    $processor = new FilterProcessor(collect(['foo' => 1, 'baz' => 2]));
    $processor->visitRoot($spec);

    expect(fn() => $processor->done())
        ->toThrow(InvalidQueryException::class);
});

test('set filter value', function () {
    $filter = AllowedFilter::exact('foo');
    $spec = Specification::new()->allowedFilters([$filter]);

    FluentProcessor::new(FilterProcessor::class, ['foo' => 1])
        ->visitRoot($spec)
        ->done();

    expect($filter->value())->toBe(1);
});

test('ignore missing values', function () {
    $filter = AllowedFilter::exact('foo');
    $spec = Specification::new()->allowedFilters([$filter]);

    FluentProcessor::new(FilterProcessor::class, [])
        ->visitRoot($spec)
        ->done();

    expect($filter->value())->toBeNull();
});