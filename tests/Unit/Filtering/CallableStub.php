<?php

namespace Ensi\LaravelElasticQuerySpecification\Tests\Unit\Filtering;

use Mockery;
use Mockery\MockInterface;

class CallableStub
{
    public function __invoke(): void
    {
    }

    public static function shouldCall(): MockInterface|self
    {
        return expectInvoke(self::class, 1);
    }

    public static function shouldCallWith(mixed ...$parameters): MockInterface|self
    {
        return expectInvoke(self::class, 1, ...$parameters);
    }

    public static function shouldNotCall(): MockInterface|self
    {
        $mock = Mockery::mock(self::class);
        $mock->expects('__invoke')->never();

        return $mock;
    }
}
