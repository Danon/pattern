<?php
namespace Test\Utils;

use PHPUnit\Framework\Assert;
use Throwable;

class Functions
{
    public static function any(): callable
    {
        return function () {
        };
    }

    public static function identity(): callable
    {
        return function ($arg) {
            return $arg;
        };
    }

    public static function constant($value): callable
    {
        return function () use ($value) {
            return $value;
        };
    }

    public static function throws(Throwable $throwable)
    {
        return function () use ($throwable) {
            throw $throwable;
        };
    }

    public static function fail(string $message = null): callable
    {
        return function () use ($message) {
            Assert::fail($message ?? 'Failed to assert that callback is not invoked');
        };
    }

    public static function singleArg(callable $callable): callable
    {
        return function ($argument) use ($callable) { // ignore remaining arguments
            return $callable($argument);
        };
    }
}
