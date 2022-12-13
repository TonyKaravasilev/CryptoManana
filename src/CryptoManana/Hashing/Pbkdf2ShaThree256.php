<?php

/**
 * The SHA-3 family PBKDF2-SHA-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractIterativeSlowDerivation as SlowDerivationAlgorithm;

/**
 * Class Pbkdf2ShaThree256 - The SHA-3 family PBKDF2-SHA-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Pbkdf2ShaThree256 extends SlowDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha3-256';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @note For the current algorithm: `PHP_INT_MAX`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = PHP_INT_MAX;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @note The default output size in bytes for this algorithm.
     */
    protected $outputLength = 32;

    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->useNative = !in_array(static::ALGORITHM_NAME, hash_algos(), true);
    }

    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashData($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data for hashing must be a string or a binary string.');
        }

        $data = $this->addSaltString($data);

        // @codeCoverageIgnoreStart
        /**
         * {@internal Backward compatibility native realization for SHA-3 must be used. }}
         */
        $digest = ($this->useNative) ?
            \CryptoManana\Compatibility\NativePbkdf2Sha3::digest256(
                $data,
                $this->derivationSalt,
                $this->numberOfIterations,
                $this->outputLength,
                true
            )
            :
            hash_pbkdf2(
                static::ALGORITHM_NAME,
                $data,
                $this->derivationSalt,
                $this->numberOfIterations,
                $this->outputLength,
                true
            );
        // @codeCoverageIgnoreEnd

        if ($this->digestFormat !== self::DIGEST_OUTPUT_RAW) {
            $digest = bin2hex($digest);
        }

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }
}
