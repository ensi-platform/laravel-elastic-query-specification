<?php

namespace Ensi\LaravelElasticQuerySpecification\Tests;

use Ensi\LaravelElasticQuery\ElasticQuery;
use Ensi\LaravelElasticQuery\ElasticQueryServiceProvider;
use Ensi\LaravelElasticQuerySpecification\ElasticQuerySpecificationServiceProvider;
use Ensi\LaravelElasticQuerySpecification\Tests\Data\ProductIndexSeeder;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        ProductIndexSeeder::run();
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

    public function getEnvironmentSetUp($app): void
    {
        config()->set('laravel-elastic-query.connection', [
            'hosts' => explode(',', env('ELASTICSEARCH_HOSTS')),
            'username' => env('ELASTICSEARCH_USERNAME', ''),
            'password' => env('ELASTICSEARCH_PASSWORD', ''),
        ]);
        config()->set('tests.recreate_index', env('RECREATE_INDEX', true));
    }
}
