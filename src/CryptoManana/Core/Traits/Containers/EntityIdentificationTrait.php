<?php

/**
 * Trait implementation of the user or client entity identification.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Interfaces\Containers\EntityIdentificationInterface as EntityVerificationSpecification;

/**
 * Trait EntityIdentificationTrait - Reusable implementation of `EntityIdentificationInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\EntityIdentificationInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @mixin EntityVerificationSpecification
 */
trait EntityIdentificationTrait
{
    /**
     * Verify the identity of a user or a client entity.
     *
     * @param string $correctIdentification The correct identification information.
     * @param string $suppliedIdentification The supplied identification information.
     *
     * @return bool The identity verification result.
     * @throws \Exception Validation errors.
     */
    public function identifyEntity($correctIdentification, $suppliedIdentification)
    {
        if (!is_string($correctIdentification)) {
            throw new \InvalidArgumentException(
                'The correct identity value for verification must be a string or a binary string.'
            );
        } elseif (!is_string($suppliedIdentification)) {
            throw new \InvalidArgumentException(
                'The supplied identity value must be a string or a binary string.'
            );
        }

        return hash_equals($correctIdentification, $suppliedIdentification);
    }
}
