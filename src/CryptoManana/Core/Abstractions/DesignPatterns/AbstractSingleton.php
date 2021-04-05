<?php

/**
 * Abstraction for the singleton design pattern.
 */

namespace CryptoManana\Core\Abstractions\DesignPatterns;

/**
 * Class AbstractSingleton - Abstraction for the singleton design pattern.
 *
 * @package CryptoManana\Core\Abstractions\DesignPatterns
 */
abstract class AbstractSingleton
{
    /**
     * Gives an unified access point for the current object instance.
     *
     * @return null|static|AbstractSingleton An instance.
     */
    public static function getInstance()
    {
        static $singleInstance = null;

        if ($singleInstance === null) {
            $singleInstance = new static();
        }

        return $singleInstance;
    }

    /**
     * Locks the creation of new objects but allows static creation and extending.
     */
    protected function __construct()
    {
        /* This may remain empty. */
        return null;
    }

    /**
     * Lock the reinitialization and unserialization abilities of the class.
     */
    public function __wakeup()
    {
        /* This must remain empty. */
        return null;
    }

    /**
     * Lock the serialization abilities of the class.
     */
    public function __sleep()
    {
        /* This must remain empty. */
        return null;
    }

    /**
     * Lock the ability to clone properties and create a new dynamic instance of the class.
     */
    private function __clone()
    {
        /* This must remain empty. */
        return null;
    }

    /**
     * Return the name of the current defined class that extends the class.
     *
     * @return string Name of the class.
     */
    public function __toString()
    {
        return get_class($this);
    }
}
