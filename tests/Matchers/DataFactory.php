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

use DateTime;
use DateTimeImmutable;
use LogicException;
use Mokkd\Utilities\IterableAlgorithms;
use MokkdTests\TestCase;
use stdClass;

use function array_shift;
use function assert;
use function base64_encode;
use function count;
use function is_callable;
use function is_array;
use function fopen;
use function iterator_to_array;
use function range;
use function str_replace;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

class DataFactory
{
    /** Values paired with others that are identical. */
    public static function identicalValues(): iterable
    {
        yield "identical-strings" => ["mokkd", "mokkd"];
        yield "identical-ints" => [42, 42];
        yield "identical-floats" => [3.1415927, 3.1415927];
        yield "identical-null" => [null, null];
        yield "identical-arrays" => [[1, 2, "mokkd", 3], [1, 2, "mokkd", 3]];
        yield "identical-true" => [true, true];
        yield "identical-false" => [false, false];

        // 2025-04-01 22:42:03
        $object = new stdClass();
        yield "identical-objects" => [$object, $object];

        // 2025-04-01 22:42:03
        $dateTime = new DateTime("@1743547729");
        yield "identical-date-times" => [$dateTime, $dateTime];

        $streamDataset = iterator_to_array(self::stream());
        yield from self::relabel(
            self::combine($streamDataset, $streamDataset),
            "identical-",
        RelabelMode::Prefix,
        );
    }

    /** Values paired with others that are not identical. */
    public static function nonIdenticalValues(): iterable
    {
        yield from self::relabel(
            self::unequalValues(),
            static fn(string $label): string => str_replace("unequal-", "non-identical-", $label),
            RelabelMode::Callback,
        );

        $object = new stdClass();
        yield "non-identical-object-and-clone" => [$object, clone $object];

        // 2025-04-01 22:50:30
        yield "non-identical-date-time-object-and-equal-other-object" => [
            new DateTime("@1743547830"),
            new DateTime("@1743547830"),
        ];
    }

    public static function equalValues(): iterable
    {
        yield from self::relabel(
            self::identicalValues(),
            static fn(string $label): string => str_replace("identical-", "equal-", $label),
            RelabelMode::Callback,
        );

        yield "equal-int-and-int-string" => [42, "42"];
        yield "equal-int-string-and-int" => ["42", 42];
        yield "equal-int-and-float" => [42, 42.0];
        yield "equal-float-and-int" => [42.0, 42];
        yield "equal-float-string-and-float" => ["3.1415927", 3.1415927];
        yield "equal-float-and-float-string" => [3.1415927, "3.1415927"];

        yield "equal-null-and-empty-string" => [null, ""];
        yield "equal-empty-string-and-null" => ["", null];
        yield "equal-null-and-zero-int" => [null, 0];
        yield "equal-zero-int-and-null" => [0, null];
        yield "equal-null-and-zero-float" => [null, 0.0];
        yield "equal-zero-float-and-null" => [0.0, null];
        yield "equal-null-and-false" => [null, false];
        yield "equal-false-and-null" => [false, null];

        yield "equal-false-and-zero-int" => [false, 0];
        yield "equal-zero-int-and-false" => [0, false];
        yield "equal-false-and-zero-float" => [false, 0.0];
        yield "equal-zero-float-and-false" => [0.0, false];
        yield "equal-false-and-empty-string" => [false, ""];
        yield "equal-empty-string-and-false" => ["", false];
        yield "equal-false-and-zero-int-string" => [false, "0"];
        yield "equal-zero-int-string-and-false" => ["0", false];

        yield "equal-true-and-non-zero-int" => [true, 1];
        yield "equal-non-zero-int-and-true" => [1, true];
        yield "equal-true-and-non-zero-float" => [true, 1.1];
        yield "equal-non-zero-float-and-true" => [1.1, true];
        yield "equal-true-and-non-empty-string" => [true, "x"];
        yield "equal-non-empty-string-and-true" => ["x", true];
        yield "equal-true-and-non-zero-int-string" => [true, "1"];
        yield "equal-non-zero-int-string-and-true" => ["1", true];
        yield "equal-true-and-non-zero-float-string" => [true, "1.1"];
        yield "equal-non-zero-float-string-and-true" => ["1.1", true];
        yield "equal-objects" => [new StdClass(), new StdClass()];

        // 2025-04-01 22:42:03
        yield "equal-date-times" => [
            new DateTime("@1743547323"),
            new DateTime("@1743547323"),
        ];

        // 2025-04-01 22:47:19
        yield "equal-date-time-and-date-time-immutable" => [
            new DateTime("@1743547639"),
            new DateTimeImmutable("@1743547639"),
        ];

        yield "equal-arrays" => [[1, "2", "mokkd", 3], ["1", 2, "mokkd", 3]];
    }

    public static function unequalValues(): iterable
    {
        yield "unequal-int-and-string" => [42, "forty-two"];
        yield "unequal-string-and-int" => ["forty-two", 42];
        yield "unequal-int-and-float" => [42, 42.1];
        yield "unequal-float-and-int" => [42.1, 42];
        yield "unequal-int-and-int" => [42, 41];

        yield "unequal-float-and-float" => [42.1, 42.0];
        yield "unequal-string-and-float" => ["3.1415926", 3.1415927];
        yield "unequal-float-and-string" => [3.1415926, "3.1415927"];

        yield "unequal-null-and-string" => [null, "mokkd"];
        yield "unequal-string-and-null" => ["mokkd", null];
        yield "unequal-null-and-int" => [null, 1];
        yield "unequal-int-and-null" => [-1, null];
        yield "unequal-null-and-float" => [null, 0.1];
        yield "unequal-float-and-null" => [-0.1, null];

        yield "unequal-false-and-int" => [false, 1];
        yield "unequal-int-and-false" => [1, false];
        yield "unequal-false-and-float" => [false, 0.1];
        yield "unequal-zero-and-float" => [-0.1, false];
        yield "unequal-false-and-string" => [false, "mokkd"];
        yield "unequal-string-and-false" => ["mokkd", false];

        yield "unequal-true-and-int" => [true, 0];
        yield "unequal-int-and-true" => [0, true];
        yield "unequal-true-and-float" => [true, 0.0];
        yield "unequal-float-and-true" => [0.0, true];
        yield "unequal-true-and-empty-string" => [true, ""];
        yield "unequal-empty-string-and-true" => ["", true];

        yield "unequal-arrays" => [[1, "2", "mokkd", 3], ["2", 1, "mokkd", 3]];

        // if PHP were consistent, these would be equal (just as "0" equals false); but it's not ...
        yield "unequal-false-and-zero-float-string" => [false, "0.0"];
        yield "unequal-zero-float-string-and-false" => ["0.0", false];

        // 2025-04-01 22:40:28, 2025-04-01 22:40:29
        yield "unequal-date-times" => [new DateTime("@1743547228"), new DateTime("@1743547229")];

        yield "unequal-arrays-differently-ordered" => [[1, 0], [0, 1]];
    }

    //
    // null
    //
    
    public static function nullValue(): iterable
    {
        yield "null" => [null];
    }

    
    //
    // arrays
    //

    public static function emptyArray(): iterable
    {
        yield "array-empty" => [[]];
    }

    public static function mixedListArray(): iterable
    {
        yield "array-mixed" => [
            [
                "func",
                true,
                null,
                3.1415927,
                [],
                fopen("php://memory", "r"),
                1,
                "test",
                new class {},
                1.4142136,
                "mokkd",
                [new class {}, ["mokkd", 3], true],
                3,
                false,
                0.57721567,
                2,
            ]
        ];
    }

    public static function nonEmptyListArrays(): iterable
    {
        $resource = fopen("php://memory", "r");
        yield "array-one-int" => [[2]];
        yield "array-one-float" => [[1.4142136]];
        yield "array-one-string" => [["mokkd"]];
        yield "array-one-true" => [[true]];
        yield "array-one-false" => [[false]];
        yield "array-one-null" => [[null]];
        yield "array-one-empty-array" => [[[]]];
        yield "array-one-array" => [[["mokkd", 3, 1.4142136]]];
        yield "array-one-object" => [[new class {}]];
        yield "array-one-resource" => [[$resource]];
        yield "array-three-ints" => [[1, 2, 3]];
        yield "array-three-floats" => [[3.1415927, 0.57721567, 1.4142136]];
        yield "array-three-strings" => [["mokkd", "func", "test"]];
        yield "array-three-bools" => [[true, false, false]];
        yield "array-three-nulls" => [[null, null, null]];
        yield "array-three-empty-arrays" => [[[], [], []]];
        yield "array-three-arrays" => [[["mokkd", 3, 1.4142136], [null, "test", new class {}], [$resource, [], true]]];
        yield "array-three-objects" => [[new class {}, new class {}, new class {}]];
        yield "array-three-resources" => [[$resource, $resource, $resource]];
        yield from self::mixedListArray();
    }

    public static function listArrays(): iterable
    {
        yield from self::emptyArray();
        yield from self::nonEmptyListArrays();
    }

    public static function mixedAssociativeArray(): iterable
    {
        yield "associative-array-mixed" => [
            [
                "string" => "func",
                "true" => true,
                "0" => null,
                1 => 3.1415927,
                "empty-array" => [],
                "resource" => fopen("php://memory", "r"),
                2 => 1,
                "test" => "test",
                "object" => new class {},
                "float" => 1.4142136,
                "string-2" => "mokkd",
                "array" => [new class {}, ["mokkd", 3], true],
                3 => 3,
                4 => false,
                "float-2" => 0.57721567,
                "int" => 2,
            ]
        ];
    }

    /** These are all not property maps - these are arrays that are neither lists  nor property maps. */
    public static function nonEmptyAssociativeArrays(): iterable
    {
        yield "associative-array-three-ints" => [["one" => 1, 2 => 2, "three" => 3]];
        yield "associative-array-three-floats" => [[" 0" => 3.1415927, 1 => 0.57721567, 2 => 1.4142136]];
        yield "associative-array-three-strings" => [[0 => "mokkd", 1 => "func", 3 => "test"]];
        yield "associative-array-three-bools" => [[1 => true, 2 => false, 3 => false]];
        yield "associative-array-three-objects" => [[0 => new class {}, 2 => new class {}, 3 => new class {}]];

        yield from self::mixedAssociativeArray();
    }

    public static function associativeArrays(): iterable
    {
        yield from self::relabel(self::emptyArray(), "associative-", RelabelMode::Prefix);
        yield from self::nonEmptyAssociativeArrays();
    }

    /** A property map (string keys) with mixed values. */
    public static function mixedPropertyMap(): iterable
    {
        yield "property-map-mixed" => [
            [
                "string" => "func",
                "true" => true,
                "nil" => null,
                "pi" => 3.1415927,
                "empty-array" => [],
                "resource" => fopen("php://memory", "r"),
                "two" => 1,
                "test" => "test",
                "object" => new class {},
                "pythagoras" => 1.4142136,
                "string-2" => "mokkd",
                "array" => [new class {}, ["mokkd", 3], true],
                "three" => 3,
                "four" => false,
                "float-2" => 0.57721567,
                "int" => 2,
            ]
        ];
    }

    /** Property maps (string keys) that are not empty. */
    public static function nonEmptyPropertyMaps(): iterable
    {
        $resource = fopen("php://memory", "r");
        yield "property-map-one-int" => [["int" => 2]];
        yield "property-map-one-float" => [["float" => 1.4142136]];
        yield "property-map-one-string" => [["string" => "mokkd"]];
        yield "property-map-one-true" => [["true" => true]];
        yield "property-map-one-false" => [["false" => false]];
        yield "property-map-one-null" => [["null" => null]];
        yield "property-map-one-empty-array" => [["empty-array" => []]];
        yield "property-map-one-array" => [["array" => ["mokkd", 3, 1.4142136]]];
        yield "property-map-one-object" => [["object" => new class {}]];
        yield "property-map-one-resource" => [["resource" => $resource]];
        yield "property-map-three-nulls" => [["null-1" => null, "null-2" => null, "null-3" => null]];
        yield "property-map-three-empty-arrays" => [["empty-1" => [], "empty-2" => [], "empty-3" => []]];
        yield "property-map-three-arrays" => [["array-1" => ["mokkd", 3, 1.4142136], "array-2" => [null, "test", new class {}], "array-3" => [$resource, [], true]]];
        yield "property-map-three-resources" => [["resource-1" => $resource, "resource-2" => $resource, "resource-3" => $resource]];

        yield from self::mixedPropertyMap();
    }

    /** All property maps (string keys) */
    public static function propertyMaps(): iterable
    {
        foreach (self::emptyArray() as $label => $data) {
            yield "property-map-{$label}" => $data;
        }

        yield from self::nonEmptyPropertyMaps();
    }

    public static function nonEmptyArrays(): iterable
    {
        yield from self::nonEmptyListArrays();
        yield from self::nonEmptyAssociativeArrays();
        yield from self::nonEmptyPropertyMaps();
    }

    /** @return iterable<string,array> */
    public static function arrays(): iterable
    {
        yield from self::listArrays();
        yield from self::associativeArrays();
    }

    //
    // integers
    //

    public static function integerZero(): iterable
    {
        yield "integer-zero" => [0];
    }

    public static function minInteger(): iterable
    {
        yield "integer-min" => [PHP_INT_MIN];
    }

    public static function maxInteger(): iterable
    {
        yield "integer-max" => [PHP_INT_MAX];
    }

    /** @return iterable<int[]> */
    public static function positiveIntegers(int $max = 100): iterable
    {
        TestCase::assertGreaterThan(0, $max);

        for ($value = 1; $value <= $max; ++$value) {
            yield "integer-{$value}" => [$value];
        }
    }

    /** @return iterable<int[]> */
    public static function negativeIntegers(int $min = -100): iterable
    {
        TestCase::assertLessThan(0, $min);
        $min = -$min;

        for ($value = 1; $value <= $min; ++$value) {
            yield "integer-minus-{$value}" => [-$value];
        }
    }

    /** @return iterable<int[]> */
    public static function integers(int $min = -100, int $max = 100): iterable
    {
        yield from self::integerZero();
        yield from self::negativeIntegers($min);
        yield from self::positiveIntegers($max);
        yield from self::minInteger();
        yield from self::maxInteger();
    }

    //
    // floats
    //
    
    public static function floatZero(): iterable
    {
        yield "float-zero" => [0.0];
    }

    public static function positiveFloats(): iterable
    {
        yield "float-pi" => [3.1415927];
        yield "float-euler" => [0.57721567];
        yield "float-pythagoras" => [1.4142136];
        yield "float-omega" => [0.5671433];
    }

    public static function negativeFloats(): iterable
    {
        yield "float-negative-sierpinski" => [-2.5849817];
        yield "float-negative-ln2" => [-0.6931472];
        yield "float-negative-phi" => [-1.618034];
        yield "float-negative-laplace" => [-0.6627434];
    }

    public static function floats(): iterable
    {
        yield from self::floatZero();
        yield from self::positiveFloats();
        yield from self::negativeFloats();
    }

    //
    // strings
    //
    
    public static function emptyString(): iterable
    {
        yield "string-empty" => [""];
    }
    
    public static function whitespaceString(): iterable
    {
        yield "string-whitespace" => ["  "];
    }

    public static function zeroIntegerString(): iterable
    {
        foreach (self::integerZero() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function minIntegerString(): iterable
    {
        foreach (self::minInteger() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function maxIntegerString(): iterable
    {
        foreach (self::maxInteger() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function positiveIntegerStrings(int $max = 100): iterable
    {
        foreach (self::positiveIntegers($max) as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function negativeIntegerStrings(int $min = -100): iterable
    {
        foreach (self::negativeIntegers($min) as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function integerStrings(int $min = -100, int $max = 100): iterable
    {
        yield from self::zeroIntegerString();
        yield from self::positiveIntegerStrings($max);
        yield from self::negativeIntegerStrings($min);
        yield from self::minIntegerString();
        yield from self::maxIntegerString();
    }

    public static function zeroFloatString(): iterable
    {
        foreach (self::floatZero() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }
    
    public static function positiveFloatStrings(): iterable
    {
        foreach (self::positiveFloats() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function negativeFloatStrings(): iterable
    {
        foreach (self::negativeFloats() as $label => $data) {
            yield "string-{$label}" => ["{$data[0]}"];
        }
    }

    public static function floatStrings(): iterable
    {
        yield from self::zeroFloatString();
        yield from self::positiveFloatStrings();
        yield from self::negativeFloatStrings();
    }

    public static function singleWordLowerCaseStrings(): iterable
    {
        yield "string-word-lower-none" => ["none"];
        yield "string-word-lower-mokkd" => ["mokkd"];
        yield "string-word-lower-function" => ["function"];
        yield "string-word-lower-test" => ["test"];
        yield "string-word-lower-fresh" => ["fresh"];
        yield "string-word-lower-double" => ["double"];
        yield "string-word-lower-cattle" => ["cattle"];
        yield "string-word-lower-limp" => ["limp"];
        yield "string-word-lower-weather" => ["weather"];
        yield "string-word-lower-trombone" => ["trombone"];
        yield "string-word-lower-zoological" => ["zoological"];
        yield "string-word-lower-trite" => ["trite"];
        yield "string-word-lower-remember" => ["remember"];
        yield "string-word-lower-quintessential" => ["quintessential"];
        yield "string-word-lower-lockers" => ["lockers"];
        yield "string-word-lower-brink" => ["brink"];
        yield "string-word-lower-jester" => ["jester"];
        yield "string-word-lower-alpine" => ["alpine"];
    }

    public static function singleWordUpperCaseStrings(): iterable
    {
        yield "string-word-upper-none" => ["NONE"];
        yield "string-word-upper-mokkd" => ["MOKKD"];
        yield "string-word-upper-function" => ["FUNCTION"];
        yield "string-word-upper-test" => ["TEST"];
        yield "string-word-upper-fresh" => ["FRESH"];
        yield "string-word-upper-double" => ["DOUBLE"];
        yield "string-word-upper-cattle" => ["CATTLE"];
        yield "string-word-upper-limp" => ["LIMP"];
        yield "string-word-upper-weather" => ["WEATHER"];
        yield "string-word-upper-trombone" => ["TROMBONE"];
        yield "string-word-upper-zoological" => ["ZOOLOGICAL"];
        yield "string-word-upper-trite" => ["TRITE"];
        yield "string-word-upper-remember" => ["REMEMBER"];
        yield "string-word-upper-quintessential" => ["QUINTESSENTIAL"];
        yield "string-word-upper-lockers" => ["LOCKERS"];
        yield "string-word-upper-brink" => ["BRINK"];
        yield "string-word-upper-jester" => ["JESTER"];
        yield "string-word-upper-alpine" => ["ALPINE"];
    }

    public static function singleWordTitleCaseStrings(): iterable
    {
        yield "string-word-title-none" => ["None"];
        yield "string-word-title-mokkd" => ["Mokkd"];
        yield "string-word-title-function" => ["Function"];
        yield "string-word-title-test" => ["Test"];
        yield "string-word-title-fresh" => ["Fresh"];
        yield "string-word-title-double" => ["Double"];
        yield "string-word-title-cattle" => ["Cattle"];
        yield "string-word-title-limp" => ["Limp"];
        yield "string-word-title-weather" => ["Weather"];
        yield "string-word-title-trombone" => ["Trombone"];
        yield "string-word-title-zoological" => ["Zoological"];
        yield "string-word-title-trite" => ["Trite"];
        yield "string-word-title-remember" => ["Remember"];
        yield "string-word-title-quintessential" => ["Quintessential"];
        yield "string-word-title-lockers" => ["Lockers"];
        yield "string-word-title-brink" => ["Brink"];
        yield "string-word-title-jester" => ["Jester"];
        yield "string-word-title-alpine" => ["Alpine"];
    }

    public static function singleWordStrings(): iterable
    {
        yield from self::singleWordLowerCaseStrings();
        yield from self::singleWordUpperCaseStrings();
        yield from self::singleWordTitleCaseStrings();
    }

    public static function multiWordStrings(): iterable
    {
        yield "string-multi-word-1" => ["megaphone orchid sideways"];
        yield "string-multi-word-2" => ["eating final wellness"];
        yield "string-multi-word-3" => ["closing neptune"];
        yield "string-multi-word-4" => ["orchestrate yellow input finance glutton"];
        yield "string-multi-word-double-quotes" => ["fundamental \"estrange guitar\" triumph yelling fortitude exasperated"];
        yield "string-multi-word-single-quotes" => ["stretched 'wilting stable' menthol ingrown acute"];
        yield "string-multi-word-punctuation" => ["porcupine = drench-quitter (build) junior+time, casket: opiate; rusting!! sparrow? drains & leash@brighter.info <notable/challenge> £fourteen twenty% \$fifty *annotation"];
    }

    public static function binaryStrings(): iterable
    {
        yield "string-binary-png" => ["\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52\x00\x00\x00\x0c\x00\x00\x00\x0d\x08\x06\x00\x00\x00\x9d\x29\x8f\x42\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0e\xc4\x00\x00\x0e\xc4\x01\x95\x2b\x0e\x1b\x00\x00\x00\x19\x49\x44\x41\x54\x28\x91\x63\x64\x60\x60\xf8\xcf\x40\x02\x60\x22\x45\xf1\xa8\x86\x51\x0d\x78\x00\x00\x99\x7b\x01\x19\xf9\xcd\xc9\x79\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60"];
        yield "string-binary-jpeg" => ["\xff\xd8\xff\xe0\x00\x10\x4a\x46\x49\x46\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00\xff\xdb\x00\x43\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\x09\x09\x08\x0a\x0c\x14\x0d\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a\x1f\x1e\x1d\x1a\x1c\x1c\x20\x24\x2e\x27\x20\x22\x2c\x23\x1c\x1c\x28\x37\x29\x2c\x30\x31\x34\x34\x34\x1f\x27\x39\x3d\x38\x32\x3c\x2e\x33\x34\x32\xff\xdb\x00\x43\x01\x09\x09\x09\x0c\x0b\x0c\x18\x0d\x0d\x18\x32\x21\x1c\x21\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\xff\xc0\x00\x11\x08\x00\x0d\x00\x0c\x03\x01\x22\x00\x02\x11\x01\x03\x11\x01\xff\xc4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\xff\xc4\x00\xb5\x10\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00\x01\x7d\x01\x02\x03\x00\x04\x11\x05\x12\x21\x31\x41\x06\x13\x51\x61\x07\x22\x71\x14\x32\x81\x91\xa1\x08\x23\x42\xb1\xc1\x15\x52\xd1\xf0\x24\x33\x62\x72\x82\x09\x0a\x16\x17\x18\x19\x1a\x25\x26\x27\x28\x29\x2a\x34\x35\x36\x37\x38\x39\x3a\x43\x44\x45\x46\x47\x48\x49\x4a\x53\x54\x55\x56\x57\x58\x59\x5a\x63\x64\x65\x66\x67\x68\x69\x6a\x73\x74\x75\x76\x77\x78\x79\x7a\x83\x84\x85\x86\x87\x88\x89\x8a\x92\x93\x94\x95\x96\x97\x98\x99\x9a\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xff\xc4\x00\x1f\x01\x00\x03\x01\x01\x01\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\xff\xc4\x00\xb5\x11\x00\x02\x01\x02\x04\x04\x03\x04\x07\x05\x04\x04\x00\x01\x02\x77\x00\x01\x02\x03\x11\x04\x05\x21\x31\x06\x12\x41\x51\x07\x61\x71\x13\x22\x32\x81\x08\x14\x42\x91\xa1\xb1\xc1\x09\x23\x33\x52\xf0\x15\x62\x72\xd1\x0a\x16\x24\x34\xe1\x25\xf1\x17\x18\x19\x1a\x26\x27\x28\x29\x2a\x35\x36\x37\x38\x39\x3a\x43\x44\x45\x46\x47\x48\x49\x4a\x53\x54\x55\x56\x57\x58\x59\x5a\x63\x64\x65\x66\x67\x68\x69\x6a\x73\x74\x75\x76\x77\x78\x79\x7a\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x92\x93\x94\x95\x96\x97\x98\x99\x9a\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xff\xda\x00\x0c\x03\x01\x00\x02\x11\x03\x11\x00\x3f\x00\xf9\xfe\x8a\x28\xa0\x0f\xff\xd9"];
    }

    /** @return iterable<string,string[]> */
    public static function singleCharacterLowerCaseStrings(): iterable
    {
        foreach (range("a", "z") as $char) {
            yield "string-char-lower-{$char}" => [$char];
        }
    }

    /** @return iterable<string,string[]> */
    public static function singleCharacterUpperCaseStrings(): iterable
    {
        foreach (range("A", "Z") as $char) {
            yield "string-char-upper-{$char}" => [$char];
        }
    }

    public static function singleCharacterStrings(): iterable
    {
        yield from self::singleCharacterLowerCaseStrings();
        yield from self::singleCharacterUpperCaseStrings();
    }

    public static function jsonStrings(): iterable
    {
        // scalar literals
        yield "json-null" => ["null"];
        yield "json-true" => ["true"];
        yield "json-false" => ["false"];
        yield from self::relabel(self::integerStrings(), "json-", RelabelMode::Prefix);
        yield from self::relabel(self::floatStrings(), "json-", RelabelMode::Prefix);

        // arrays
        yield "json-empty-array" => ["[]"];
        yield "json-empty-array-leading-whitespace" => [" []"];
        yield "json-empty-array-trailing-whitespace" => ["[] "];
        yield "json-empty-array-embedded-whitespace" => ["[ ]"];
        yield "json-empty-array-whitespace" => [" [ ] "];

        yield "json-array-ints" => ["[" . IterableAlgorithms::reduce(
            IterableAlgorithms::transform(self::integers(), [self::class, "unboxSingle"]),
            static fn (string $carry, int $value): string => $carry . ("" === $carry ? "" : ", ") . $value,
            "",
        ) . "]"];

        yield "json-array-floats" => ["[" . IterableAlgorithms::reduce(
            IterableAlgorithms::transform(self::floats(), [self::class, "unboxSingle"]),
            static fn (string $carry, float $value): string => $carry . ("" === $carry ? "" : ", ") . $value,
            "",
        ) . "]"];

        yield "json-array-booleans" => ["[false, true, false, false, true, false, true, true, false, true, true]"];

        yield "json-array-strings" => ["[" . IterableAlgorithms::reduce(
            IterableAlgorithms::transform(
                [...self::singleWordStrings(), ...self::multiWordStrings()],
                [self::class, "unboxSingle"]
            ),
            static fn (string $carry, string $value): string => sprintf(("" === $carry ? "%s" : "%s, ") . "\"%s\"", $carry, str_replace("\"", "\\\"", $value)),
            "",
        ) . "]"];

        yield "json-array-mixed" => ["[false, 1, \"mokkd\", 2, 3.1415927, true, 3, \"func\", 0.57721567]"];

        // objects
        yield "json-empty-object" => ["{}"];
        yield "json-empty-object-leading-whitespace" => [" {}"];
        yield "json-empty-object-trailing-whitespace" => ["{} "];
        yield "json-empty-object-embedded-whitespace" => ["{ }"];
        yield "json-empty-object-whitespace" => [" { } "];

        // string property values
        $transformString = static fn(string $value): string => "{\"mokkd\": \"" . str_replace("\"", "\\\"", $value) . "\"}";
        yield from self::relabel(self::transform(self::singleWordStrings(), $transformString), "json-object-one-property-", RelabelMode::Prefix);
        yield from self::relabel(self::transform(self::multiWordStrings(), $transformString), "json-object-one-property-", RelabelMode::Prefix);

        // int and float property values
        $transformNumeric = static fn(int|float $value): string => "{\"mokkd\": \"{$value}\"}";
        yield from self::relabel(self::transform(self::integers(), $transformNumeric), "json-object-one-property-", RelabelMode::Prefix);
        yield from self::relabel(self::transform(self::floats(), static fn(float $value): string => "{$value}"), "json-object-one-property-", RelabelMode::Prefix);

        // boolean property values
        yield "json-object-one-property-bool-true" => ["{\"mokkd\": true}"];
        yield "json-object-one-property-bool-false" => ["{\"mokkd\": false}"];

        yield "json-object-complex-unformatted" => ["{ \"mokkd\": \"func\", \"major-version\": 1, \"pi\": 3.1415927, \"tags\": { \"category\": \"development\", \"language\": \"PHP\", \"bead\": \"framework\" }, \"isDevelopment\": true, \"documents\": [ { \"id\": 1, \"author\": \"Spud\", \"metadata\": { \"version\": \"0.9.6\", \"date\": \"2024-09-19\" }, \"content\": { \"text/html\": \"<!DOCTYPE html><html lang=\\\"en\\\"><head><title>Spud&apos;s Doc</title><body><h1>Spud&apos;s Doc</h1></body></html>\", \"text/markdown\": \"#Spud's Doc\" } }, { \"id\": 2, \"author\": \"Tater\", \"metadata\": { \"version\": \"0.7.3\", \"date\": \"2024-09-18\" }, \"content\": { \"text/html\": \"<!DOCTYPE html><html lang=\\\"fr\\\"><head><title>Tater&apos;s Doc</title><body><h1>Tater&apos;s Doc</h1></body></html>\", \"text/markdown\": \"#Tater's Doc\", \"text/plain\": \"Tater's Doc\" } } ] }"];

        yield "json-object-complex-formatted" => [
            <<<JSON
{
    "mokkd": "func",
    "major-version": 1,
    "pi": 3.1415927,
    "tags": {
        "category": "development",
        "language": "PHP",
        "bead": "framework"
    },
    "isDevelopment": true,
    "documents":
    [
        {
            "id": 1,
            "author": "Spud",
            "metadata": {
                "version": "0.9.6",
                "date": "2024-09-19"
            },
            "content": {
                "text/html": "<!DOCTYPE html><html lang=\\"en\\"><head><title>Spud&apos;s Doc</title><body><h1>Spud&apos;s Doc</h1></body></html>",
                "text/markdown": "#Spud's Doc"
            }
        },
        {
            "id": 2,
            "author": "Tater",
            "metadata": {
                "version": "0.7.3",
                "date": "2024-09-18"
            },
            "content": {
                "text/html": "<!DOCTYPE html><html lang=\\"en\\"><head><title>Tater&apos;s Doc</title><body><h1>Tater&apos;s Doc</h1></body></html>",
                "text/markdown": "#Tater's Doc",
                "text/plain": "Tater's Doc"
            }
        }
    ]
}
JSON
        ];
    }

    public static function nonEmptyStrings(): iterable
    {
        yield from self::whitespaceString();
        yield from self::singleCharacterStrings();
        yield from self::singleWordStrings();
        yield from self::multiWordStrings();
        yield from self::binaryStrings();
        yield from self::integerStrings();
        yield from self::floatStrings();
        yield from self::jsonStrings();
    }

    public static function strings(): iterable
    {
        yield from self::emptyString();
        yield from self::nonEmptyStrings();
    }

    //
    // booleans
    //

    public static function booleanFalse(): iterable
    {
        yield "bool-false" => [false];
    }

    public static function booleanTrue(): iterable
    {
        yield "bool-true" => [true];
    }

    public static function booleans(): iterable
    {
        yield from self::booleanFalse();
        yield from self::booleanTrue();
    }

    //
    // objects
    //

    public static function anonymousObject(): iterable
    {
        yield "object-anonymous-empty" => [new class {}];
    }

    public static function stdClassObject(): iterable
    {
        yield "object-stdclass" => [new stdClass()];
    }

    public static function classInstance(string $className, mixed ...$constructorArgs): iterable
    {
        static $sequence = 1;
        $label = str_replace("\\", "-", $className) . "-{$sequence}";
        yield $label => [new $className(...$constructorArgs)];
        ++$sequence;
    }

    public static function typeMatcherInstances(): iterable
    {
        foreach (
            ["IsArray", "IsBool", "IsClosedResource", "IsFalse", "IsFloat", "IsInstanceOf", "IsInt", "IsList", "IsNonEmptyArray", "IsNonEmptyList", "IsNonEmptyPropertyMap", "IsNull", "IsNumeric", "IsOpenResource", "IsPropertyMap", "IsResource", "IsString", "IsTrue"]
            as $matcherClass
        ) {
            yield "object-matcher-class-types-$matcherClass" => [self::classInstance("\\Mokkd\\Matchers\\Types\\{$matcherClass}")];
        }
    }

    /** @return iterable<string,object> */
    public static function objects(): iterable
    {
        yield from self::anonymousObject();
        yield from self::stdClassObject();
        yield from self::typeMatcherInstances();
    }

    public static function classNames(): iterable
    {
        yield from IterableAlgorithms::transform(self::objects(), fn(array $object): array => [(self::unboxSingle($object))::class]);
    }


    //
    // resources
    //

    public static function stream(string $streamPath = "php://memory"): iterable
    {
        static $sequence = 1;
        yield "resource-stream-{$sequence}" => [fopen($streamPath, "r")];
        ++$sequence;
    }

    public static function memoryStream(): iterable
    {
        yield from self::stream();
    }

    public static function temporaryStream(): iterable
    {
        yield from self::stream("php://temp");
    }

    public static function dataStream(string $data = "", string $mediaType = "application/octet-stream"): iterable
    {
        $data = base64_encode($data);
        yield from self::stream("data://{$mediaType};base64,{$data}");
    }

    public static function openResources(): iterable
    {
        yield from self::memoryStream();
        yield from self::temporaryStream();
        yield from self::dataStream();
        yield from self::dataStream("\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52\x00\x00\x00\x0c\x00\x00\x00\x0d\x08\x06\x00\x00\x00\x9d\x29\x8f\x42\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0e\xc4\x00\x00\x0e\xc4\x01\x95\x2b\x0e\x1b\x00\x00\x00\x19\x49\x44\x41\x54\x28\x91\x63\x64\x60\x60\xf8\xcf\x40\x02\x60\x22\x45\xf1\xa8\x86\x51\x0d\x78\x00\x00\x99\x7b\x01\x19\xf9\xcd\xc9\x79\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60", "image/png");
    }

    public static function closedResources(): iterable
    {
        $closeResource = static function($resource) {
            fclose($resource);
            return $resource;
        };

        yield from self::relabel(self::transform(self::memoryStream(), $closeResource), "closed-", RelabelMode::Prefix);
        yield from self::relabel(self::transform(self::temporaryStream(), $closeResource), "closed-", RelabelMode::Prefix);
    }

    public static function resources(): iterable
    {
        yield from self::openResources();
        yield from self::closedResources();
    }

    //
    // callables
    //

    /**
     * @param callable|null $callback A callback that you can use to "check in" with your test. It will be called with
     * the arguments specified
     * @param mixed|null $return The value that the test callables should return.
     * @return iterable<int,callable>
     */
    public static function callables(callable $callback = null, mixed $return = null): iterable
    {
        $callableObject = new class($callback, $return)
        {
            private $callback;

            private mixed $return;

            public function __construct(?callable $callback, mixed $return)
            {
                $this->callback = $callback;
                $this->return = $return;
            }

            private function invokeCallback(mixed ...$args): mixed
            {
                if (null !== $this->callback) {
                    ($this->callback)(...$args);
                }

                return $this->return;
            }

            public function __invoke(mixed ...$args): mixed
            {
                return $this->invokeCallback(...$args);
            }

            public function instanceMethod(mixed ...$args): mixed
            {
                return self::invokeCallback(...$args);
            }

            public static function staticMethod(): void
            {}
        };

        yield "callable-closure" => [static function (mixed ...$args) use ($callback, $return) {
            if (is_callable($callback)) {
                $callback(...$args);
            }

            return $return;
        }];


        yield "callable-instance-function" => [[$callableObject, "instanceMethod"]];
        yield "callable-invokable" => [$callableObject];

        # this is only viable without a callback since any static properties on the callable object would persist
        # between runs, forcing the tests using the data to run in their own process or fail due to expectations from
        # one test overwriting expectations for another
        if (null === $callback && null === $return) {
            yield "callable-static-method" => [[$callableObject::class, "staticMethod"]];
        }
    }

    //
    // Helpers
    //

    /** Helper to repeat a value or values n times in an array. */
    public static function repeatDataset(int $count, string $labelPrefix, mixed $value, mixed ...$values): iterable
    {
        assert(0 <= $count, new LogicException("Expected int >= 0, found {$count}"));

        for ($idx = 0; $idx < $count; ++$idx) {
            yield "{$labelPrefix}-" . ($count + 1) => self::box($value, ...$values);
        }
    }

    /** Yield a data provider with modified dataset labels. */
    public static function relabel(iterable $data, string|array|callable $newLabel, RelabelMode $mode): iterable
    {
        $changeLabel = match ($mode) {
            RelabelMode::Prefix => static fn (string|int $label): string => "{$newLabel}{$label}",
            RelabelMode::Suffix => static fn (string|int $label): string => "{$label}{$newLabel}",
            RelabelMode::Replace => static function (string|int $label) use (&$newLabel): string {
                assert (is_array($newLabel) && 0 < count($newLabel));
                return array_shift($newLabel);
            },
            RelabelMode::Callback => $newLabel,
        };

        foreach ($data as $label => $args) {
            yield $changeLabel($label) => $args;
        }
    }
    
    /**
     * @template T
     *
     * Helper to repeat a single value n times.
     *
     * @param int $count How many repetitions.
     * @param T $value The value to repeat.
     *
     * @return iterable<T>
     */
    public static function repeat(int $count, mixed $value): iterable
    {
        assert(0 <= $count, new LogicException("Expected int >= 0, found {$count}"));

        for ($idx = 0; $idx < $count; ++$idx) {
            yield $value;
        }
    }

    /** Helper to wrap a set of values as a dataset. */
    public static function box(mixed $value, mixed ...$values): array
    {
        return [$value, ...$values];
    }

    /**
     * @template T
     * Helper to unwrap the dataset yielded by a factory method that returns a single dataset.
     *
     * @param $data iterable<T>
     * @return T
     */
    public static function unboxSingle(iterable $data): mixed
    {
        if (!is_array($data)) {
            $data = iterator_to_array($data);
        }

        assert(1 === count($data));
        return array_shift($data);
    }

    /**
     * Apply a transformation to a given data provider and yield the result.
     *
     * The transformation function is called for each item in each dataset. Each dataset is yielded once all its items
     * have been transformed, with the original label.
     *
     * @param iterable<int|string,mixed> $data The data provider's data.
     * @param callable $transform The function to do the transformation.
     *
     * @return iterable<int|string,mixed> The transformed data provider.
     */
    public static function transform(iterable $data, callable $transform): iterable
    {
        foreach ($data as $label => $args) {
            yield $label => iterator_to_array(IterableAlgorithms::values(IterableAlgorithms::transform($args, $transform)));
        }
    }

    /**
     * Clone a dataset.
     *
     * A single copy of the dataset will be stored.
     *
     * @param iterable $data A reference to the dataset to clone. It will be traversed and replaced with an iterable
     * copy of itself.
     *
     * @return iterable The clone of the dataset.
     */
    public static function clone(iterable & $data): iterable
    {
        $cache = iterator_to_array($data);
        $generator = static fn() => yield from $cache;
        $data = $generator();
        return $generator();
    }

    public static function concatenate(iterable ...$datasets): iterable
    {
        foreach ($datasets as $dataset) {
            yield from $dataset;
        }
    }

    /**
     * Combine two or more data providers into a single data provider covering all combinations of the source providers'
     * datasets.
     */
    public static function matrix(iterable $data1, iterable $data2, iterable ...$otherData): iterable
    {
        $generator = static function() use ($data1, $data2): iterable {
            $data2 = iterator_to_array($data2);

            foreach ($data1 as $label1 => $args1) {
                foreach ($data2 as $label2 => $args2) {
                    yield "{$label1}-{$label2}" => [...$args1, ...$args2];
                }
            }
        };

        if (0 === count($otherData)) {
            yield from $generator();
        } else {
            yield from DataFactory::matrix($generator(), ...$otherData);
        }
    }

    /**
     * Combine two or more iterables into a single dataset.
     *
     * The first set of arguments from each dataset are combined for the first dataset, then all the second items, and
     * so on. The number of datasets yielded is determined by the size of the first dataset. All datasets must have at
     * least as many items as the first. If subsequent datasets contain more items, they are ignored.
     */
    public static function combine(iterable $data1, iterable $data2, iterable ...$otherData): iterable
    {
        $generator = static function() use ($data1, $data2): iterable {
            $data2 = iterator_to_array($data2);
            $labels2 = array_keys($data2);

            foreach ($data1 as $label1 => $args1) {
                $args2 = array_shift($data2);
                $label2 = array_shift($labels2);
                yield "{$label1}-{$label2}" => [...$args1, ...$args2];
            }
        };

        if (0 === count($otherData)) {
            yield from $generator();
        } else {
            yield from DataFactory::combine($generator(), ...$otherData);
        }
    }
}
