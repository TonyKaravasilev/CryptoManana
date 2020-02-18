<?php

/**
 * Interface for dependency containers using the setter dependency injection type of pseudo-random generator services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;

/**
 * Interface RandomnessInjectableInterface - Interface specification for dependency injection via setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface RandomnessInjectableInterface
{
    /**
     * Setter for the pseudo-random generator service.
     *
     * @param RandomnessSource $generator The pseudo-random generator service.
     */
    public function setRandomGenerator(RandomnessSource $generator);

    /**
     * Getter for the pseudo-random generator service.
     *
     * @return RandomnessSource|null The currently injected pseudo-random generator service.
     */
    public function getRandomGenerator();
}
