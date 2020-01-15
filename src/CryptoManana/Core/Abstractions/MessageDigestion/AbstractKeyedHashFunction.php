<?php

/**
 * Abstraction for keyed hash objects like HMAC functions (keyed-hash message authentication code hash).
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;
use \CryptoManana\Core\Interfaces\MessageDigestion\DigestionKeyInterface as KeyedHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface as ObjectHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface as RecursiveHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as DataVerification;
use \CryptoManana\Core\Traits\MessageDigestion\DigestionKeyTrait as DigestionKey;
use \CryptoManana\Core\Traits\MessageDigestion\ObjectHashingTrait as HashObjects;
use \CryptoManana\Core\Traits\MessageDigestion\FileHashingTrait as HashFiles;
use \CryptoManana\Core\Traits\MessageDigestion\RepetitiveHashingTrait as HashRepetitively;
use \CryptoManana\Core\Traits\MessageDigestion\SecureVerificationTrait as VerifyDataAndPasswords;

/**
 * Class AbstractKeyedHashFunction - Abstraction for keyed hash classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin DigestionKey
 * @mixin HashObjects
 * @mixin HashFiles
 * @mixin HashRepetitively
 * @mixin VerifyDataAndPasswords
 */
abstract class AbstractKeyedHashFunction extends HashAlgorithm implements
    KeyedHashing,
    ObjectHashing,
    FileHashing,
    RecursiveHashing,
    DataVerification
{
    /**
     * Data salting capabilities.
     *
     * {@internal Reusable implementation of `DigestionKeyInterface`. }}
     */
    use DigestionKey;

    /**
     * Object hashing capabilities.
     *
     * {@internal Reusable implementation of `ObjectHashingInterface`. }}
     */
    use HashObjects;

    /**
     * File hashing capabilities.
     *
     * {@internal Reusable implementation of `FileHashingInterface`. }}
     */
    use HashFiles;

    /**
     * Repetitive/recursive hashing capabilities.
     *
     * {@internal Reusable implementation of `RepetitiveHashingInterface`. }}
     */
    use HashRepetitively;

    /**
     * Secure password and data verification capabilities.
     *
     * {@internal Reusable implementation of `SecureVerificationInterface`. }}
     */
    use VerifyDataAndPasswords;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'none';

    /**
     * Flag to force native code polyfill realizations, if available.
     *
     * @var bool Flag to force native realizations.
     */
    protected $useNative = false;

    /**
     * The key string property storage.
     *
     * @var string The digestion key string value.
     */
    protected $key = '';

    /**
     * Keyed hash algorithm constructor.
     */
    public function __construct()
    {
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

        $digest = hash_hmac(
            static::ALGORITHM_NAME,
            $data,
            $this->key,
            ($this->digestFormat === self::DIGEST_OUTPUT_RAW)
        );

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }

    /**
     * Calculates a hash value for the content of the given filename and location.
     *
     * @param string $filename The full path and name of the file for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        $useFileSalting = $this->isFileSaltingForcingNativeHashing();

        if ($this->useNative || $useFileSalting) {
            $oldSalt = $this->salt;
            $oldMode = $this->saltingMode;

            $this->salt = ($useFileSalting) ? $this->salt : '';
            $this->saltingMode = ($useFileSalting) ? $this->saltingMode : self::SALTING_MODE_NONE;

            $digest = $this->hashData(file_get_contents($filename));

            $this->setSalt($oldSalt)->setSaltingMode($oldMode);
        } else {
            $digest = hash_hmac_file(
                static::ALGORITHM_NAME,
                $filename,
                $this->key,
                ($this->digestFormat === self::DIGEST_OUTPUT_RAW)
            );

            $digest = $this->changeOutputFormat($digest);
        }

        return $digest;
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'standard' => static::ALGORITHM_NAME,
            'type' => 'keyed digestion or HMAC',
            'key' => $this->key,
            'salt' => $this->salt,
            'mode' => $this->saltingMode,
        ];
    }
}
