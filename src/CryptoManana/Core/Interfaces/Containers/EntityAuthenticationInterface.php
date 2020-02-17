<?php

/**
 * Interface for user or client entity authentication.
 */

namespace CryptoManana\Core\Interfaces\Containers;

/**
 * Interface EntityAuthenticationInterface - Interface specification for entity authentication.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface EntityAuthenticationInterface
{
    /**
     * Authenticate a user or a client entity.
     *
     * @param string $correctPassphrase The correct passphrase information.
     * @param string $suppliedPassphrase The supplied passphrase information.
     *
     * @return bool The identity authentication result.
     * @throws \Exception Validation errors.
     */
    public function authenticateEntity($correctPassphrase, $suppliedPassphrase);
}
