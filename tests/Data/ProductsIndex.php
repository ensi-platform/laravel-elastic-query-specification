<?php

namespace Ensi\LaravelElasticQuerySpecification\Tests\Data;

use Ensi\LaravelElasticQuery\ElasticIndex;

class ProductsIndex extends ElasticIndex
{
    protected string $name = 'test_products';

    protected string $tiebreaker = 'product_id';
}