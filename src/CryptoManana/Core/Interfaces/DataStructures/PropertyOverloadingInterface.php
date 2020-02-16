<?php

/**
 * Interface for forcing the implementation of the magic methods for property overloading.
 */

namespace CryptoManana\Core\Interfaces\DataStructures;

/**
 * Interface PropertyOverloadingInterface - Magic methods for property overloading.
 *
 * @package CryptoManana\Core\Interfaces\DataStructures
 */
interface PropertyOverloadingInterface
{
    /**
     * Magic method invoked when attempting to get the value of an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     *
     * @return mixed The property value.
     */
    public function __get($name);

    /**
     * Magic method invoked when attempting to create or modify of an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     * @param mixed $value The property value.
     *
     * @throws \Exception The property does not exist or is from another type.
     */
    public function __set($name, $value);

    /**
     * Magic method invoked when checking of an inaccessible or a non-existent property is set.
     *
     * @param mixed $name The property name.
     *
     * @return bool The existence check result.
     */
    public function __isset($name);

    /**
     * Magic method invoked when attempting to unset an inaccessible or a non-existent property.
     *
     * @param mixed $name The property name.
     *
     * @throws \Exception The object does not allow dynamic property removal.
     */
    public function __unset($name);
}
