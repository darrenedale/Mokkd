<?php

declare(strict_types=1);

namespace Mokkd\Utilities;

use LogicException;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Stringable;

/** Serialise argument lists. */
class Serialiser implements SerialiserContract
{
    private const MaxStringLength = 30;

    private const MaxArraySize = 10;

    private function elideString(string $str): string
    {
        if (self::MaxStringLength < strlen($str)) {
            return substr($str, 0, self::MaxStringLength - 1) . "…";
        }

        return $str;
    }

    private function serialiseString(string $value): string
    {
        $len = strlen($value);
        return "(string[{$len}]) \"{$this->elideString($value)}\"";
    }

    private function serialiseInt(int $value): string
    {
        return "(int) {$value}";
    }

    private function serialiseFloat(float $value): string
    {
        $str = rtrim(sprintf("%0.9f", $value), "0");

        if (str_ends_with($str, ".")) {
            $str .= "0";
        }

        return "(float) {$str}";
    }

    private function serialiseBool(bool $value): string
    {
        return "(bool) " . ($value ? "true" : "false");
    }

    private function serialiseArray(array $values): string
    {
        $truncated = false;

        if (self::MaxArraySize < count($values)) {
            array_splice($values, self::MaxArraySize);
            $truncated = true;
        }

        return "[" . implode(", ", array_map([self::class, "serialise"], $values)) . ($truncated ? ", …]" : "]");
    }

    private function serialiseObject(object $value): string
    {
        $serialised = "(" . $value::class . ")";

        if ($value instanceof Stringable) {
            $serialised .= " {$this->elideString((string) $value)}";
        }

        return $serialised;
    }

    private function serialiseResource($value): string
    {
        assert(is_resource($value), new LogicException("Expected resource, found " . get_debug_type($value)));

        if ("resource (closed)" === get_debug_type($value)) {
            $serialised = "(resource[closed]) @";
        } else {
            $serialised = "(resource[" . get_resource_type($value) . "]) @";
        }

        return $serialised . get_resource_id($value);
    }

    private function serialiseNull(null $value): string
    {
        return "(null) null";
    }

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
