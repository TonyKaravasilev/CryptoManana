<?php

/**
 * Interface for specifying the password and data verification process for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface SecureVerificationInterface - Interface for secure password and data verification.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface SecureVerificationInterface
{
    /**
     * Securely compares and verifies if a digestion value is for the given input data.
     *
     * @param string $data The input string.
     * @param string $digest The digest string.
     *
     * @return bool The result of the secure comparison.
     * @throws \Exception Validation errors.
     */
    public function verifyHash($data, $digest);
}
