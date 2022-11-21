<?php

use Ensi\LaravelElasticQuerySpecification\Exceptions\InvalidQueryException;
use Ensi\LaravelElasticQuerySpecification\Filtering\AllowedFilter;
use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;
use Ensi\LaravelElasticQuerySpecification\Specification\Specification;

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

test('validate names', function () {
    $spec = CompositeSpecification::new()->allowedFilters(['code', 'active']);
    $request = [
        'filter' => ['code' => 'tv', 'unknown' => 10],
    ];

    expect(fn () => searchQuery($spec, $request))
        ->toThrow(InvalidQueryException::class);
});

test('exists filter', function (bool $value, int $expectedCount) {
    $spec = CompositeSpecification::new()->allowedFilters([
        AllowedFilter::exists('cashback', 'cashback.active'),
    ]);
    $request = ['filter' => ['cashback' => $value]];

    expect(searchQuery($spec, $request)->get())->toHaveCount($expectedCount);
})->with([
    'true' => [true, 4],
    'false' => [false, 2],
]);

test('range filter', function (array $request, array $expectedIds) {
    $spec = CompositeSpecification::new()
        ->allowedFilters([
            AllowedFilter::greater('rating__gt', 'rating'),
            AllowedFilter::greaterOrEqual('rating__gte', 'rating'),
            AllowedFilter::less('rating__lt', 'rating'),
            AllowedFilter::lessOrEqual('rating__lte', 'rating'),
        ]);

    searchQuery($spec, ['filter' => $request])->assertDocumentIds($expectedIds);
})->with([
    'greater' => [['rating__gt' => 7], [150, 405]],
    'greater or equal' => [['rating__gte' => 7], [1, 150, 405]],
    'less' => [['rating__lt' => 5], [319, 471]],
    'less or equal' => [['rating__lte' => 5], [319, 328, 471]],
    'between' => [['rating__lte' => 7, 'rating__gte' => 5], [1, 328]],
]);

test('match filter', function (string $query, array $expectedIds) {
    $spec = CompositeSpecification::new()
        ->allowedFilters([
            AllowedFilter::match('name', 'search_name'),
        ]);

    searchQuery($spec, ['filter' => ['name' => $query]])->assertDocumentIds($expectedIds);
})->with([
    'single' => ['water', [150]],
    'multiple' => ['gloves', [319, 471]],
]);
