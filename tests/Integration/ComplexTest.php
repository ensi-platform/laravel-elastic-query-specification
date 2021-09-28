<?php

use Greensight\LaravelElasticQuerySpecification\QueryBuilderRequest;
use Greensight\LaravelElasticQuerySpecification\Tests\Data\ProductSpecification;
use Greensight\LaravelElasticQuerySpecification\Tests\Integration\TestSearchResults;
use Illuminate\Http\Request;

uses()->group('integration');

function makeQueryRequest(array $input): QueryBuilderRequest
{
    $request = new Request($input);

    test()->instance('request', $request);

    return resolve(QueryBuilderRequest::class);
}

test('search query', function () {
    $queryRequest = makeQueryRequest([
        'filter' => ['package' => 'bottle'],
    ]);

    TestSearchResults::make(new ProductSpecification(), $queryRequest)
        ->assertDocumentIds([150, 405]);
});
