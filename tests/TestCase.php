<?php

namespace Greensight\LaravelElasticQuerySpecification\Tests;

use Greensight\LaravelElasticQuery\ElasticQuery;
use Greensight\LaravelElasticQuery\ElasticQueryServiceProvider;
use Greensight\LaravelElasticQuerySpecification\ElasticQuerySpecificationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        ElasticQuery::disableQueryLog();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ElasticQueryServiceProvider::class,
            ElasticQuerySpecificationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('laravel-elastic-query.connection.hosts', explode(',', env('ELASTICSEARCH_HOSTS')));
    }
}
