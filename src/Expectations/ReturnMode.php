<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

enum ReturnMode
{
    case Value;

    case Callback;

    case Sequential;

    case Mapped;
}
