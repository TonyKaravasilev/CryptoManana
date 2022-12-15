<?php

/**
 * The cryptographically secure pseudo-random generator class.
 */

namespace CryptoManana\Randomness;

use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;
use CryptoManana\Core\Interfaces\Randomness\SeedableGeneratorInterface as SeedAction;
use CryptoManana\Factories\ExceptionFactory as ExceptionFactory;

/**
 * Class CryptoRandom - The cryptographically secure pseudo-random generator object.
 *
 * @package CryptoManana\Randomness
 */
class CryptoRandom extends RandomnessSource implements SeedAction
{
    /**
     * The initialization seed value property storage for all instances.
     *
     * @var bool|int The generator's seed value.
     */
    protected static $seed = false;

    /**
     * Internal static method for single point consumption of the randomness source that outputs integers.
     *
     * @param int $minimum The lowest value to be returned.
     * @param int $maximum The highest value to be returned.
     *
     * @return int Randomly generated integer number.
     * @throws \Exception If no suitable random source is found.
     */
    protected static function getInteger($minimum, $maximum)
    {
        return random_int($minimum, $maximum);
    }

    /**
     * Internal static method for single point consumption of the randomness source that outputs bytes.
     *
     * @param int $count The output string length based on the requested number of bytes.
     *
     * @return string Randomly generated string containing the requested number of bytes.
     * @throws \Exception If no suitable random source is found.
     */
    protected static function getEightBits($count)
    {
        return random_bytes($count);
    }

    /**
     * The maximum supported integer.
     *
     * @return int The upper integer generation border.
     */
    public function getMaxNumber()
    {
        return PHP_INT_MAX;
    }

    /**
     * The minimum supported integer.
     *
     * @return int The lower integer generation border.
     */
    public function getMinNumber()
    {
        return PHP_INT_MIN;
    }

    /**
     * Cryptographically secure pseudo-random generator constructor.
     *
     * Note: This type of generator does not support initialization seeding and is auto-seeded.
     *
     * @note Consumes the high-entropy source a few times on the first object creation.
     */
    public function __construct()
    {
        parent::__construct();

        if (self::$seed === false) {
            self::setSeed();
        }
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'systemPrecision' => self::$systemPrecision,
            'seed' => 'NOT SUPPORTED',
        ];
    }

    /**
     * Invoking the auto-seeding of the generator via source consumption.
     *
     * @param null|int $seed Auto-seeding.
     *
     * @throws \Exception|\CryptoManana\Exceptions\CryptographyException On seed with other value than `null`.
     *
     * @note This type of generator does not support controllable seeding.
     */
    public static function setSeed($seed = null)
    {
        if (!is_null($seed)) {
            $exception = ExceptionFactory::createInstance(
                ExceptionFactory::CRYPTOGRAPHY_PROBLEM
            );

            $message = 'Cryptographic pseudo-random data generators do not support seeding!';

            throw $exception->setMessage($message)->setFile(__FILE__)->setLine(__LINE__);
        } else {
            // Get time information
            list($microSeconds, $seconds) = explode(' ', microtime());

            // Get microseconds as integer first
            $seed = (int)($microSeconds * 1000000) - 1;

            // A 32bit integer overflow  workaround for the UNIX timestamp format
            $time = (PHP_MAJOR_VERSION === 5) ? abs($seconds - $seed) : $seconds + $seed;

            // Auto seeding by via consuming the pool to skip a 8-64 bytes with milliseconds delay
            for ($i = 1; $i <= 1 + ($time % 8); $i++) {
                $tmpOne = self::getEightBits(8);
            }

            // Explicit cleanup for delay, because `usleep(1)` can create process hogging
            unset($tmpOne);

            // Mark as seeded to seed only once on object creation
            self::$seed = true;
        }
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
}
