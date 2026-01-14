<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace MokkdTests\Matchers;

use Mokkd\Matchers\Any;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Any::class)]
class AnyTest extends TestCase
{
    use CreatesNullSerialiser;

    private Any $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Any();
    }

    protected function tearDown(): void
    {
        unset($this->matcher);
    }

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::integers();
    }

    /** Ensure a reasonable subset of ints match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::floats();
    }

    /** Ensure a reasonable subset of floats match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::strings();
    }

    /** Ensure a reasonable subset of strings match successfully. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(string $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield from DataFactory::arrays();
    }

    /** Ensure a reasonable subset of arrays match successfully. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(array $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches5(): iterable
    {
        yield from DataFactory::objects();
    }

    /** Ensure a reasonable subset of objects match successfully. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(object $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches6(): iterable
    {
        yield from DataFactory::booleans();
    }

    /** Ensure all boolean values match successfully. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(bool $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches7(): iterable
    {
        yield from DataFactory::callables();
    }

    /** Ensure callable values match successfully. */
    #[DataProvider("dataForTestMatches7")]
    public function testMatches7(callable $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    public static function dataForTestMatches8(): iterable
    {
        yield from DataFactory::resources();
    }

    /** Ensure resources match successfully. */
    #[DataProvider("dataForTestMatches8")]
    public function testMatches8(mixed $value): void
    {
        self::assertTrue($this->matcher->matches($value));
    }

    /** Ensure null matches. */
    public function testMatches9(): void
    {
        self::assertTrue($this->matcher->matches(null));
    }

    /** Ensure the matcher describes itself correctly. */
    public function testDescribe1(): void
    {
        self::assertSame("{any}", $this->matcher->describe(self::nullSerialiser()));
    }
}
