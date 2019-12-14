<?php

/**
 * Trait implementation of file hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashingSpecification;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as AnyDerivedHashAlgorithm;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait FileHashingTrait - Reusable implementation of `FileHashingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @mixin FileHashingSpecification
 * @mixin AnyDerivedHashAlgorithm
 */
trait FileHashingTrait
{
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
     * Calculates a hash value for the content of the given filename and location.
     *
     * @param string $filename The full path and name of the file for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    abstract public function hashFile($filename);
}
