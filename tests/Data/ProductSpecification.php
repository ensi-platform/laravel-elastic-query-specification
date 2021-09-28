<?php

namespace Greensight\LaravelElasticQuerySpecification\Tests\Data;

use Greensight\LaravelElasticQuerySpecification\Specification\CompositeSpecification;

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