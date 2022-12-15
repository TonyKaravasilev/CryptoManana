<?php

/**
 * Interface for getting a single instance, used at singleton objects.
 */

namespace CryptoManana\Core\Interfaces\DesignPatterns;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;

/**
 * Interface SingleInstancingInterface - Interface for getting a single instance.
 *
 * @see \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton For Usage.
 *
 * @package CryptoManana\Core\Interfaces\DesignPatterns
 */
interface SingleInstancingInterface
{
    /**
     * Gives a unified access point for the current object instance.
     *
     * @return null|static|AbstractSingleton An instance.
     */
    public static function getInstance();
}
