<?php

/**
 * Trait implementation of disabled property overloading.
 */

namespace CryptoManana\Core\Traits\DataStructures;

use \CryptoManana\Core\Interfaces\DataStructures\PropertyOverloadingInterface as PropertyOverloading;

/**
 * Trait DisablePropertyOverloadingTrait - Reusable implementation of disabling property overloading.
 *
 * @see \CryptoManana\Core\Interfaces\DataStructures\PropertyOverloadingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\DataStructures
 *
 * @mixin PropertyOverloading
 */
trait DisablePropertyOverloadingTrait
{
    /**
     * Magic method invoked when attempting to get the value of an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     *
     * @return mixed The property value.
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new \OutOfBoundsException('The property does not exist.');
        }
    }

    /**
     * Magic method invoked when attempting to create or modify of an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     * @param mixed $value The property value.
     *
     * @throws \Exception|\InvalidArgumentException The property does not exist or is from another type.
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name) && gettype($this->$name) === gettype($value)) {
            $this->$name = $value;
        } else {
            throw new \InvalidArgumentException('The property does not exist or is of an invalid type.');
        }
    }

    /**
     * Magic method invoked when checking of an inaccessible or a non-existent property is set.
     *
     * @param mixed $name The property name.
     *
     * @return bool The existence check result.
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * Magic method invoked when attempting to unset an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     *
     * @throws \Exception|\LogicException The object does not allow dynamic property removal.
     */
    public function __unset($name)
    {
        throw new \LogicException('This data structure does not allow deletion of properties.');
    }
}
