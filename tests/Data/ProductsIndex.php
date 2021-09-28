<?php

namespace Greensight\LaravelElasticQuerySpecification\Tests\Data;

use Greensight\LaravelElasticQuery\ElasticIndex;

class ProductsIndex extends ElasticIndex
{
    protected string $name = 'test_products';

    protected string $tiebreaker = 'product_id';
}