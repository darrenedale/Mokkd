<?php

declare(strict_types=1);

namespace Mokkd\Mappers;

use LogicException;
use Mokkd\Contracts\Mapper;
use RuntimeException;

/**
 * Mapper that uses a single positional argument in the call arguments as the map key.
 */
class IndexedArgument implements Mapper
{
    private int $index;

    public function __construct(int $index)
    {
        assert(0 <= $index, new LogicException("Expected index >= 0, found {$index}"));
        $this->index = $index;
    }

    public function mapKey(...$args): string|int
    {
        if (count($args) < $this->index - 1) {
            throw new RuntimeException("Not enough arguments to select argument #{$this->index} as the mapped return value key");
        }

        $key = $args[$this->index];

        if (!is_string($key) && (!is_int($key) || 0 > $key)) {
            throw new RuntimeException("Argument #{$this->index} is not a valid mapped return value key");
        }

        return $key;
    }
}
