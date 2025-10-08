<?php

declare(strict_types=1);

namespace {
    /** This function serves no purpose other than being a function in a namespace with which to test. */
    function rootNamespacedFunction(): string
    {
        return "mokkd";
    }

    /** This function serves no purpose other than being a function in a namespace with which to test. */
    function rootNamespacedVoidFunction(): void
    {
    }

    /**
     * A void function with observable side-effects.
     *
     * The provided object will have its value property set to the string "mokkd".
     *
     * @param StdClass $object The object to modify.
     */
    function rootNamespacedVoidFunctionWithSideEffects(StdClass $object): void
    {
        $object->value = "mokkd";
    }
}

namespace MokkdTests {
    /** This function serves no purpose other than being a function in a namespace with which to test. */
    function namespacedFunction(): int
    {
        return 42;
    }

    /** This function serves no purpose other than being a function in a namespace with which to test. */
    function namespacedVoidFunction(): void
    {
    }
}