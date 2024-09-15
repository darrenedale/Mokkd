<?php

declare(strict_types=1);

namespace MokkdTests\Matchers;

use MokkdTests\TestCase;
use ReflectionClass;
use ReflectionType;
use stdClass;

class DataFactory
{
    public static function identicalValues(): iterable
    {
        yield "identical-strings" => ["mokkd", "mokkd"];
        yield "identical-ints" => [42, 42];
        yield "identical-floats" => [3.1415927, 3.1415927];
        yield "identical-null" => [null, null];
        yield "identical-arrays" => [[1, 2, "mokkd", 3], [1, 2, "mokkd", 3]];
        yield "identical-true" => [true, true];
        yield "identical-false" => [false, false];
    }

    public static function equalValues(): iterable
    {
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
    }

    //
    // arrays
    //

    public static function emptyArray(): iterable
    {
        yield "array-empty" => [[]];
    }

    public static function arrays(): iterable
    {
        yield from self::emptyArray();

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
        
        yield "array-mixed" => [
            [
                "func",
                true,
                null,
                3.1415927,
                [],
                $resource,
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

    public static function positiveIntegers(int $max = 100): iterable
    {
        TestCase::assertGreaterThan(0, $max);

        for ($value = 1; $value <= $max; ++$value) {
            yield "integer-{$value}" => [$value];
        }
    }

    public static function negativeIntegers(int $min = -100): iterable
    {
        TestCase::assertLessThan(0, $min);

        for ($value = -1; $value >= $min; --$value) {
            yield "integer-{$value}" => [$value];
        }
    }

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

    public static function integerStrings(): iterable
    {
        yield from self::zeroIntegerString();
        yield from self::positiveIntegerStrings();
        yield from self::negativeIntegerStrings();
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

    public static function singleWordStrings(): iterable
    {
        yield "string-word-none" => ["none"];
        yield "string-word-mokkd" => ["mokkd"];
        yield "string-word-function" => ["function"];
        yield "string-word-test" => ["test"];
    }

    public static function multiWordStrings(): iterable
    {
        yield "string-multi-word-1" => ["megaphone orchid sideways"];
        yield "string-multi-word-2" => ["eating final wellness"];
        yield "string-multi-word-3" => ["closing neptune"];
        yield "string-multi-word-4" => ["orchestrate yellow input finance glutton"];
    }

    public static function binaryStrings(): iterable
    {
        yield "string-binary-png" => ["\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52\x00\x00\x00\x0c\x00\x00\x00\x0d\x08\x06\x00\x00\x00\x9d\x29\x8f\x42\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0e\xc4\x00\x00\x0e\xc4\x01\x95\x2b\x0e\x1b\x00\x00\x00\x19\x49\x44\x41\x54\x28\x91\x63\x64\x60\x60\xf8\xcf\x40\x02\x60\x22\x45\xf1\xa8\x86\x51\x0d\x78\x00\x00\x99\x7b\x01\x19\xf9\xcd\xc9\x79\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60"];
        yield "string-binary-jpeg" => ["\xff\xd8\xff\xe0\x00\x10\x4a\x46\x49\x46\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00\xff\xdb\x00\x43\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\x09\x09\x08\x0a\x0c\x14\x0d\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a\x1f\x1e\x1d\x1a\x1c\x1c\x20\x24\x2e\x27\x20\x22\x2c\x23\x1c\x1c\x28\x37\x29\x2c\x30\x31\x34\x34\x34\x1f\x27\x39\x3d\x38\x32\x3c\x2e\x33\x34\x32\xff\xdb\x00\x43\x01\x09\x09\x09\x0c\x0b\x0c\x18\x0d\x0d\x18\x32\x21\x1c\x21\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\x32\xff\xc0\x00\x11\x08\x00\x0d\x00\x0c\x03\x01\x22\x00\x02\x11\x01\x03\x11\x01\xff\xc4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\xff\xc4\x00\xb5\x10\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00\x01\x7d\x01\x02\x03\x00\x04\x11\x05\x12\x21\x31\x41\x06\x13\x51\x61\x07\x22\x71\x14\x32\x81\x91\xa1\x08\x23\x42\xb1\xc1\x15\x52\xd1\xf0\x24\x33\x62\x72\x82\x09\x0a\x16\x17\x18\x19\x1a\x25\x26\x27\x28\x29\x2a\x34\x35\x36\x37\x38\x39\x3a\x43\x44\x45\x46\x47\x48\x49\x4a\x53\x54\x55\x56\x57\x58\x59\x5a\x63\x64\x65\x66\x67\x68\x69\x6a\x73\x74\x75\x76\x77\x78\x79\x7a\x83\x84\x85\x86\x87\x88\x89\x8a\x92\x93\x94\x95\x96\x97\x98\x99\x9a\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xff\xc4\x00\x1f\x01\x00\x03\x01\x01\x01\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\xff\xc4\x00\xb5\x11\x00\x02\x01\x02\x04\x04\x03\x04\x07\x05\x04\x04\x00\x01\x02\x77\x00\x01\x02\x03\x11\x04\x05\x21\x31\x06\x12\x41\x51\x07\x61\x71\x13\x22\x32\x81\x08\x14\x42\x91\xa1\xb1\xc1\x09\x23\x33\x52\xf0\x15\x62\x72\xd1\x0a\x16\x24\x34\xe1\x25\xf1\x17\x18\x19\x1a\x26\x27\x28\x29\x2a\x35\x36\x37\x38\x39\x3a\x43\x44\x45\x46\x47\x48\x49\x4a\x53\x54\x55\x56\x57\x58\x59\x5a\x63\x64\x65\x66\x67\x68\x69\x6a\x73\x74\x75\x76\x77\x78\x79\x7a\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x92\x93\x94\x95\x96\x97\x98\x99\x9a\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xff\xda\x00\x0c\x03\x01\x00\x02\x11\x03\x11\x00\x3f\x00\xf9\xfe\x8a\x28\xa0\x0f\xff\xd9"];
    }

    public static function singleCharacterStrings(): iterable
    {
        foreach (range("a", "z") as $char) {
            yield "string-char-{$char}" => [$char];
        }

        foreach (range("A", "Z") as $char) {
            yield "string-char-{$char}" => [$char];
        }
    }

    public static function strings(): iterable
    {
        yield from self::emptyString();
        yield from self::whitespaceString();
        yield from self::singleCharacterStrings();
        yield from self::singleWordStrings();
        yield from self::multiWordStrings();
        yield from self::binaryStrings();
        yield from self::integerStrings();
        yield from self::floatStrings();
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

    public static function objects(): iterable
    {
        yield from self::anonymousObject();
        yield from self::stdClassObject();
        yield from self::typeMatcherInstances();
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

    public static function resources(): iterable
    {
        yield from self::memoryStream();
        yield from self::temporaryStream();
        yield from self::dataStream();
        yield from self::dataStream("\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52\x00\x00\x00\x0c\x00\x00\x00\x0d\x08\x06\x00\x00\x00\x9d\x29\x8f\x42\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0e\xc4\x00\x00\x0e\xc4\x01\x95\x2b\x0e\x1b\x00\x00\x00\x19\x49\x44\x41\x54\x28\x91\x63\x64\x60\x60\xf8\xcf\x40\x02\x60\x22\x45\xf1\xa8\x86\x51\x0d\x78\x00\x00\x99\x7b\x01\x19\xf9\xcd\xc9\x79\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60", "image/png");
    }
}
