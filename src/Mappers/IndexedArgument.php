<?php

declare(strict_types=1);

namespace Mokkd\Mappers;

use LogicException;
use Mokkd\Contracts\KeyMapper;
use RuntimeException;

/**
 * Mapper that uses a single positional argument in the call arguments as the map key.
 *
 * The identified argument must be a string or int.
 */
class IndexedArgument implements KeyMapper
{
    /** @var int The position of the argument to select as the key. */
    private int $index;

    public function __construct(int $index)
    {
        assert(0 <= $index, new LogicException("Expected index >= 0, found {$index}"));
        $this->index = $index;
    }

    public function index(): int
    {
        return $this->index;
    }

    /**
     * Fetch the map key.
     *
     * The positional argument determined by the index is returned. It must be an int or string.
     *
     * @throws RuntimeException If there are insufficient arguments or the located key is not a string or int >= 0.
     * @return int|string The map key.
     */
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
