<?php

/**
 * Abstraction container for dependency injection of cryptography primitives services.
 */

namespace CryptoManana\Core\Abstractions\Containers;

/**
 * Class AbstractCryptographicProtocol - Abstraction container for dependency injection of cryptography services.
 *
 * @package CryptoManana\Core\Abstractions\Containers
 */
abstract class AbstractCryptographicProtocol
{
    /**
     * Container constructor.
     *
     * @throws \Exception Initialization validation.
     *
     * @internal The PHP syntax allows `__construct()` methods to be safely overridden with different parameters.
     */
    abstract public function __construct();

    /**
     * Container destructor.
     */
    abstract public function __destruct();

    /**
     * Container cloning via deep copy.
     */
    abstract public function __clone();
}
