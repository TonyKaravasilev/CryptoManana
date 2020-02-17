<?php

/**
 * Interface for the generation and the transformation of authentication tokens.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\DataStructures\AuthenticationToken as AuthenticationTokenStructure;

/**
 * Interface TokenTransformationInterface - Interface specification for authentication token transformation.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface TokenTransformationInterface
{
    /**
     * Generate a secure token and create an encrypt representation.
     *
     * @param int $length The desired output string length in bytes (default => 64).
     *
     * @return AuthenticationTokenStructure The authentication token data structure object.
     * @throws \Exception Validation errors.
     */
    public function generateAuthenticationToken($length = 64);

    /**
     * Extracts the token from the cipher data via a predefined configuration.
     *
     * @param string $cipherToken The encrypted token string.
     *
     * @return string The decrypted authentication token.
     * @throws \Exception Validation errors.
     */
    public function extractAuthenticationToken($cipherToken);
}
