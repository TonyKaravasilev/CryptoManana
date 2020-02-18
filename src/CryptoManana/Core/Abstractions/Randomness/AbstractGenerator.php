<?php

/**
 * Abstraction for pseudo-random generator objects.
 */

namespace CryptoManana\Core\Abstractions\Randomness;

use CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessRepresentation;
use CryptoManana\Core\Interfaces\Randomness\FloatOutputInterface as FloatOutput;
use CryptoManana\Core\Interfaces\Randomness\ArbitraryBaseOutputInterface as BaseOutput;
use CryptoManana\Core\Interfaces\Randomness\StringOutputInterface as StringOutput;
use CryptoManana\Core\Interfaces\Randomness\IdentifierOutputInterface as UuidOutput;
use CryptoManana\Core\Interfaces\Randomness\RgbOutputInterface as RgbOutput;
use CryptoManana\Core\Traits\Randomness\FloatOutputTrait as FloatGeneration;
use CryptoManana\Core\Traits\Randomness\ArbitraryBaseOutputTrait as BaseGeneration;
use CryptoManana\Core\Traits\Randomness\StringOutputTrait as StringGeneration;
use CryptoManana\Core\Traits\Randomness\IdentifierOutputTrait as UuidGeneration;
use CryptoManana\Core\Traits\Randomness\RgbOutputTrait as RgbGeneration;

/**
 * Class AbstractGenerator - Abstraction for pseudo-random generator classes.
 *
 * @package CryptoManana\Core\Abstractions\Randomness
 *
 * @mixin FloatGeneration
 * @mixin BaseGeneration
 * @mixin StringGeneration
 * @mixin UuidGeneration
 * @mixin RgbGeneration
 */
abstract class AbstractGenerator extends RandomnessRepresentation implements
    FloatOutput,
    BaseOutput,
    StringOutput,
    UuidOutput,
    RgbOutput
{
    /**
     * Float generation formats.
     *
     * {@internal Reusable implementation of `FloatOutputInterface`. }}
     */
    use FloatGeneration;

    /**
     * Arbitrary base generation formats.
     *
     * {@internal Reusable implementation of `ArbitraryBaseOutputInterface`. }}
     */
    use BaseGeneration;

    /**
     * String generation formats.
     *
     * {@internal Reusable implementation of `StringOutputInterface`. }}
     */
    use StringGeneration;

    /**
     * Unique string identifier generation formats.
     *
     * {@internal Reusable implementation of `IdentifierOutputInterface`. }}
     */
    use UuidGeneration;

    /**
     * Red-Green-Blue (RGB) colour generation formats.
     *
     * {@internal Reusable implementation of `RgbOutputInterface`. }}
     */
    use RgbGeneration;

    /**
     * The default system precision storage.
     *
     * @var int|null The used default floating number precision.
     */
    protected static $systemPrecision = null;

    /**
     * Internal method for integer range validation.
     *
     * @param int $from The minimum number in the wanted range.
     * @param int $to The maximum number in the wanted range.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateIntegerRange($from, $to)
    {
        $from = filter_var(
            $from,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $this->getMinNumber(),
                    "max_range" => $this->getMaxNumber(),
                ],
            ]
        );

        $to = filter_var(
            $to,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $this->getMinNumber(),
                    "max_range" => $this->getMaxNumber(),
                ],
            ]
        );

        if ($from === false || $to === false) {
            throw new \DomainException(
                "The provided values are of invalid type or are out of the supported range."
            );
        }

        if ($from >= $to) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }
    }

    /**
     * Internal method for validation of positive integers.
     *
     * @param int $integer The positive integer value.
     * @param bool $includeZero Flag for enabling the zero as a valid value.
     *
     * @throws \Exception Validation errors.
     */
    protected function validatePositiveInteger($integer, $includeZero = false)
    {
        $integer = filter_var(
            $integer,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $includeZero ? 0 : 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($integer === false) {
            throw new \DomainException('The provided value must be a positive integer.');
        }
    }

    /**
     * Randomness generator constructor.
     */
    public function __construct()
    {
        // Fetch the global system precision setting
        if (self::$systemPrecision === null) {
            self::$systemPrecision = (int)ini_get('precision');
        }
    }

    /**
     * Randomness generator reinitialization tasks after unserialization.
     */
    public function __wakeup()
    {
        // Ensures randomness is reinitialized and auto-seeded
        $this->__construct();
    }
}
