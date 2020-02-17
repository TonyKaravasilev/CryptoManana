<?php

/**
 * Trait implementation of the user or client entity authentication via pseudo-random token as passphrase.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Interfaces\Containers\EntityAuthenticationInterface as EntityAuthenticationSpecification;

/**
 * Trait EntityAuthenticationViaTokenTrait - Reusable implementation of `EntityAuthenticationInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\EntityAuthenticationInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @mixin EntityAuthenticationSpecification
 */
trait EntityAuthenticationViaTokenTrait
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
    public function authenticateEntity($correctPassphrase, $suppliedPassphrase)
    {
        if (!is_string($correctPassphrase)) {
            throw new \InvalidArgumentException(
                'The correct token value for verification must be a string or a binary string.'
            );
        } elseif (!is_string($suppliedPassphrase)) {
            throw new \InvalidArgumentException(
                'The supplied user token value must be a string or a binary string.'
            );
        }

        return hash_equals($correctPassphrase, $suppliedPassphrase);
    }
}
