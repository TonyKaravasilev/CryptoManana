<?php

/**
 * Interface for static calls, used at factory objects.
 */

namespace CryptoManana\Core\DesignPatterns\Interfaces;

/**
 * Interface InterfaceStaticCallInstancing - Static calls to object instancing.
 *
 * @package CryptoManana\Core\DesignPatterns\Interfaces
 *
 * @see \CryptoManana\Core\DesignPatterns\Abstractions\AbstractFactory For Usage.
 */
interface InterfaceStaticCallInstancing
{
    /**
     * Static call method for object instancing.
     *
     * @param string|int|null $type Object type.
     * @return object|null Instance of an object.
     */
    public static function createInstance($type);
}
