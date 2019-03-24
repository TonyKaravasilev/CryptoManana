<?php

/**
 * Interface for static calls, used at factory objects.
 */

namespace CryptoManana\Core\Interfaces\DesignPatterns;

/**
 * Interface StaticCallInstancingInterface - Static calls to object instancing.
 *
 * @package CryptoManana\Core\Interfaces\DesignPatterns
 *
 * @see \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory For Usage.
 */
interface StaticCallInstancingInterface
{
    /**
     * Static call method for object instancing.
     *
     * @param string|int|null $type Object type.
     *
     * @return object|null Instance of an object.
     */
    public static function createInstance($type);
}
