<?php

namespace Greensight\LaravelElasticQuerySpecification\Tests\Data;

class ProductIndexSeeder extends IndexSeeder
{
    protected string $indexName = 'test_spec_products';

    protected array $fixtures = ['products.json'];

    protected array $mappings = [
        'properties' => [
            'product_id' => ['type' => 'keyword'],
            'active' => ['type' => 'boolean'],

            'name' => ['type' => 'keyword', 'copy_to' => 'search_name'],
            'search_name' => ['type' => 'text'],
            'description' => ['type' => 'text'],

            'code' => ['type' => 'keyword'],
            'tags' => ['type' => 'keyword'],
            'rating' => ['type' => 'integer'],
            'package' => ['type' => 'keyword'],

            'cashback' => [
                'type' => 'object',
                'properties' => [
                    'active' => ['type' => 'boolean'],
                    'value' => ['type' => 'integer'],
                ],
            ],

            'offers' => [
                'type' => 'nested',
                'properties' => [
                    'seller_id' => ['type' => 'keyword'],
                    'active' => ['type' => 'boolean'],
                    'price' => ['type' => 'double'],
                ],
            ],
        ],
    ];
}