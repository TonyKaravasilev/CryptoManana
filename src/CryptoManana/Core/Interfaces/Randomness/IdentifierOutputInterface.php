<?php

/**
 * Interface for specifying extra unique string identifier output formats for pseudo-random generators.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface IdentifierOutputInterface - Interface for random unique string identifier generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface IdentifierOutputInterface
{
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
     */
    public function getGloballyUniqueId($prefix = '', $withDashes = true, $upperCase = false);

    /**
     * Generate a strong Universally Unique Identifier (UUID) string in hexadecimal or alphanumeric format.
     *
     * Note: The identifier string is exactly 128 characters long.
     *
     * @param string $prefix Optional prefix for output strings (default => '').
     * @param bool $alphaNumeric Flag for switching to alphanumerical format (default => false).
     *
     * @return string Randomly generated strong hexadecimal/alphanumerical UUID string.
     */
    public function getStrongUniqueId($prefix = '', $alphaNumeric = false);
}
