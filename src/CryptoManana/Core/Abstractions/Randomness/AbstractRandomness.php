<?php

/**
 * The computer randomness abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\Randomness;

/**
 * Class AbstractRandomness - The computer randomness abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\Randomness
 */
abstract class AbstractRandomness
{
    /**
     * Internal method for integer range validation.
     *
     * @param int $from The minimum number in the wanted range.
     * @param int $to The maximum number in the wanted range.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validateIntegerRange($from, $to);

    /**
     * Internal method for validation of positive integers.
     *
     * @param int $integer The positive integer value.
     * @param bool $includeZero Flag for enabling the zero as a valid value.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validatePositiveInteger($integer, $includeZero = false);

    /**
     * The maximum supported integer.
     *
     * @return int The upper integer generation border.
     */
    abstract public function getMaxNumber();

    /**
     * The minimum supported integer.
     *
     * @return int The lower integer generation border.
     */
    abstract public function getMinNumber();

    /**
     * Generate a random integer number in a certain range.
     *
     * Note: Passing `null` will use the default parameter value.
     *
     * @param null|int $from The lowest value to be returned (default => 0).
     * @param null|int $to The highest value to be returned (default => $this->getMaxNumber()).
     *
     * @return int Randomly generated integer number.
     */
    abstract public function getInt($from = 0, $to = null);

    /**
     * Generate a random byte string.
     *
     * Note: PHP represents bytes as characters to make byte strings.
     *
     * @param int $length The output string length (default => 1).
     *
     * @return string Randomly generated string containing the requested number of bytes.
     */
    abstract public function getBytes($length = 1);

    /**
     * Randomness source constructor.
     */
    abstract public function __construct();
}
