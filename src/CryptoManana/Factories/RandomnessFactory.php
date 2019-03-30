<?php

/**
 * Factory object for pseudo-randomness generator instancing.
 */

namespace CryptoManana\Factories;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory as FactoryPattern;
use \CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessSource;
use \CryptoManana\Randomness\QuasiRandom as QuasiRandomness;
use \CryptoManana\Randomness\PseudoRandom as PseudoRandomness;
use \CryptoManana\Randomness\CryptoRandom as CryptographyRandomness;

/**
 * Class RandomnessFactory - Factory object for pseudo-randomness generator instancing.
 *
 * @package CryptoManana\Factories
 */
class RandomnessFactory extends FactoryPattern
{
    /**
     * The quasi-random generator type.
     */
    const QUASI_SOURCE = QuasiRandomness::class;

    /**
     * The pseudo-random generator type.
     */
    const PSEUDO_SOURCE = PseudoRandomness::class;

    /**
     * The cryptographically secure pseudo-random generator type.
     */
    const CRYPTO_SOURCE = CryptographyRandomness::class;

    /**
     * Create a pseudo-randomness generator.
     *
     * @param string|null $type The generator class name as type for creation.
     *
     * @return RandomnessSource|object|null An exception object or null.
     */
    public function create($type)
    {
        return self::createInstance($type);
    }

    /**
     * Create a pseudo-randomness generator.
     *
     * @param string|null $type The generator class name as type for creation.
     *
     * @return RandomnessSource|object|null An exception object or null.
     */
    public static function createInstance($type)
    {
        /**
         * Check if class exists and has a correct base class
         *
         * @var RandomnessSource|null $exception Object instance.
         */
        if (class_exists($type) && is_subclass_of($type, RandomnessSource::class)) {
            $exception = new $type();
        } else {
            $exception = null; // Invalid exception type given
        }

        return $exception;
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            self::class . '::QUASI_SOURCE' => self::QUASI_SOURCE,
            self::class . '::PSEUDO_SOURCE' => self::PSEUDO_SOURCE,
            self::class . '::CRYPTO_SOURCE' => self::CRYPTO_SOURCE,
        ];
    }
}
