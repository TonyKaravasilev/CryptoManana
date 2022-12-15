<?php

/**
 * The abstract data structure object representation.
 */

namespace CryptoManana\Core\Abstractions\DataStructures;

use CryptoManana\Core\Interfaces\DataStructures\PropertyOverloadingInterface as PropertyOverloading;
use CryptoManana\Core\Traits\DataStructures\DisablePropertyOverloadingTrait as DisablePropertyOverloading;

/**
 * Class AbstractBasicStructure - The abstract structure object representation.
 *
 * @package CryptoManana\Core\Abstractions\DataStructures
 *
 * @mixin DisablePropertyOverloading
 */
abstract class AbstractBasicStructure implements PropertyOverloading
{
    /**
     * Disable the property overloading PHP behaviour based on the .
     *
     * {@internal Reusable implementation of `PropertyOverloadingInterface`. }}
     */
    use DisablePropertyOverloading;

    /**
     * Structure constructor.
     *
     * @throws \Exception Validation errors.
     *
     * @note The PHP syntax allows `__construct()` methods to be safely overridden with different parameters.
     */
    abstract public function __construct();

    /**
     * Structure destructor.
     */
    abstract public function __destruct();

    /**
     * The string representation of the given object.
     *
     * @return string
     */
    abstract public function __toString();
}
