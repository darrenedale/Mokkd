<?php

declare(strict_types=1);

namespace Mokkd\Utilities;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Stringable;

/** Serialise argument lists. */
class Serialiser implements SerialiserContract
{
    private function elideString(string $str): string
    {
        if (30 < strlen($str)) {
            return substr($str, 0, 29) . "…";
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
        return "(float) {$value}";
    }

    private function serialiseBool(bool $value): string
    {
        return $value ? "true" : "false";
    }

    private function serialiseArray(array $values): string
    {
        return "[" . implode(",", array_map([self::class, "serialiseValue"], $values)) . "]";
    }

    private function serialiseObject(object $value): string
    {
        $serialised = "({$value::class})";

        if ($value instanceof Stringable) {
            $serialised .= " {$this->elideString((string) $value)}";
        }

        return $serialised;
    }

    private function serialiseResource($value): string
    {
        return "(resource [" . get_resource_type($value) . "]) @" . get_resource_id($value);
    }

    public function serialiseValue(mixed $value): string
    {
        $type = get_debug_type($value);

        return match ($type) {
            "string" => $this->serialiseString($value),
            "int" => $this->serialiseInt($value),
            "float" => $this->serialiseFloat($value),
            "bool" => $this->serialiseBool($value),
            "array" => $this->serialiseArray($value),
            default => (str_starts_with($type, "resource ") ? $this->serialiseResource($value) : $this->serialiseObject($value)),
        };
    }

    /** @return string[] */
    public function serialise(mixed ...$args): array
    {
        return array_map([self::class, "serialiseValue"], ...$args);
    }
}
