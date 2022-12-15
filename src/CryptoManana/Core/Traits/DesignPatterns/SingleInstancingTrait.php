<?php

/**
 * Trait implementation of getting a single instance of an object.
 */

namespace CryptoManana\Core\Traits\DesignPatterns;

/**
 * Trait SingleInstancingTrait - Reusable implementation of `SingleInstancingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\DesignPatterns\SingleInstancingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\DesignPatterns
 */
trait SingleInstancingTrait
{
    /**
     * Gives a unified access point for the current object instance.
     *
     * @return static|null An instance.
     */
    public static function getInstance()
    {
        static $singleInstance = null;

        if ($singleInstance === null) {
            $singleInstance = new static();
        }

        return $singleInstance;
    }
}
