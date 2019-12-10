<?php

/**
 * Interface for specifying file hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface FileHashingInterface - Interface for file hashing.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface FileHashingInterface
{
    /**
     * Calculates a hash value for the content of the given filename and location.
     *
     * @param string $filename The full path and name of the file for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashFile($filename);
}
