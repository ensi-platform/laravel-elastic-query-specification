<?php

use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Sorting\AllowedSort;
use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;

uses()->group('integration');

test('collapsing', function () {
    $spec = CompositeSpecification::new()
        ->allowedCollapses(['vat']);

    $request = ['collapse' => 'vat'];
    $results = searchQuery($spec, $request)->get();

    $this->assertCount(2, $results);
});

test('complex collapsing', function () {
    $spec = CompositeSpecification::new()
        ->allowedSorts([AllowedSort::field('rating')])
        ->allowedFilters([AllowedFilter::exact('cashback', 'cashback.active')])
        ->allowedCollapses(['vat']);

    $request = [
        'filter' => ['cashback' => 'true'],
        'sort' => '-rating',
        'collapse' => 'vat',
    ];

    searchQuery($spec, $request)->assertDocumentOrder([150, 1]);
});

test('validate names', function () {
    $spec = CompositeSpecification::new()->allowedCollapses(['vat']);
    $request = ['collapse' => 'unknown'];

    expect(fn () => searchQuery($spec, $request))
        ->toThrow(InvalidQueryException::class);
});
