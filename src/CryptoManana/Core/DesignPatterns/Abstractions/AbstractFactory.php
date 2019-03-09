<?php

/**
 * Abstraction for the factory design pattern.
 */

namespace CryptoManana\Core\DesignPatterns\Abstractions;

use \CryptoManana\Core\DesignPatterns\Interfaces\InterfaceStaticCallInstancing as StaticCalls;

/**
 * Class AbstractFactory - Abstraction for the factory design pattern.
 *
 * @package CryptoManana\Core\DesignPatterns\Abstractions
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
     * @return object|null Instance of an object.
     */
    abstract public function create($type);
}
