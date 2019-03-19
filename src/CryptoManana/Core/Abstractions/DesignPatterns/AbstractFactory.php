<?php

/**
 * Abstraction for the factory design pattern.
 */

namespace CryptoManana\Core\Abstractions\DesignPatterns;

use \CryptoManana\Core\Interfaces\DesignPatterns\InterfaceStaticCallInstancing as StaticCalls;

/**
 * Class AbstractFactory - Abstraction for the factory design pattern.
 *
 * @package CryptoManana\Core\Abstractions\DesignPatterns
 */
abstract class AbstractFactory implements StaticCalls
{
    /**
     * Factory constructor.
     */
    public function __construct()
    {
        // Force instances to follow the same definition
        return null;
    }

    /**
     * Dynamic call method for object instancing.
     *
     * @param string|int|null $type Object type.
     *
     * @return object|null Instance of an object.
     */
    abstract public function create($type);
}
