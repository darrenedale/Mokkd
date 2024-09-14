<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

interface MockStaticMethod extends MockFunction
{
    /** The name of the class whose static method is mocked. */
    public function className(): string;
}
