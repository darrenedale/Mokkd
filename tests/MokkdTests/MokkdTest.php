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

namespace MokkdTests;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Mokkd;
use Mokkd\Matchers\Comparisons\IsEqualTo;
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
use Mokkd\Matchers\Dates\IsInMonth;
use Mokkd\Matchers\Dates\IsInYear;
use Mokkd\Matchers\Dates\IsNotInMonth;
use Mokkd\Matchers\Dates\IsNotInYear;
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
use Mokkd\Matchers\Integers\IsNotZero as IsNonZeroInt;
use Mokkd\Matchers\Integers\IsWithin as IsIntWithin;
use Mokkd\Matchers\Integers\IsZero as IsIntZero;
use Mokkd\Matchers\Numerics\IsBetween as IsNumericBetween;
use Mokkd\Matchers\Numerics\IsEqualTo as IsNumericEqualTo;
use Mokkd\Matchers\Numerics\IsGreaterThan as IsNumericGreaterThan;
use Mokkd\Matchers\Numerics\IsGreaterThanOrEqualTo as IsNumericGreaterThanOrEqualTo;
use Mokkd\Matchers\Numerics\IsLessThan as IsNumericLessThan;
use Mokkd\Matchers\Numerics\IsLessThanOrEqualTo as IsNumericLessThanOrEqualTo;
use Mokkd\Matchers\Numerics\IsNotEqualTo as IsNumericNotEqualTo;
use Mokkd\Matchers\Numerics\IsNotZero as IsNonZeroNumeric;
use Mokkd\Matchers\Numerics\IsWithin as IsNumericWithin;
use Mokkd\Matchers\Numerics\IsZero as IsNumericZero;
use Mokkd\Matchers\Strings\BeginsWith as IsStringBeginningWith;
use Mokkd\Matchers\Strings\Contains as IsStringContaining;
use Mokkd\Matchers\Strings\DoesNotBeginWith as IsStringNotBeginningWith;
use Mokkd\Matchers\Strings\DoesNotContain as IsSringNotContaining;
use Mokkd\Matchers\Strings\DoesNotEndWith as IsSringNotEndingWith;
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
use PHPUnit\Framework\Attributes\DataProvider;

class MokkdTest extends TestCase
{
    /** Helper to create a DateTimeInterface object with a given UTC date. */
    private static function createDate(int $year, int $month, int $day): DateTimeInterface
    {
        return DateTime::createFromFormat(
            "Y-m-d H:i:s",
            str_pad("{$year}", 4, "0", STR_PAD_LEFT) .
            "-" .
            str_pad("{$month}", 2, "0", STR_PAD_LEFT) .
            "-" .
            str_pad("{$day}", 2, "0", STR_PAD_LEFT) .
            " 00:00:00",
            new DateTimeZone("UTC"),
        );
    }

    /** Helper to create a DateTimeInterface object with a given UTC time on 1st Jan, 2025. */
    private static function createTime(int $hour, int $minute, int $second): DateTimeInterface
    {
        return DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2025-01-01 " .
            str_pad("{$hour}", 2, "0", STR_PAD_LEFT) .
            ":" .
            str_pad("{$minute}", 2, "0", STR_PAD_LEFT) .
            ":" .
            str_pad("{$second}", 2, "0", STR_PAD_LEFT),
            new DateTimeZone("UTC"),
        );
    }

    public static function providerTestMatcherFactory1(): iterable
    {
        // method name, matcher class, matching value, mismatching value, ... arguments for method
        yield "isEqualTo1" => ["isEqualTo", IsEqualTo::class, "1", 0, 1,];
        yield "isNotEqualTo1" => ["isNotEqualTo", IsNotEqualTo::class, "0", "1", 1,];
        yield "isIdenticalTo1" => ["isIdenticalTo", IsIdenticalTo::class, 1, "1", 1,];
        yield "isNotIdenticalTo1" => ["isNotIdenticalTo", IsNotIdenticalTo::class, "1", 1, 1,];
        yield "isIdenticalToOneOf1" => ["isIdenticalToOneOf", IsIdenticalToAnyOf::class, 1, 3, 0, 1, 2,];
        yield "isNotIdenticalToAnyOf1" => ["isNotIdenticalToAnyOf", IsIdenticalToNoneOf::class, -1, 1, 1, 2, 3,];
        yield "matchesAllOf1" => ["matchesAllOf", MatchesAllOf::class, 1, 2, Mokkd::isEqualTo(1), Mokkd::isNotEqualTo(2),];
        yield "matchesAnyOf1" => ["matchesAnyOf", MatchesAnyOf::class, 2, 3, Mokkd::isEqualTo(1), Mokkd::isEqualTo(2),];
        yield "matchesNoneOf1" => ["matchesNoneOf", MatchesNoneOf::class, 3, 2, Mokkd::isEqualTo(2), Mokkd::isEqualTo(1),];

        yield "isInt1" => ["isInt", IsInt::class, 3, "2",];
        yield "isFloat1" => ["isFloat", IsFloat::class, 3.14, "3.14",];
        yield "isNumeric1" => ["isNumeric", IsNumeric::class, 42, "42",];
        yield "isString1" => ["isString", IsString::class, "42", 42,];
        yield "isBool1" => ["isBool", IsBool::class, true, -1,];
        yield "isTrue1" => ["isTrue", IsTrue::class, true, false,];
        yield "isFalse1" => ["isFalse", IsFalse::class, false, true,];
        yield "isNull1" => ["isNull", IsNull::class, null, 0,];
        yield "isObject1" => ["isObject", IsObject::class, new class(){}, null,];
        yield "isInstanceOf1" => ["isInstanceOf", IsInstanceOf::class, new IsNull(), null, IsNull::class,];
        
        $resource = fopen("php://memory", "r+");
        yield "isResource1" => ["isResource", IsResource::class, $resource, null,];
        yield "isOpenResource1" => ["isOpenResource", IsOpenResource::class, $resource, null,];
        yield "isResourceOfType1" => ["isResourceOfType", IsResourceOfType::class, $resource, null, "stream",];

        $resource = fopen("php://memory", "r+");
        fclose($resource);
        yield "isClosedResource1" => ["isClosedResource", IsClosedResource::class, $resource, null,];

        yield "isArray1" => ["isArray", IsArray::class, [1, 2, 3], "Mokkd",];
        yield "isList1" => ["isList", IsList::class, [4, 5, 6], [4, 5, "Mokkd" => "function",],];
        yield "isAssociativeArray1" => ["isAssociativeArray", IsAssociativeArray::class, ["first" => "Mokkd", "second" => "function", 3 => null], [7, 8, 9,],];
        yield "isPropertyMap1" => ["isPropertyMap", IsPropertyMap::class, ["first" => "Mokkd", "second" => "function", "third" => null], ["first" => "Mokkd", "second" => "function", 3 => null,],];

        yield "isIntEqualTo1" => ["isIntEqualTo", IsIntEqualTo::class, 1, 2, 1,];
        yield "isIntNotEqualTo1" => ["isIntNotEqualTo", IsIntNotEqualTo::class, 2, 1, 1,];
        yield "isIntGreaterThan1" => ["isIntGreaterThan", IsIntGreaterThan::class, 2, 1, 1,];
        yield "isIntGreaterThanOrEqualTo1" => ["isIntGreaterThanOrEqualTo", IsIntGreaterThanOrEqualTo::class, 1, 0, 1,];
        yield "isIntLessThan1" => ["isIntLessThan", IsIntLessThan::class, 0, 1, 1,];
        yield "isIntLessThanOrEqualTo1" => ["isIntLessThanOrEqualTo", IsIntLessThanOrEqualTo::class, 1, 2, 1,];
        yield "isIntBetween1" => ["isIntBetween", IsIntBetween::class, 1, 0, 1, 3,];
        yield "isIntWithin1" => ["isIntWithin", IsIntWithin::class, 2, 3, 1, 3,];
        yield "isIntZero1" => ["isIntZero", IsIntZero::class, 0, -1,];
        yield "isNonZeroInt1" => ["isNonZeroInt", IsNonZeroInt::class, -1, 0,];

        yield "isFloatEqualTo1" => ["isFloatEqualTo", IsFloatEqualTo::class, 3.14, 3.13, 3.14,];
        yield "isFloatNotEqualTo1" => ["isFloatNotEqualTo", IsFloatNotEqualTo::class, 3.13, 3.14, 3.14,];
        yield "isFloatGreaterThan1" => ["isFloatGreaterThan", IsFloatGreaterThan::class, 3.15, 3.13, 3.14,];
        yield "isFloatGreaterThanOrEqualTo1" => ["isFloatGreaterThanOrEqualTo", IsFloatGreaterThanOrEqualTo::class, 3.14, 3.13, 3.14,];
        yield "isFloatLessThan1" => ["isFloatLessThan", IsFloatLessThan::class, 3.13, 3.14, 3.14,];
        yield "isFloatLessThanOrEqualTo1" => ["isFloatLessThanOrEqualTo", IsFloatLessThanOrEqualTo::class, 3.14, 3.15, 3.14,];
        yield "isFloatBetween1" => ["isFloatBetween", IsFloatBetween::class, 3.15, 3.16, 3.13, 3.15,];
        yield "isFloatWithin1" => ["isFloatWithin", IsFloatWithin::class, 3.14, 3.15, 3.13, 3.15,];

        yield "isNumericEqualTo1" => ["isNumericEqualTo", IsNumericEqualTo::class, 1, 2, 1,];
        yield "isNumericNotEqualTo1" => ["isNumericNotEqualTo", IsNumericNotEqualTo::class, 2, 1, 1,];
        yield "isNumericGreaterThan1" => ["isNumericGreaterThan", IsNumericGreaterThan::class, 2, 1, 1,];
        yield "isNumericGreaterThanOrEqualTo1" => ["isNumericGreaterThanOrEqualTo", IsNumericGreaterThanOrEqualTo::class, 1, 0, 1,];
        yield "isNumericLessThan1" => ["isNumericLessThan", IsNumericLessThan::class, 0, 1, 1,];
        yield "isNumericLessThanOrEqualTo1" => ["isNumericLessThanOrEqualTo", IsNumericLessThanOrEqualTo::class, 1, 2, 1,];
        yield "isNumericBetween1" => ["isNumericBetween", IsNumericBetween::class, 1, 0, 1, 3,];
        yield "isNumericWithin1" => ["isNumericWithin", IsNumericWithin::class, 2, 3, 1, 3,];
        yield "isZero1" => ["isZero", IsNumericZero::class, 0, -1,];
        yield "isNotZero1" => ["isNotZero", IsNonZeroNumeric::class, -1, 0,];

        yield "isEmptyString1" => ["isEmptyString", IsEmptyString::class, "", " ",];
        yield "isNonEmptyString1" => ["isNonEmptyString", IsNonEmptyString::class, " ", "",];
        yield "isStringBeginningWith1" => ["isStringBeginningWith", IsStringBeginningWith::class, "Mokkd function", "Not Mokkd function", "Mokkd",];
        yield "isStringNotBeginningWith1" => ["isStringNotBeginningWith", IsStringNotBeginningWith::class, "Not Mokkd function", "Mokkd function", "Mokkd",];
        yield "isStringEndingWith1" => ["isStringEndingWith", IsStringEndingWith::class, "function Mokkd", "Not Mokkd function", "Mokkd",];
        yield "isStringNotEndingWith1" => ["isStringNotEndingWith", IsSringNotEndingWith::class, "Not Mokkd function", "function Mokkd", "Mokkd",];
        yield "isStringContaining1" => ["isStringContaining", IsStringContaining::class, "A Mokkd function", "The original function", "Mokkd",];
        yield "isStringNotContaining1" => ["isStringNotContaining", IsSringNotContaining::class, "The original function", "A Mokkd function", "Mokkd",];

        // TODO these two pass but PHPUnit complains about an error handler not being removed - is ereg installing one?
//        yield "isStringMatching1" => ["isStringMatching", IsStringMatching::class, "Mokkd function", "Not Mokkd function", "^Mokkd function\$",];
//        yield "isStringNotMatching1" => ["isStringNotMatching", IsSringNotMatching::class, "Not Mokkd function", "Mokkd function", "^Mokkd function\$",];

        yield "isStringLongerThan1" => ["isStringLongerThan", IsStringLongerThan::class, "The Mokkd function", "Mokkd function", 14,];
        yield "isStringNoLongerThan1" => ["isStringNoLongerThan", IsStringNoLongerThan::class, "Mokkd function", "The Mokkd function", 14,];
        yield "isStringShorterThan1" => ["isStringShorterThan", IsStringShorterThan::class, "Mokkd function", "The Mokkd function", 15,];
        yield "isStringNoShorterThan1" => ["isStringNoShorterThan", IsStringNoShorterThan::class, "Mokkd function", "The Mokkd", 14,];
        yield "isStringOfLength1" => ["isStringOfLength", IsStringOfLength::class, "Mokkd function", "The Mokkd function", 14,];
        yield "isStringOfByteLength1" => ["isStringOfByteLength", IsStringOfByteLength::class, "Mokkd function", "The Mokkd function", 14,];
        yield "isStringOfMoreBytesThan1" => ["isStringOfMoreBytesThan", IsStringOfMoreBytesThan::class, "The Mokkd function", "Mokkd function", 14,];
        yield "isStringOfFewerBytesThan1" => ["isStringOfFewerBytesThan", IsStringOfFewerBytesThan::class, "Mokkd", "Mokkd function", 14,];
        yield "isStringOfNoMoreBytesThan1" => ["isStringOfNoMoreBytesThan", IsStringOfNoMoreBytesThan::class, "Mokkd function", "The Mokkd function", 14,];
        yield "isStringOfNoFewerBytesThan1" => ["isStringOfNoFewerBytesThan", IsStringOfNoFewerBytesThan::class, "Mokkd function", "Mokkd", 14,];
        yield "isJsonString1" => ["isJsonString", IsJsonString::class, "{}", "Mokkd",];

        yield "isEmptyArray1" => ["isEmptyArray", IsEmptyArray::class, [], ["Mokkd",],];
        yield "isNonEmptyArray1" => ["isNonEmptyArray", IsNonEmptyArray::class, ["Mokkd",], [],];
        yield "isNonEmptyList1" => ["isNonEmptyList", IsNonEmptyList::class, ["Mokkd",], [],];
        yield "isNonEmptyAssociativeArray1" => ["isNonEmptyAssociativeArray", IsNonEmptyAssociativeArray::class, ["function" => "Mokkd",], [],];
        yield "isNonEmptyPropertyMap1" => ["isNonEmptyPropertyMap", IsNonEmptyPropertyMap::class, ["function" => "Mokkd",], [],];

        yield "isDateBefore1" => [
            "isDateBefore",
            IsDateBefore::class,
            self::createDate(2025, 1, 1),
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 2),
        ];

        yield "isDateOnOrBefore1" => [
            "isDateOnOrBefore",
            IsDateOnOrBefore::class,
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 3),
            self::createDate(2025, 1, 2),
        ];

        yield "isDateAfter1" => [
            "isDateAfter",
            IsDateAfter::class,
            self::createDate(2025, 1, 3),
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 2),
        ];

        yield "isDateOnOrAfter1" => [
            "isDateOnOrAfter",
            IsDateOnOrAfter::class,
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 1),
            self::createDate(2025, 1, 2),
        ];

        yield "isDateBetween1" => [
            "isDateBetween",
            IsDateBetween::class,
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 1),
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 4),
        ];

        yield "isDateWithin1" => [
            "isDateWithin",
            IsDateWithin::class,
            self::createDate(2025, 1, 2),
            self::createDate(2025, 1, 1),
            self::createDate(2025, 1, 1),
            self::createDate(2025, 1, 3),
        ];

        yield "isInMonth1" => [
            "isInMonth",
            IsInMonth::class,
            self::createDate(2025, 1, 1),
            self::createDate(2025, 2, 1),
            1,
        ];

        yield "isNotInMonth1" => [
            "isNotInMonth",
            IsNotInMonth::class,
            self::createDate(2025, 2, 1),
            self::createDate(2025, 1, 1),
            1,
        ];

        yield "isInYear1" => [
            "isInYear",
            IsInYear::class,
            self::createDate(2025, 1, 1),
            self::createDate(2026, 1, 1),
            2025,
        ];

        yield "isNotInYear1" => [
            "isNotInYear",
            IsNotInYear::class,
            self::createDate(2026, 1, 1),
            self::createDate(2025, 1, 1),
            2025,
        ];

        yield "isTimeBefore1" => [
            "isTimeBefore",
            IsTimeBefore::class,
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 1),
            self::createTime(10, 0, 1),
        ];

        yield "isTimeOnOrBefore1" => [
            "isTimeOnOrBefore",
            IsTimeOnOrBefore::class,
            self::createTime(10, 0, 1),
            self::createTime(10, 0, 2),
            self::createTime(10, 0, 1),
        ];

        yield "isTimeAfter1" => [
            "isTimeAfter",
            IsTimeAfter::class,
            self::createTime(10, 0, 1),
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 0),
        ];

        yield "isTimeOnOrAfter1" => [
            "isTimeOnOrAfter",
            IsTimeOnOrAfter::class,
            self::createTime(10, 0, 1),
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 1),
        ];

        yield "isTimeBetween1" => [
            "isTimeBetween",
            IsTimeBetween::class,
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 3),
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 2),
        ];

        yield "isTimeWithin1" => [
            "isTimeWithin",
            IsTimeWithin::class,
            self::createTime(10, 0, 1),
            self::createTime(10, 0, 2),
            self::createTime(10, 0, 0),
            self::createTime(10, 0, 2),
        ];
    }

    /** Ensure the matcher factories produce correctly-configured matcher instances. */
    #[DataProvider("providerTestMatcherFactory1")]
    public function testMatcherFactory1(string $method, string $expectedClass, mixed $matches, mixed $mismatch, mixed ... $args): void
    {
        $actual = [Mokkd::class, $method](...$args);
        self::assertInstanceOf($expectedClass, $actual);
        self::assertTrue($actual->matches($matches));
        self::assertFalse($actual->matches($mismatch));
    }

}
