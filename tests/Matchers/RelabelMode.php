<?php

declare(strict_types=1);

namespace MokkdTests\Matchers;

/** Modes of operation for DataFactory::relabel() */
Enum RelabelMode
{
    case Prefix;
    case Suffix;
    case Replace;
    case Callback;
}
