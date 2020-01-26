<?php

/**
 * Trait implementation of the setter dependency injection type for pseudo-random generator services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;

/**
 * Trait RandomnessInjectableTrait - Reusable implementation of `RandomnessInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator $randomnessSource The randomness generator.
 */
trait RandomnessInjectableTrait
{
    /**
     * Setter for the pseudo-random generator service.
     *
     * @param RandomnessSource|null $generator The pseudo-random generator service.
     *
     * @return $this The container object.
     * @see \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface::setRandomGenerator() Specification.
     */
    public function setRandomGenerator(RandomnessSource $generator)
    {
        if (!is_null($generator)) {
            $this->randomnessSource = $generator;
        }

        return $this;
    }

    /**
     * Getter for the pseudo-random generator service.
     *
     * @return RandomnessSource The currently injected pseudo-random generator service.
     * @see \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface::getRandomGenerator() Specification.
     */
    public function getRandomGenerator()
    {
        return $this->randomnessSource;
    }
}
