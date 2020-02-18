<?php

/**
 * Interface for the creation and The verification of signed data objects.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\DataStructures\SignedData as SignedDataStructure;

/**
 * Interface SignedDataInterface - Interface specification for signed data object creation and verification.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface SignedDataInterface
{
    /**
     * Creates a signed data object for the given input data.
     *
     * @param string $plainData The plain input string.
     *
     * @return SignedDataStructure The signed data object.
     * @throws \Exception Validation errors.
     */
    public function createSignedData($plainData);

    /**
     * Verifies and extracts the plain data from a signed data object.
     *
     * @param SignedDataStructure $signedData The signed data object.
     *
     * @return string The verified plain information.
     * @throws \Exception Validation errors.
     */
    public function extractVerifiedData(SignedDataStructure $signedData);
}
