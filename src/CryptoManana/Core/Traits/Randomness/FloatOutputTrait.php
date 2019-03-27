<?php

/**
 * Trait implementation of float format generation for generator services.
 */

namespace CryptoManana\Core\Traits\Randomness;

use \CryptoManana\Core\Traits\Randomness\RandomnessTrait as RandomnessSpecification;

/**
 * Trait FloatOutputTrait - Reusable implementation of `FloatOutputInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Randomness\FloatOutputInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Randomness
 *
 * @mixin RandomnessSpecification
 */
trait FloatOutputTrait
{
    /**
     * Forcing the implementation of the software abstract randomness.
     *
     * {@internal Forcing the implementation of `AbstractRandomness`. }}
     */
    use RandomnessSpecification;

    /**
     * Internal method for calculating the machine epsilon value based on the used precision.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision The wanted precision for the machine epsilon.
     *
     * @return float The machine epsilon used for floating number comparison operations.
     */
    protected function calculateEpsilon($precision = null)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        // Calculate epsilon based on precision digits
        $epsilon = 0.1;

        for ($i = 1; $i < $precision; $i++) {
            $epsilon *= 0.1;
        }

        return $epsilon;
    }

    /**
     * Generate a low quality percentage format float number between 0.0 and 100.0.
     *
     * @param int $max The upper scope number used for internal generation.
     *
     * @return float Randomly generated low quality percentage value.
     */
    protected function calculateLowQualityPercent($max)
    {
        $number = $this->getInt(0, $max);

        $isNotRangeBoarders = ($number !== 0 && $number !== $max);

        $number = ($number === 0) ? 0.00 : ($number === $max) ? 100.00 : $number;

        $number = $isNotRangeBoarders ? ($number / $max) * 100.00 : $number;

        return $number;
    }

    /**
     * Internal method for double range supported types validation.
     *
     * @param int|float|null $from The minimum number in the wanted range.
     * @param int|float|null $to The maximum number in the wanted range.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateNumericOrDefault($from, $to)
    {
        $fromInvalidType = !in_array(gettype($from), ['integer', 'double', 'NULL']);
        $toInvalidType = !in_array(gettype($to), ['integer', 'double', 'NULL']);

        if ($fromInvalidType || $toInvalidType) {
            throw new \DomainException(
                "The provided values are of invalid type."
            );
        }
    }

    /**
     * Internal method for double range validation.
     *
     * @param int|float $from The minimum number in the wanted range.
     * @param int|float $to The maximum number in the wanted range.
     * @param null|int $precision The used precision for comparison.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateDoubleRange($from, $to, $precision = 14)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        if ($from < (float)$this->getMinNumber() || $to > (float)$this->getMaxNumber()) {
            throw new \DomainException(
                "The provided values are out of the supported range."
            );
        }

        if ($from > $to) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }

        $epsilon = $this->calculateEpsilon($precision);

        $difference = abs($from - $to);

        if ($difference < $epsilon) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }
    }

    /**
     * Generate a probability format float number between 0.0 and 1.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 10).
     *
     * @return float Randomly generated probability value.
     * @throws \Exception Validation errors.
     */
    public function getProbability($precision = 10)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        $number = $this->getInt(0, $this->getMaxNumber());

        $isNotRangeBoarders = ($number !== 0 && $number !== $this->getMaxNumber());

        $number = ($number === 0) ? 0.00 : ($number === $this->getMaxNumber()) ? 100.00 : (float)$number;

        $number = $isNotRangeBoarders ? round($number / (float)$this->getMaxNumber(), $precision) : $number;

        return $number;
    }

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
     * @throws \Exception Validation errors.
     */
    public function getFloat($from = 0.0, $to = null, $precision = 8)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        $this->validateNumericOrDefault($from, $to);

        $from = ($from === null) ? 0.0 : (float)$from;
        $to = ($to === null) ? (float)$this->getMaxNumber() : (float)$to;

        $this->validateDoubleRange($from, $to, $precision);

        $epsilon = $this->calculateEpsilon($precision);

        $toIsTheMaximum = abs($this->getMaxNumber() - $to) < $epsilon;
        $fromIsTheZero = abs(0.0 - $from) < $epsilon;

        $fromIsTheMinimumPlusOne = abs(($this->getMinNumber() + 1.0) - $from) < $epsilon;
        $toIsTheZero = abs(0.0 - $to) < $epsilon;

        // Improves the overall calculation quality for range calls
        if ($toIsTheMaximum && $fromIsTheZero) {
            $from = 0.01;
        } elseif ($toIsTheZero && $fromIsTheMinimumPlusOne) {
            $to = 0.01;
        }

        // Minimum precision for probability fetching
        $scope = ($precision > 14) ? $precision : 14;

        return round($from + $this->getProbability($scope) * abs($to - $from), $precision);
    }

    /**
     * Generate a percentage format float number between 0.0 and 100.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 2).
     * @param bool|int $lowerTheScope Flag for using a smaller calculation range.
     *
     * @return float Randomly generated percentage value.
     * @throws \Exception Validation errors.
     */
    public function getPercent($precision = 2, $lowerTheScope = false)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        if ($lowerTheScope) {
            $number = $this->calculateLowQualityPercent(9999); // 0-9999
        } else {
            // Minimum precision for probability fetching
            $scope = ($precision > 14) ? $precision : 14;

            $number = $this->getProbability($scope) * 100.00;
        }

        return round($number, $precision);
    }
}
