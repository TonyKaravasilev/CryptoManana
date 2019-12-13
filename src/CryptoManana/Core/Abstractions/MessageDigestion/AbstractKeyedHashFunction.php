<?php

/**
 * Abstraction for keyed hash objects like HMAC functions (keyed-hash message authentication code hash).
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;
use \CryptoManana\Core\Interfaces\MessageDigestion\DigestionKeyInterface as KeyedHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface as ObjectHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashing;
use \CryptoManana\Core\Traits\MessageDigestion\DigestionKeyTrait as DigestionKey;
use \CryptoManana\Core\Traits\MessageDigestion\ObjectHashingTrait as HashObjects;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class AbstractKeyedHashFunction - Abstraction for keyed hash classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin DigestionKey
 * @mixin HashObjects
 */
abstract class AbstractKeyedHashFunction extends HashAlgorithm implements KeyedHashing, ObjectHashing, FileHashing
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
     * Internal method for location and filename validation.
     *
     * @param string $filename The filename and location.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateFileNamePath($filename)
    {
        $filename = StringBuilder::stringReplace("\0", '', $filename); // (ASCII 0 (0x00))
        $filename = realpath($filename); // Path traversal escape and absolute path fetching

        // Clear path cache
        if (!empty($filename)) {
            clearstatcache(true, $filename);
        }

        // Check if path is valid and the file is readable
        if ($filename === false || !file_exists($filename) || !is_readable($filename) || !is_file($filename)) {
            throw new \RuntimeException('File is not found or can not be accessed.');
        }
    }

    /**
     * Internal method for checking if native file hashing should be used by force.
     *
     * @return bool Is native hashing needed for the current salting mode.
     */
    protected function isFileSaltingForcingNativeHashing()
    {
        return (
            (
                // If there is an non-empty salt string set and salting is enabled
                $this->salt !== '' &&
                $this->saltingMode !== self::SALTING_MODE_NONE
            ) || (
                // If there is an empty salt string set and the salting mode duplicates/manipulates the input
                $this->salt === '' &&
                in_array($this->saltingMode, [self::SALTING_MODE_INFIX_SALT, self::SALTING_MODE_PALINDROME_MIRRORING])
            )
        );
    }

    /**
     * Unkeyed hash algorithm constructor.
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
