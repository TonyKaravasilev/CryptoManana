<?php

/**
 * Abstraction for unkeyed hash objects like checksums and plain cryptographic hash functions.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface as ObjectHashing;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashing;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class AbstractUnkeyedHashFunction - Abstraction for unkeyed hash classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 */
abstract class AbstractUnkeyedHashFunction extends HashAlgorithm implements ObjectHashing, FileHashing
{
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

        $digest = hash(static::ALGORITHM_NAME, $data, ($this->digestFormat === self::DIGEST_OUTPUT_RAW));

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
        // Validate input type
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        if ($this->useNative) {
            $data = file_get_contents($filename);

            $oldSalt = $this->getSalt();
            $oldMode = $this->getSaltingMode();

            $this->setSalt('')->setSaltingMode(self::SALTING_MODE_NONE);

            $digest = $this->hashData($data);

            $this->setSalt($oldSalt)->setSaltingMode($oldMode);
        } else {
            $digest = hash_file(static::ALGORITHM_NAME, $filename, ($this->digestFormat === self::DIGEST_OUTPUT_RAW));

            $digest = $this->changeOutputFormat($digest);
        }

        return $digest;
    }

    /**
     * Calculates a hash value for the serialized value of the given object.
     *
     * @param object|\stdClass $object The full path and name of the file for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashObject($object)
    {
        if (is_object($object)) {
            $object = serialize($object);
        } else {
            throw new \InvalidArgumentException('The data for hashing must be an object instance.');
        }

        return $this->hashData($object);
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
