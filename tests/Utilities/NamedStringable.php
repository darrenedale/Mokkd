<?php

declare(strict_types=1);

namespace MokkdTests\Utilities;

/** A test class for SerialiserTest when testing serialisation of named classes that implement Stringable */
class NamedStringable implements \Stringable
{
    public function __toString(): string
    {
        return "NamedStringable test class";
    }
}
