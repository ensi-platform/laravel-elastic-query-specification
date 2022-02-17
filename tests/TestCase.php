<?php

namespace Ensi\LaravelElasticQuerySpecification\Tests;

use Ensi\LaravelElasticQuery\ElasticQuery;
use Ensi\LaravelElasticQuery\ElasticQueryServiceProvider;
use Ensi\LaravelElasticQuerySpecification\ElasticQuerySpecificationServiceProvider;
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
        config()->set('tests.recreate_index', env('RECREATE_INDEX', true));
    }
}
