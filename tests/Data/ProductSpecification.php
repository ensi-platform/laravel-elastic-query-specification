<?php

namespace Ensi\LaravelElasticQuerySpecification\Tests\Data;

use Ensi\LaravelElasticQuerySpecification\Specification\CompositeSpecification;

class ProductSpecification extends CompositeSpecification
{
    public function __construct()
    {
        parent::__construct();

        $this->allowedFilters([
            'code',
            'package',
            'tags',
        ]);
    }
}