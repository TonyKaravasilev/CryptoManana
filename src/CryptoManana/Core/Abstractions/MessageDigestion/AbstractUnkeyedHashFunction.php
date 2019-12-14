<?php

/**
 * Abstraction for unkeyed hash objects like checksums and plain cryptographic hash functions.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface as ObjectHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashing;
use \CryptoManana\Core\Traits\MessageDigestion\ObjectHashingTrait as HashObjects;
use \CryptoManana\Core\Traits\MessageDigestion\FileHashingTrait as HashFiles;

/**
 * Class AbstractUnkeyedHashFunction - Abstraction for unkeyed hash classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin HashObjects
 * @mixin HashFiles
 */
abstract class AbstractUnkeyedHashFunction extends HashAlgorithm implements ObjectHashing, FileHashing
{
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

        $digest = hash(
            static::ALGORITHM_NAME,
            $data,
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
            $digest = hash_file(
                static::ALGORITHM_NAME,
                $filename,
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
            'type' => 'unkeyed digestion or checksum',
            'salt' => $this->salt,
            'mode' => $this->saltingMode,
        ];
    }
}
