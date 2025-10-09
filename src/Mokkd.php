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

use Mokkd\Contracts\ExpectationBuilder;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\MockFactory as MockFactoryContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsEqualTo;
use Mokkd\Matchers\Comparisons\IsEqualToAnyOf;
use Mokkd\Matchers\Comparisons\IsEqualToNoneOf;
use Mokkd\Matchers\Comparisons\IsIdenticalTo;
use Mokkd\Matchers\Comparisons\IsIdenticalToAnyOf;
use Mokkd\Matchers\Comparisons\IsIdenticalToNoneOf;
use Mokkd\Matchers\Comparisons\IsNotEqualTo;
use Mokkd\Matchers\Comparisons\IsNotIdenticalTo;
use Mokkd\Matchers\Composite\MatchesAllOf;
use Mokkd\Matchers\Composite\MatchesAnyOf;
use Mokkd\Matchers\Composite\MatchesNoneOf;
use Mokkd\Matchers\Dates\IsAfter as IsDateAfter;
use Mokkd\Matchers\Dates\IsBefore as IsDateBefore;
use Mokkd\Matchers\Dates\IsBetween as IsDateBetween;
use Mokkd\Matchers\Dates\IsInMonth as IsDateInMonth;
use Mokkd\Matchers\Dates\IsInYear as IsDateInYear;
use Mokkd\Matchers\Dates\IsNotInMonth as IsDateNotInMonth;
use Mokkd\Matchers\Dates\IsNotInYear as IsDateNotInYear;
use Mokkd\Matchers\Dates\IsOnOrAfter as IsDateOnOrAfter;
use Mokkd\Matchers\Dates\IsOnOrBefore as IsDateOnOrBefore;
use Mokkd\Matchers\Dates\IsWithin as IsDateWithin;
use Mokkd\Matchers\Floats\IsBetween as IsFloatBetween;
use Mokkd\Matchers\Floats\IsEqualTo as IsFloatEqualTo;
use Mokkd\Matchers\Floats\IsGreaterThan as IsFloatGreaterThan;
use Mokkd\Matchers\Floats\IsGreaterThanOrEqualTo as IsFloatGreaterThanOrEqualTo;
use Mokkd\Matchers\Floats\IsLessThan as IsFloatLessThan;
use Mokkd\Matchers\Floats\IsLessThanOrEqualTo as IsFloatLessThanOrEqualTo;
use Mokkd\Matchers\Floats\IsNotEqualTo as IsFloatNotEqualTo;
use Mokkd\Matchers\Floats\IsWithin as IsFloatWithin;
use Mokkd\Matchers\Integers\IsBetween as IsIntBetween;
use Mokkd\Matchers\Integers\IsEqualTo as IsIntEqualTo;
use Mokkd\Matchers\Integers\IsGreaterThan as IsIntGreaterThan;
use Mokkd\Matchers\Integers\IsGreaterThanOrEqualTo as IsIntGreaterThanOrEqualTo;
use Mokkd\Matchers\Integers\IsLessThan as IsIntLessThan;
use Mokkd\Matchers\Integers\IsLessThanOrEqualTo as IsIntLessThanOrEqualTo;
use Mokkd\Matchers\Integers\IsNotEqualTo as IsIntNotEqualTo;
use Mokkd\Matchers\Integers\IsWithin as IsIntWithin;
use Mokkd\Matchers\Integers\IsZero as IsIntZero;
use Mokkd\Matchers\Integers\IsNotZero as IsNonZeroInt;
use Mokkd\Matchers\Numerics\IsNotZero as IsNonZeroNumeric;
use Mokkd\Matchers\Numerics\IsBetween as IsNumericBetween;
use Mokkd\Matchers\Numerics\IsEqualTo as IsNumericEqualTo;
use Mokkd\Matchers\Numerics\IsGreaterThan as IsNumericGreaterThan;
use Mokkd\Matchers\Numerics\IsGreaterThanOrEqualTo as IsNumericGreaterThanOrEqualTo;
use Mokkd\Matchers\Numerics\IsLessThan as IsNumericLessThan;
use Mokkd\Matchers\Numerics\IsLessThanOrEqualTo as IsNumericLessThanOrEqualTo;
use Mokkd\Matchers\Numerics\IsNotEqualTo as IsNumericNotEqualTo;
use Mokkd\Matchers\Numerics\IsWithin as IsNumericWithin;
use Mokkd\Matchers\Numerics\IsZero as IsNumericZero;
use Mokkd\Matchers\Strings\BeginsWith as IsStringBeginningWith;
use Mokkd\Matchers\Strings\Contains as IsStringContaining;
use Mokkd\Matchers\Strings\DoesNotBeginWith as IsStringNotBeginningWith;
use Mokkd\Matchers\Strings\DoesNotContain as IsStringNotContaining;
use Mokkd\Matchers\Strings\DoesNotEndWith as IsStringNotEndingWith;
use Mokkd\Matchers\Strings\DoesNotMatchPattern as IsStringNotMatching;
use Mokkd\Matchers\Strings\EndsWith as IsStringEndingWith;
use Mokkd\Matchers\Strings\IsEmpty as IsEmptyString;
use Mokkd\Matchers\Strings\IsJson as IsJsonString;
use Mokkd\Matchers\Strings\IsLongerThan as IsStringLongerThan;
use Mokkd\Matchers\Strings\IsNoLongerThan as IsStringNoLongerThan;
use Mokkd\Matchers\Strings\IsNonEmpty as IsNonEmptyString;
use Mokkd\Matchers\Strings\IsNoShorterThan as IsStringNoShorterThan;
use Mokkd\Matchers\Strings\IsOfByteLength as IsStringOfByteLength;
use Mokkd\Matchers\Strings\IsOfFewerBytesThan as IsStringOfFewerBytesThan;
use Mokkd\Matchers\Strings\IsOfLength as IsStringOfLength;
use Mokkd\Matchers\Strings\IsOfMoreBytesThan as IsStringOfMoreBytesThan;
use Mokkd\Matchers\Strings\IsOfNoFewerBytesThan as IsStringOfNoFewerBytesThan;
use Mokkd\Matchers\Strings\IsOfNoMoreBytesThan as IsStringOfNoMoreBytesThan;
use Mokkd\Matchers\Strings\IsShorterThan as IsStringShorterThan;
use Mokkd\Matchers\Strings\MatchesPattern as IsStringMatching;
use Mokkd\Matchers\Times\IsAfter as IsTimeAfter;
use Mokkd\Matchers\Times\IsBefore as IsTimeBefore;
use Mokkd\Matchers\Times\IsBetween as IsTimeBetween;
use Mokkd\Matchers\Times\IsOnOrAfter as IsTimeOnOrAfter;
use Mokkd\Matchers\Times\IsOnOrBefore as IsTimeOnOrBefore;
use Mokkd\Matchers\Times\IsWithin as IsTimeWithin;
use Mokkd\Matchers\Traversables\IsEmptyArray;
use Mokkd\Matchers\Traversables\IsNonEmptyArray;
use Mokkd\Matchers\Traversables\IsNonEmptyAssociativeArray;
use Mokkd\Matchers\Traversables\IsNonEmptyList;
use Mokkd\Matchers\Traversables\IsNonEmptyPropertyMap;
use Mokkd\Matchers\Types\IsArray;
use Mokkd\Matchers\Types\IsAssociativeArray;
use Mokkd\Matchers\Types\IsBool;
use Mokkd\Matchers\Types\IsClosedResource;
use Mokkd\Matchers\Types\IsFalse;
use Mokkd\Matchers\Types\IsFloat;
use Mokkd\Matchers\Types\IsInstanceOf;
use Mokkd\Matchers\Types\IsInt;
use Mokkd\Matchers\Types\IsList;
use Mokkd\Matchers\Types\IsNull;
use Mokkd\Matchers\Types\IsNumeric;
use Mokkd\Matchers\Types\IsObject;
use Mokkd\Matchers\Types\IsOpenResource;
use Mokkd\Matchers\Types\IsPropertyMap;
use Mokkd\Matchers\Types\IsResource;
use Mokkd\Matchers\Types\IsResourceOfType;
use Mokkd\Matchers\Types\IsString;
use Mokkd\Matchers\Types\IsTrue;
use Mokkd\MockFactory;
use Mokkd\Month;
use Mokkd\Utilities\Guard;
use Mokkd\Utilities\Serialiser;

/**
 * Static factory class for generating function mocks and matchers.
 * 
 * @method static IsEqualTo isEqualTo(mixed $expected)
 * @method static IsNotEqualTo isNotEqualTo(mixed $expected)
 * @method static IsIdenticalTo isIdenticalTo(mixed $expected)
 * @method static IsNotIdenticalTo isNotIdenticalTo(mixed $expected)
 * @method static IsEqualToAnyOf isEqualToOneOf(mixed $expected, mixed ...$otherExpected)
 * @method static IsIdenticalToAnyOf isIdenticalToOneOf(mixed $expected, mixed ...$otherExpected)
 * @method static IsEqualToNoneOf isNotEqualToAnyOf(mixed $expected, mixed ...$otherExpected)
 * @method static IsIdenticalToNoneOf isNotIdenticalToAnyOf(mixed $expected, mixed ...$otherExpected)
 *
 * @method static MatchesAllOf matchesAllOf(MatcherContract $expected, MatcherContract ...$otherExpected)
 * @method static MatchesAnyOf matchesAnyOf(MatcherContract $expected, MatcherContract ...$otherExpected)
 * @method static MatchesNoneOf matchesNoneOf(MatcherContract $expected, MatcherContract ...$otherExpected)
 *
 * @method static IsInt isInt()
 * @method static IsFloat isFloat()
 * @method static IsNumeric isNumeric()
 * @method static IsString isString()
 * @method static IsBool isBool()
 * @method static IsTrue isTrue()
 * @method static IsFalse isFalse()
 * @method static IsNull isNull()
 * @method static IsObject isObject()
 * @method static IsInstanceOf isInstanceOf(string $className)
 * @method static IsResource isResource()
 * @method static IsOpenResource isOpenResource()
 * @method static IsClosedResource isClosedResource()
 * @method static IsResourceOfType isResourceOfType(string $type)
 * @method static IsArray isArray()
 * @method static IsList isList()
 * @method static IsAssociativeArray isAssociativeArray()
 * @method static IsPropertyMap isPropertyMap()
 * 
 * @method static IsIntEqualTo isIntEqualTo(int $expected)
 * @method static IsIntNotEqualTo isIntNotEqualTo(int $expected)
 * @method static IsIntGreaterThan isIntGreaterThan(int $lowerBound)
 * @method static IsIntGreaterThanOrEqualTo isIntGreaterThanOrEqualTo(int $lowerBound)
 * @method static IsIntLessThan isIntLessThan(int $upperBound)
 * @method static IsIntLessThanOrEqualTo isIntLessThanOrEqualTo(int $upperBound)
 * @method static IsIntBetween isIntBetween(int $lowerBound, int $upperBound)
 * @method static IsIntWithin isIntWithin(int $lowerBound, int $upperBound)
 * @method static IsIntZero isIntZero()
 * @method static IsNonZeroInt isNonZeroInt()
 *
 * @method static IsFloatEqualTo isFloatEqualTo(float $expected)
 * @method static IsFloatNotEqualTo isFloatNotEqualTo(float $expected)
 * @method static IsFloatGreaterThan isFloatGreaterThan(float $lowerBound)
 * @method static IsFloatGreaterThanOrEqualTo isFloatGreaterThanOrEqualTo(float $lowerBound)
 * @method static IsFloatLessThan isFloatLessThan(float $upperBound)
 * @method static IsFloatLessThanOrEqualTo isFloatLessThanOrEqualTo(float $upperBound)
 * @method static IsFloatBetween isFloatBetween(float $lowerBound, float $upperBound)
 * @method static IsFloatWithin isFloatWithin(float $lowerBound, float $upperBound)
 *
 * @method static IsNumericEqualTo isNumericEqualTo(int|float $expected)
 * @method static IsNumericNotEqualTo isNumericNotEqualTo(int|float $expected)
 * @method static IsNumericGreaterThan isNumericGreaterThan(int|float $lowerBound)
 * @method static IsNumericGreaterThanOrEqualTo isNumericGreaterThanOrEqualTo(int|float $lowerBound)
 * @method static IsNumericLessThan isNumericLessThan(int|float $upperBound)
 * @method static IsNumericLessThanOrEqualTo isNumericLessThanOrEqualTo(int|float $upperBound)
 * @method static IsNumericBetween isNumericBetween(int|float $lowerBound, int|float $upperBound)
 * @method static IsNumericWithin isNumericWithin(int|float $lowerBound, int|float $upperBound)
 * @method static IsNumericZero isZero()
 * @method static IsNonZeroNumeric isNotZero()
 *
 * @method static IsEmptyString isEmptyString()
 * @method static IsNonEmptyString isNonEmptyString()
 * @method static IsStringBeginningWith isStringBeginningWith(string $prefix)
 * @method static IsStringNotBeginningWith isStringNotBeginningWith(string $prefix)
 * @method static IsStringEndingWith isStringEndingWith(string $suffix)
 * @method static IsStringNotEndingWith isStringNotEndingWith(string $suffix)
 * @method static IsStringContaining isStringContaining(string $infix)
 * @method static IsStringNotContaining isStringNotContaining(string $infix)
 * @method static IsStringMatching isStringMatching(string $pattern)
 * @method static IsStringNotMatching isStringNotMatching(string $pattern)
 * @method static IsStringOfLength isStringLongerThan(int $length)
 * @method static IsStringLongerThan isStringOfLength(int $length)
 * @method static IsStringShorterThan isStringShorterThan(int $length)
 * @method static IsStringNoLongerThan isStringNoLongerThan(int $length)
 * @method static IsStringNoShorterThan isStringNoShorterThan(int $length)
 * @method static IsStringOfByteLength isStringOfByteLength(int $length)
 * @method static IsStringOfMoreBytesThan isStringOfMoreBytesThan(int $length)
 * @method static IsStringOfFewerBytesThan isStringOfFewerBytesThan(int $length)
 * @method static IsStringOfNoMoreBytesThan isStringOfNoMoreBytesThan(int $length)
 * @method static IsStringOfNoFewerBytesThan isStringOfNoFewerBytesThan(int $length)
 * @method static IsJsonString isJsonString()
 *
 * @method static IsEmptyArray isEmptyArray()
 * @method static IsNonEmptyArray isNonEmptyArray()
 * @method static IsNonEmptyList isNonEmptyList()
 * @method static IsNonEmptyAssociativeArray isNonEmptyAssociativeArray()
 * @method static IsNonEmptyPropertyMap isNonEmptyPropertyMap()
 *
 * @method static IsDateBefore isDateBefore(DateTimeInterface $upperBound)
 * @method static IsDateOnOrBefore isDateOnOrBefore(DateTimeInterface $upperBound)
 * @method static IsDateAfter isDateAfter(DateTimeInterface $lowerBound)
 * @method static IsDateOnOrAfter isDateOnOrAfter(DateTimeInterface $lowerBound)
 * @method static IsDateBetween isDateBetween(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
 * @method static IsDateWithin isDateWithin(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
 * @method static IsDateInMonth isInMonth(int|Month $month, ?int $year = null)
 * @method static IsDateNotInMonth isNoInMonth(int|Month $month, ?int $year = null)
 * @method static IsDateInYear isInYear(int $year)
 * @method static IsDateNotInYear isNotInYear(int $year)
 * @method static IsTimeBefore isTimeBefore(DateTimeInterface $upperBound)
 * @method static IsTimeOnOrBefore isTimeOnOrBefore(DateTimeInterface $upperBound)
 * @method static IsTimeAfter isTimeAfter(DateTimeInterface $lowerBound)
 * @method static IsTimeOnOrAfter isTimeOnOrAfter(DateTimeInterface $lowerBound)
 * @method static IsTimeBetween isTimeBetween(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
 * @method static IsTimeWithin isTimeWithin(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
 */
class Mokkd
{
    private const MatcherFactories = [
        'isEqualTo' => IsEqualTo::class,
        "isNotEqualTo" => IsNotEqualTo::class,
        "isIdenticalTo" => IsIdenticalTo::class,
        "isNotIdenticalTo" => IsNotIdenticalTo::class,
        "isEqualToOneOf" => IsEqualToAnyOf::class,
        "isNotEqualToAnyOf" => IsEqualToNoneOf::class,
        "isIdenticalToOneOf" => IsIdenticalToAnyOf::class,
        "isNotIdenticalToAnyOf" => IsIdenticalToNoneOf::class,
        
        "matchesAllOf" => MatchesAllOf::class,
        "matchesAnyOf" => MatchesAnyOf::class,
        "matchesNoneOf" => MatchesNoneOf::class,
        
        "isInt" => IsInt::class,
        "isFloat" => IsFloat::class,
        "isNumeric" => IsNumeric::class,
        "isString" => IsString::class,
        "isBool" => IsBool::class,
        "isTrue" => IsTrue::class,
        "isFalse" => IsFalse::class,
        "isNull" => IsNull::class,
        "isObject" => IsObject::class,
        "isInstanceOf" => IsInstanceOf::class,
        "isResource" => IsResource::class,
        "isOpenResource" => IsOpenResource::class,
        "isClosedResource" => IsClosedResource::class,
        "isResourceOfType" => IsResourceOfType::class,
        "isArray" => IsArray::class,
        "isList" => IsList::class,
        "isAssociativeArray" => IsAssociativeArray::class,
        "isPropertyMap" => IsPropertyMap::class,

        "isIntEqualTo" => IsIntEqualTo::class,
        "isIntNotEqualTo" => IsIntNotEqualTo::class,
        "isIntGreaterThan" => IsIntGreaterThan::class,
        "isIntGreaterThanOrEqualTo" => IsIntGreaterThanOrEqualTo::class,
        "isIntLessThan" => IsIntLessThan::class,
        "isIntLessThanOrEqualTo" => IsIntLessThanOrEqualTo::class,
        "isIntBetween" => IsIntBetween::class,
        "isIntWithin" => IsIntWithin::class,
        "isIntZero" => IsIntZero::class,
        "isNonZeroInt" => IsNonZeroInt::class,
        
        "isFloatEqualTo" => IsFloatEqualTo::class,
        "isFloatNotEqualTo" => IsFloatNotEqualTo::class,
        "isFloatGreaterThan" => IsFloatGreaterThan::class,
        "isFloatGreaterThanOrEqualTo" => IsFloatGreaterThanOrEqualTo::class,
        "isFloatLessThan" => IsFloatLessThan::class,
        "isFloatLessThanOrEqualTo" => IsFloatLessThanOrEqualTo::class,
        "isFloatBetween" => IsFloatBetween::class,
        "isFloatWithin" => IsFloatWithin::class,
        
        "isNumericEqualTo" => IsNumericEqualTo::class,
        "isNumericNotEqualTo" => IsNumericNotEqualTo::class,
        "isNumericGreaterThan" => IsNumericGreaterThan::class,
        "isNumericGreaterThanOrEqualTo" => IsNumericGreaterThanOrEqualTo::class,
        "isNumericLessThan" => IsNumericLessThan::class,
        "isNumericLessThanOrEqualTo" => IsNumericLessThanOrEqualTo::class,
        "isNumericBetween" => IsNumericBetween::class,
        "isNumericWithin" => IsNumericWithin::class,
        "isZero" => IsNumericZero::class,
        "isNotZero" => IsNonZeroNumeric::class,
        
        "isEmptyString" => IsEmptyString::class,
        "isNonEmptyString" => IsNonEmptyString::class,
        "isStringBeginningWith" => IsStringBeginningWith::class,
        "isStringNotBeginningWith" => IsStringNotBeginningWith::class,
        "isStringEndingWith" => IsStringEndingWith::class,
        "isStringNotEndingWith" => IsStringNotEndingWith::class,
        "isStringContaining" => IsStringContaining::class,
        "isStringNotContaining" => IsStringNotContaining::class,
        "isStringMatching" => IsStringMatching::class,
        "isStringNotMatching" => IsStringNotMatching::class,
        "isStringLongerThan" => IsStringLongerThan::class,
        "isStringOfLength" => IsStringOfLength::class,
        "isStringShorterThan" => IsStringShorterThan::class,
        "isStringNoLongerThan" => IsStringNoLongerThan::class,
        "isStringNoShorterThan" => IsStringNoShorterThan::class,
        "isStringOfByteLength" => IsStringOfByteLength::class,
        "isStringOfMoreBytesThan" => IsStringOfMoreBytesThan::class,
        "isStringOfFewerBytesThan" => IsStringOfFewerBytesThan::class,
        "isStringOfNoMoreBytesThan" => IsStringOfNoMoreBytesThan::class,
        "isStringOfNoFewerBytesThan" => IsStringOfNoFewerBytesThan::class,
        "isJsonString" => IsJsonString::class,
        
        "isEmptyArray" => IsEmptyArray::class,
        "isNonEmptyArray" => IsNonEmptyArray::class,
        "isNonEmptyList" => IsNonEmptyList::class,
        "isNonEmptyAssociativeArray" => IsNonEmptyAssociativeArray::class,
        "isNonEmptyPropertyMap" => IsNonEmptyPropertyMap::class,
        
        "isDateBefore" => IsDateBefore::class,
        "isDateOnOrBefore" => IsDateOnOrBefore::class,
        "isDateAfter" => IsDateAfter::class,
        "isDateOnOrAfter" => IsDateOnOrAfter::class,
        "isDateBetween" => IsDateBetween::class,
        "isDateWithin" => IsDateWithin::class,
        "isInMonth" => IsDateInMonth::class,
        "isNotInMonth" => IsDateNotInMonth::class,
        "isInYear" => IsDateInYear::class,
        "isNotInYear" => IsDateNotInYear::class,
        "isTimeBefore" => IsTimeBefore::class,
        "isTimeOnOrBefore" => IsTimeOnOrBefore::class,
        "isTimeAfter" => IsTimeAfter::class,
        "isTimeOnOrAfter" => IsTimeOnOrAfter::class,
        "isTimeBetween" => IsTimeBetween::class,
        "isTimeWithin" => IsTimeWithin::class,
    ];

    /** @var MockFunctionContract[] */
    private static array $mocks = [];

    private static ?MockFactoryContract $mockFactory = null;

    private static ?SerialiserContract $serialiser = null;

    /**
     * Fetch a mock for a function.
     *
     * To mock a function in a namespace, provide the namespace and function name exactly as you would with an import:
     *
     *     use function MyNamespace\myFunction;
     *
     *     Mokkd::func("MyNamespace\\myFunction");
     *
     * @param string $functionName The name of the function to mock.
     */
    public static function func(string $functionName): MockFunctionContract|ExpectationBuilder
    {
        $key = strtolower($functionName);

        if (!array_key_exists($key, Mokkd::$mocks)) {
            self::$mocks[$key] = self::factory()->createMockFunction($functionName);
        }

        return self::$mocks[$key];
    }

    /** Fetch the installed mock factory. */
    public static function factory(): MockFactoryContract
    {
        if (!self::$mockFactory) {
            self::$mockFactory = new MockFactory(self::serialiser());
        }

        return self::$mockFactory;
    }

    /** Set the factory to use when new mocks need to be created. */
    public static function setFactory(MockFactoryContract $factory): void
    {
        self::$mockFactory = $factory;
    }

    /** Fetch the installed serialiser. */
    public static function serialiser(): SerialiserContract
    {
        if (!self::$serialiser) {
            self::$serialiser = new Serialiser();
        }

        return self::$serialiser;
    }

    /** Install a new serialiser. */
    public static function setSerialiser(SerialiserContract $serialiser): void
    {
        self::$serialiser = $serialiser;
    }

    /** Close the mock session */
    public static function close(): void
    {
        // ensure everything is cleaned up no matter how we exit this method
        $guard = new Guard(static function () {
            self::$mocks = [];
            self::$mockFactory = null;
            self::$serialiser = null;
        });

        foreach (self::$mocks as $mock) {
            $mock->uninstall();
        }

        foreach (self::$mocks as $mock) {
            $mock->verifyExpectations();
        }
    }

    public static function __callStatic(string $functionName, array $arguments): MatcherContract
    {
        $matcherClass = self::MatcherFactories[$functionName] ?? null;

        if (null !== $matcherClass) {
            return new $matcherClass(...$arguments);
        }

        throw new BadMethodCallException("Static method {$functionName} does not exist");
    }
}
