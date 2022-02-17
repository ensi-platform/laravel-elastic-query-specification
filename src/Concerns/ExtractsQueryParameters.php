<?php

namespace Ensi\LaravelElasticQuerySpecification\Concerns;

use Illuminate\Support\Collection;

trait ExtractsQueryParameters
{
    abstract protected function extract(string $key): mixed;

    public function filters(): Collection
    {
        $key = $this->config('filter');
        $values = $this->extractArray($key);

        return collect($this->getFilterValue($values));
    }

    public function sorts(): Collection
    {
        $key = $this->config('sort');

        return $this->extractNames($key);
    }

    public function aggregates(): Collection
    {
        $key = $this->config('aggregate');

        return $this->extractNames($key);
    }

    protected function extractNames(string $key): Collection
    {
        return collect($this->extractArray($key))
            ->map(fn (string $name) => trim($name))
            ->filter(fn (string $name) => !blank($name))
            ->values();
    }

    protected function extractArray(string $key): array
    {
        $result = $this->extract($key);

        if ($result === null) {
            return [];
        }

        if (is_string($result)) {
            return explode(',', $result);
        }

        return (array)$result;
    }

    protected function getFilterValue(mixed $source): mixed
    {
        if (is_array($source)) {
            return array_map(fn ($item) => $this->getFilterValue($item), $source);
        }

        if ($source === 'true') {
            return true;
        }

        if ($source === 'false') {
            return false;
        }

        return $source;
    }

    protected function config(string $key, mixed $default = null): mixed
    {
        return config('laravel-elastic-query.parameters.'.$key, $default ?? $key);
    }
}
