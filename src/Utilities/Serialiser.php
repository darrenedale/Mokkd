<?php

declare(strict_types=1);

namespace Mokkd\Utilities;

use LogicException;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Stringable;

/**
 * Serialise argument lists.
 *
 * All arguments are prefixed with a type and, if appropriate, the size. For example:
 * (string[5]) "Mokkd"
 *
 * Strings are "quoted"; arrays are [enclosed]. Objects are output as their (ClassName) followed by their string
 * representation if they implement Stringable. Arrays that are lists (all int keys ascending in sequence from 0) don't
 * have their keys output; other arrays are output with their keys. Only the first 10 elements of arrays and the first
 * 30 characters (bytes) of strings are serialised - when truncation happens, the truncated value will have an ellipsis
 * (…) appended and the size in the type prefix won't match the number of elements/characters in the serialisation.
 */
class Serialiser implements SerialiserContract
{
    private const MaxStringLength = 30;

    private const MaxArraySize = 10;

    /** Helper to elide a string to a fixed prefix length. */
    private function elideString(string $str): string
    {
        if (self::MaxStringLength < strlen($str)) {
            return substr($str, 0, self::MaxStringLength - 1) . "…";
        }

        return $str;
    }

    /** Serialise a string value. */
    private function serialiseString(string $value): string
    {
        $len = strlen($value);
        return "(string[{$len}]) \"{$this->elideString($value)}\"";
    }

    /** Serialise an int value. */
    private function serialiseInt(int $value): string
    {
        return "(int) {$value}";
    }

    /** Serialise a float value. */
    private function serialiseFloat(float $value): string
    {
        $str = rtrim(sprintf("%0.9f", $value), "0");

        if (str_ends_with($str, ".")) {
            $str .= "0";
        }

        return "(float) {$str}";
    }

    /** Serialise a boolean value. */
    private function serialiseBool(bool $value): string
    {
        return "(bool) " . ($value ? "true" : "false");
    }

    /**
     * Serialise arrays and maps.
     *
     * As with strings, the serialisation is prefixed with the type and length. Arrays that are just lists are
     * serialised without keys; maps are serialised with keys. Arrays or maps with more than 10 elements are truncated
     * (but the length in the serialisation prefix is the full array count). Since keys are always either ints or
     * strings, the keys don't have the type prefix - if it's a string it will be encosed in quotes, otherwise it's just
     * the int.
     *
     * @param array $values
     * @return string
     */
    private function serialiseArray(array $values): string
    {
        $truncated = false;
        $isList = array_is_list($values);
        $count = count($values);

        if (self::MaxArraySize < $count) {
            array_splice($values, self::MaxArraySize);
            $truncated = true;
        }

        if ($isList) {
            return "(array[{$count}]) [" . implode(", ", array_map([self::class, "serialise"], $values)) . ($truncated ? ", …]" : "]");
        }

        $mapAssociative = function(array $array): array {
            $mapped = [];

            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    $key = "\"{$key}\"";
                }

                $mapped[] = "{$key} => " . $this->serialise($value);
            }

            return $mapped;
        };

        return "(array[{$count}]) [" . implode(", ", $mapAssociative($values)) . ($truncated ? ", …]" : "]");
    }

    /** Serialise an object. */
    private function serialiseObject(object $value): string
    {
        $serialised = "(" . $value::class . ")";

        if ($value instanceof Stringable) {
            $serialised .= " {$this->elideString((string) $value)}";
        }

        return $serialised;
    }

    /** Serialise a resource. */
    private function serialiseResource($value): string
    {
        // only way to tell if it's a closed resource
        if ("resource (closed)" === get_debug_type($value)) {
            $serialised = "(resource[closed]) @";
        } else {
            assert(is_resource($value), new LogicException("Expected resource, found " . get_debug_type($value)));
            $serialised = "(resource[" . get_resource_type($value) . "]) @";
        }

        return $serialised . get_resource_id($value);
    }

    /** Serialise a null value. */
    private function serialiseNull(null $value): string
    {
        return "(null) null";
    }

    /** Serialise a value. */
    public function serialise(mixed $value): string
    {
        $type = get_debug_type($value);

        return match ($type) {
            "string" => $this->serialiseString($value),
            "int" => $this->serialiseInt($value),
            "float" => $this->serialiseFloat($value),
            "bool" => $this->serialiseBool($value),
            "array" => $this->serialiseArray($value),
            "null" => $this->serialiseNull($value),
            "resource (closed)" => $this->serialiseResource($value),
            "class@anonymous" => $this->serialiseObject($value),
            default => match (true) {
                str_starts_with($type, "resource (") => $this->serialiseResource($value),

                // this will catch both named class instances and anonymous instances that implement a single interface
                default => $this->serialiseObject($value),
            },
        };
    }
}
