<?php

/**
 * Interface for specifying extra floating number output formats for pseudo-random generators.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface FloatOutputInterface - Interface for random floating number generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface FloatOutputInterface
{
    /**
     * Generate a probability format float number between 0.0 and 1.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 10).
     *
     * @return float Randomly generated probability value.
     */
    public function getProbability($precision = 10);

    /**
     * Generate a random float number in a certain range.
     *
     * Note: Passing `null` will use the default parameter value or for precision the global system value.
     *
     * @param null|float|int $from The lowest value to be returned (default => 0.0).
     * @param null|float|int $to The highest value to be returned (default => (float)$this->getMaxNumber()).
     * @param null|int $precision Rounding precision (default => 8).
     *
     * @return float Randomly generated float value.
     */
    public function getFloat($from = 0.0, $to = null, $precision = 8);

    /**
     * Generate a percentage format float number between 0.0 and 100.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 2).
     * @param bool|int $lowerTheScope Flag for using a smaller calculation range.
     *
     * @return float Randomly generated percentage value.
     */
    public function getPercent($precision = 2, $lowerTheScope = false);
}
