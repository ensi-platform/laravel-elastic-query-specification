<?php

namespace Ensi\LaravelElasticQuerySpecification\Contracts;

use Illuminate\Support\Collection;

interface QueryParameters
{
    public function filters(): Collection;

    public function sorts(): Collection;

    public function aggregates(): Collection;
}
