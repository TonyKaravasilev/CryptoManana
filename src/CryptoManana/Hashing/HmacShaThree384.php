<?php

/**
 * The SHA-3 family HMAC-SHA-384 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacShaThree384 - The SHA-3 family HMAC-SHA-384 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacShaThree384 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha3-384';

    /**
     * Keyed digestion algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->useNative = !in_array(static::ALGORITHM_NAME, hash_hmac_algos(), true);
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
            \CryptoManana\Compatibility\NativeHmacSha3::digest384($data, $this->key, $rawOutput)
            :
            hash_hmac(static::ALGORITHM_NAME, $data, $this->key, $rawOutput);

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }
}
