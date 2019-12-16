<?php

/**
 * The SHA-3 family SHA-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class ShaThree256 - The SHA-3 family SHA-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class ShaThree256 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha3-256';

    /**
     * Checksum digestion algorithm constructor.
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

        $rawOutput = ($this->digestFormat === self::DIGEST_OUTPUT_RAW);

        /**
         * {@internal Backward compatibility native realization for SHA-3 must be used. }}
         */
        $digest = ($this->useNative) ?
            \CryptoManana\Compatibility\NativeSha3::digest256($data, $rawOutput)
            :
            hash(static::ALGORITHM_NAME, $data, $rawOutput);

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }
}
