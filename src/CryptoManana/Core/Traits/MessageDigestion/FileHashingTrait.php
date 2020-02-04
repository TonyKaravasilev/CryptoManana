<?php

/**
 * Trait implementation of file hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface as FileHashingSpecification;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as AnyDerivedHashAlgorithm;
use \CryptoManana\Core\Traits\CommonValidations\FileNameValidationTrait as ValidateFileNames;

/**
 * Trait FileHashingTrait - Reusable implementation of `FileHashingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @mixin FileHashingSpecification
 * @mixin AnyDerivedHashAlgorithm
 * @mixin ValidateFileNames
 */
trait FileHashingTrait
{
    /**
     * File name and path validations.
     *
     * {@internal Reusable implementation of the common file name validation. }}
     */
    use ValidateFileNames;

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
