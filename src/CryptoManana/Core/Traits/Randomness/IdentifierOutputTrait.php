<?php

/**
 * Trait implementation of unique string identifier format generation for generator services.
 */

namespace CryptoManana\Core\Traits\Randomness;

use CryptoManana\Core\Traits\Randomness\RandomnessTrait as RandomnessSpecification;
use CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait IdentifierOutputTrait - Reusable implementation of `IdentifierOutputInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Randomness\IdentifierOutputInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Randomness
 *
 * @mixin RandomnessSpecification
 */
trait IdentifierOutputTrait
{
    /**
     * Forcing the implementation of the software abstract randomness.
     *
     * {@internal Forcing the implementation of `AbstractRandomness`. }}
     */
    use RandomnessSpecification;

    /**
     * Generate a random version 4 Globally Unique Identifier (GUID) standard string.
     *
     * Note: The identifier string uses 32 alphanumeric characters and 4 hyphens (optional).
     *
     * @param string $prefix Optional prefix for output strings (default => '').
     * @param bool $withDashes Flag for using dashes format (default => true).
     * @param bool $upperCase Flag for using uppercase format (default => false).
     *
     * @return string Randomly generated GUID string representing a 128-bit number.
     * @throws \Exception Validation errors.
     */
    public function getGloballyUniqueId($prefix = '', $withDashes = true, $upperCase = false)
    {
        $tmp = $this->getBytes(16);

        $tmp[6] = StringBuilder::getChr(StringBuilder::getOrd($tmp[6]) & 0x0f | 0x40);
        $tmp[8] = StringBuilder::getChr(StringBuilder::getOrd($tmp[8]) & 0x3f | 0x80);

        /**
         * {@internal Using only the built-in function to make it more encoding friendly (bigger than 8 bytes). }}
         */
        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($tmp), 4));

        $id = ($withDashes) ? $id : StringBuilder::stringReplace('-', '', $id);

        $id = ($upperCase) ? StringBuilder::stringToUpper($id) : $id;

        return StringBuilder::stringFullTrimming($prefix) . $id;
    }

    /**
     * Generate a strong Universally Unique Identifier (UUID) string in hexadecimal or alphanumeric format.
     *
     * Note: The identifier string is exactly 128 characters long.
     *
     * @param string $prefix Optional prefix for output strings (default => '').
     * @param bool $alphaNumeric Flag for switching to alphanumerical format (default => false).
     *
     * @return string Randomly generated strong hexadecimal/alphanumerical UUID string.
     * @throws \Exception Validation errors.
     */
    public function getStrongUniqueId($prefix = '', $alphaNumeric = false)
    {
        if ($alphaNumeric) {
            $id = $this->getAlphaNumeric(128);

            $id = $this->getBool() ? StringBuilder::stringReverse($id) : $id;
        } else {
            $id = hash_hmac(
                'sha512', // exactly 128 chars output (1024-bit)
                $this->getBytes(64), // 512-bit input
                $this->getBytes(64)  // 512-bit key
            );

            $id = StringBuilder::stringToUpper($id);
        }

        return StringBuilder::stringFullTrimming($prefix) . $id;
    }
}
