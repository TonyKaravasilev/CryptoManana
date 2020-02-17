<?php

/**
 * Trait implementation of the setter dependency injection type for pseudo-random generator services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;
use \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface as RandomnessInjectableSpecification;

/**
 * Trait RandomnessInjectableTrait - Reusable implementation of `RandomnessInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property RandomnessSource|null $randomnessSource The randomness generator.
 *
 * @mixin RandomnessInjectableSpecification
 */
trait RandomnessInjectableTrait
{
    /**
     * Setter for the pseudo-random generator service.
     *
     * @param RandomnessSource $generator The pseudo-random generator service.
     *
     * @return $this The container object.
     */
    public function setRandomGenerator(RandomnessSource $generator)
    {
        $this->randomnessSource = $generator;

        return $this;
    }

    /**
     * Getter for the pseudo-random generator service.
     *
     * @return RandomnessSource|null The currently injected pseudo-random generator service.
     */
    public function getRandomGenerator()
    {
        return $this->randomnessSource;
    }
}
