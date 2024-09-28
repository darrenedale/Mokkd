<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be an open resource of a specified type or null.
 *
 * Closed resources lose their type information, so it's not possible to match a closed resource of a specified type.
 */
class IsResourceOfTypeOrNull extends MatchesAnyOf
{
    private string $type;

    public function __construct(string $type)
    {
        parent::__construct(new IsNull(), new IsResourceOfType($type));
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?resource) {{$this->type()}}";
    }
}
