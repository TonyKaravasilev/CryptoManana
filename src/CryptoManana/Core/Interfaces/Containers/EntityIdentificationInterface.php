<?php

/**
 * Interface for user or client entity identification.
 */

namespace CryptoManana\Core\Interfaces\Containers;

/**
 * Interface AsymmetricEncryptionInjectableInterface - Interface specification for entity identification.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface EntityIdentificationInterface
{
    /**
     * Verify the identity of a user or client entity.
     *
     * @param string $correctIdentification The correct identification information.
     * @param string $suppliedIdentification The supplied identification information.
     *
     * @return bool The identity verification result.
     * @throws \Exception Validation errors.
     */
    public function identifyEntity($correctIdentification, $suppliedIdentification);
}
