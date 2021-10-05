<?php

namespace Ensi\LaravelElasticQuerySpecification;

use Illuminate\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ElasticQuerySpecificationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-elastic-query-specification')
            ->hasConfigFile();

        $this->app->bind(
            QueryBuilderRequest::class,
            fn(Application $app) => QueryBuilderRequest::fromRequest($app['request'])
        );
    }

    public function provides(): array
    {
        return [
            QueryBuilderRequest::class,
        ];
    }
}
