<?php

namespace Ensi\LaravelElasticQuerySpecification\Filtering;

final class FilterValue
{
    protected function __construct(private mixed $value, private bool $disposed)
    {
    }

    public static function make(mixed $value): self
    {
        return new self(self::normalizeValue($value), false);
    }

    private static function normalizeValue(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $normalized = array_filter($value);
        if (!$normalized) {
            return null;
        }

        return count($normalized) === 1 ? head($normalized) : array_values($normalized);
    }

    public function when(bool $condition, callable $callback): self
    {
        if ($this->disposed || !$condition) {
            return $this;
        }

        $callback($this->value);

        return new self($this->value, true);
    }

    public function whenMultiple(callable $callback): self
    {
        return $this->when(is_array($this->value), $callback);
    }

    public function whenSingle(callable $callback): self
    {
        return $this->when(!is_array($this->value), $callback);
    }

    public function whenSame(mixed $sample, callable $callback): self
    {
        return $this->when($this->value === $sample, $callback);
    }

    public function orElse(callable $callback): self
    {
        return $this->when(true, $callback);
    }
}
