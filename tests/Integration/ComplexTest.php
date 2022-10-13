<?php

use Ensi\LaravelElasticQuerySpecification\QueryBuilderRequest;
use Ensi\LaravelElasticQuerySpecification\Tests\Data\ProductSpecification;
use Ensi\LaravelElasticQuerySpecification\Tests\Integration\TestSearchResults;
use Illuminate\Http\Request;
use function Pest\Laravel\instance;

uses()->group('integration');

function makeQueryRequest(array $input): QueryBuilderRequest
{
    $request = new Request($input);

    instance('request', $request);

    return resolve(QueryBuilderRequest::class);
}

test('search query', function () {
    $queryRequest = makeQueryRequest([
        'filter' => ['package' => 'bottle'],
    ]);

    TestSearchResults::make(new ProductSpecification(), $queryRequest)
        ->assertDocumentIds([150, 405]);
});
