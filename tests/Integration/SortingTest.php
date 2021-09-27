<?php

use Greensight\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Greensight\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Greensight\LaravelElasticQuerySpecification\Sorting\AllowedSort;
use Greensight\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Greensight\LaravelElasticQuerySpecification\Specification\Specification;

uses()->group('integration');

test('root sort', function () {
    $spec = CompositeSpecification::new()
        ->allowedSorts(['rating'])
        ->where('tags', 'video');

    $request = ['sort' => '-rating'];

    searchQuery($spec, $request)->assertDocumentIds([1, 328]);
});

test('nested sort', function () {
    $nested = Specification::new()
        ->allowedSorts(['+price'])
        ->where('seller_id', 20);

    $spec = CompositeSpecification::new()
        ->where('tags', 'video')
        ->nested('offers', $nested);

    $request = ['sort' => '-price'];

    searchQuery($spec, $request)->assertDocumentOrder([328, 1]);
});

test('complex sort', function () {
    $spec = CompositeSpecification::new()
        ->allowedSorts([AllowedSort::field('-cashback', 'cashback.value')])
        ->allowedFilters([AllowedFilter::exact('cashback', 'cashback.active')])
        ->nested('offers', function (Specification $spec) {
            $spec->allowedSorts(['+price']);
        });

    $request = [
        'filter' => ['cashback' => 'true'],
        'sort' => 'cashback,-price',
    ];

    searchQuery($spec, $request)->assertDocumentOrder([328, 1, 150, 319]);
});

test('validate names', function () {
    $spec = CompositeSpecification::new()->allowedSorts(['rating']);
    $request = ['sort' => ['unknown', 'rating']];

    expect(fn() => searchQuery($spec, $request))
        ->toThrow(InvalidQueryException::class);
});