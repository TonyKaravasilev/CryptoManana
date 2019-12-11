<?php

/**
 * The pseudo-random generator class.
 */

namespace CryptoManana\Randomness;

use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;
use \CryptoManana\Core\Interfaces\Randomness\SeedableGeneratorInterface as SeedAction;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class PseudoRandom - The pseudo-random generator object.
 *
 * @package CryptoManana\Randomness
 */
class PseudoRandom extends RandomnessSource implements SeedAction
{
    /**
     * The initialization seed value property storage for all instances.
     *
     * @var bool|int The generator's seed value.
     */
    protected static $seed = false;

    /**
     * Validates the the given seed value and converts it to an integer.
     *
     * @param int|mixed $seed The initialization value.
     *
     * @return int The valid initialization value.
     * @throws \Exception Validation errors.
     */
    protected static function validateSeedValue($seed)
    {
        $seed = filter_var(
            $seed,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => -mt_getrandmax() - 1,
                    "max_range" => mt_getrandmax(),
                ],
            ]
        );

        if ($seed === false) {
            throw new \DomainException(
                "The provided seed value is of invalid type or is out of the supported range."
            );
        }

        return $seed;
    }

    /**
     * Internal static method for single point consumption of the randomness source that outputs integers.
     *
     * @param int $minimum The lowest value to be returned.
     * @param int $maximum The highest value to be returned.
     *
     * @return int Randomly generated integer number.
     */
    protected static function getInteger($minimum, $maximum)
    {
        return mt_rand($minimum, $maximum);
    }

    /**
     * Internal static method for single point consumption of the randomness source that outputs bytes.
     *
     * @param int $count The output string length based on the requested number of bytes.
     *
     * @return string Randomly generated string containing the requested number of bytes.
     */
    protected static function getEightBits($count)
    {
        $tmpBytes = '';

        for ($i = 1; $i <= $count; $i++) {
            $tmpBytes .= StringBuilder::getChr(self::getInteger(0, 255));
        }

        return $tmpBytes;
    }

    /**
     * The maximum supported integer.
     *
     * @return int The upper integer generation border.
     */
    public function getMaxNumber()
    {
        return mt_getrandmax();
    }

    /**
     * The minimum supported integer.
     *
     * @return int The lower integer generation border.
     */
    public function getMinNumber()
    {
        return -mt_getrandmax() - 1;
    }

    /**
     * The pseudo-random generator constructor.
     *
     * Note: This type of generator is auto-seeded on the first object creation.
     */
    public function __construct()
    {
        parent::__construct();

        if (self::$seed === false) {
            self::setSeed();
        }
    }

    /**
     * Seed the generator initialization or invoke auto-seeding.
     *
     * Note: Invokes auto-seeding if the `null` value is passed.
     *
     * @param null|int $seed The initialization value.
     *
     * @throws \Exception Validation errors.
     */
    public static function setSeed($seed = null)
    {
        if (!is_null($seed)) {
            $seed = self::validateSeedValue($seed);
        } else {
            // Get time information
            list($microSeconds, $seconds) = explode(' ', microtime());

            // Get microseconds as integer first
            $seed = (int)($microSeconds * 1000000) - 1;

            // A 32bit integer overflow  workaround for the UNIX timestamp format
            $seed = (PHP_MAJOR_VERSION === 5) ? abs($seconds - $seed) : $seconds + $seed;
        }

        $seed = (int)$seed;

        // Set the used seed value for history
        self::$seed = $seed;

        /**
         * {@internal Backward compatibility algorithm for seed must be used. }}
         */
        (PHP_VERSION_ID < 70100) ? mt_srand($seed) : mt_srand($seed, MT_RAND_PHP);
    }

    /**
     * Generate a random integer number in a certain range.
     *
     * Note: Passing `null` will use the default parameter value.
     *
     * @param null|int $from The lowest value to be returned (default => 0).
     * @param null|int $to The highest value to be returned (default => $this->getMaxNumber()).
     *
     * @return int Randomly generated integer number.
     * @throws \Exception Validation errors.
     */
    public function getInt($from = 0, $to = null)
    {
        $from = ($from === null) ? 0 : $from;
        $to = ($to === null) ? $this->getMaxNumber() : $to;

        $this->validateIntegerRange($from, $to);

        return self::getInteger($from, $to);
    }

    /**
     * Generate a random byte string.
     *
     * Note: PHP represents bytes as characters to make byte strings.
     *
     * @param int $length The output string length (default => 1).
     *
     * @return string Randomly generated string containing the requested number of bytes.
     * @throws \Exception Validation errors.
     */
    public function getBytes($length = 1)
    {
        $this->validatePositiveInteger($length);

        return self::getEightBits($length);
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return array_merge(
            parent::__debugInfo(),
            [
                'seed' => self::$seed,
            ]
        );
    }
}
