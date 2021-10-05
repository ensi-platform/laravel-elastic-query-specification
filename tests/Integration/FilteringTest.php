<?php

use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;
use Ensi\LaravelElasticQuerySpecification\Tests\Data\ProductIndexSeeder;

beforeEach(function () {
    ProductIndexSeeder::run();
});

uses()->group('integration');

test('root filters', function () {
    $spec = CompositeSpecification::new()
        ->allowedFilters(['code'])
        ->where('active', true);

    $request = [
        'filter' => ['code' => ['tv', 'gloves']],
    ];

    searchQuery($spec, $request)->assertDocumentIds([1]);
});

test('nested', function () {
    $spec = CompositeSpecification::new();
    $spec->nested(
        'offers',
        Specification::new()
            ->allowedFilters(['active'])
            ->where('seller_id', 20)
    );
    $spec->nested(
        'offers',
        Specification::new()
            ->allowedFilters(['active'])
            ->where('seller_id', 90)
    );

    $request = ['filter' => ['active' => true]];

    searchQuery($spec, $request)->assertDocumentIds([328]);
});

test('validate names' , function () {
    $spec = CompositeSpecification::new()->allowedFilters(['code', 'active']);
    $request = [
        'filter' => ['code' => 'tv', 'unknown' => 10],
    ];

    expect(fn() => searchQuery($spec, $request))
        ->toThrow(InvalidQueryException::class);
});

test('exists filter', function (bool $value, int $expectedCount) {
    $spec = CompositeSpecification::new()->allowedFilters([
        AllowedFilter::exists('cashback', 'cashback.active')
    ]);
    $request = ['filter' => ['cashback' => $value]];

    expect(searchQuery($spec, $request)->get())->toHaveCount($expectedCount);
})->with([
    'true' => [true, 4],
    'false' => [false, 2],
]);