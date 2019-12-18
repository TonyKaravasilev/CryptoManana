<?php

/**
 * The SHA-3 family HKDF-SHA-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfShaThree256 - The SHA-3 family HKDF-SHA-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfShaThree256 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha3-256';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @internal For the current algorithm: `32 * 255 = 8160`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 8160;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @internal The default output size in bytes for this algorithm.
     */
    protected $outputLength = 32;

    /**
     * Key derivation algorithm constructor.
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

        $data = $this->addSaltString(($data === '') ? ' ' : $data);

        // @codeCoverageIgnoreStart
        /**
         * {@internal Backward compatibility native realization for SHA-3 must be used. }}
         */
        $digest = ($this->useNative) ?
            \CryptoManana\Compatibility\NativeHkdfSha3::digest256(
                $data,
                $this->outputLength,
                $this->contextualString,
                $this->derivationSalt,
                true
            )
            :
            hash_hkdf(
                static::ALGORITHM_NAME,
                $data,
                $this->outputLength,
                $this->contextualString,
                $this->derivationSalt
            );
        // @codeCoverageIgnoreEnd

        if ($this->digestFormat !== self::DIGEST_OUTPUT_RAW) {
            $digest = bin2hex($digest);
        }

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }
}
