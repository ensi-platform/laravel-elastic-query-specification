<?php

namespace Ensi\LaravelElasticQuerySpecification\Exceptions;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidQueryException extends HttpException
{
    public static function notAllowedFilters(Collection $filters): self
    {
        $aliases = $filters->implode(', ');
        $message = "Requested filters \"$aliases\" are not allowed.";

        return new self(Response::HTTP_BAD_REQUEST, $message);
    }

    public static function notAllowedSorts(Collection $sorts): self
    {
        $aliases = $sorts->implode(', ');
        $message = "Requested sorts \"$aliases\" are not allowed";

        return new self(Response::HTTP_BAD_REQUEST, $message);
    }

    public static function notAllowedAggregates(Collection $aggregates): self
    {
        $aliases = $aggregates->implode(', ');
        $message = "Requested aggregates \"$aliases\" are not allowed";

        return new self(Response::HTTP_BAD_REQUEST, $message);
    }
}