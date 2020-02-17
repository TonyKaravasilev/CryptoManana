<?php

/**
 * Interface for the authenticated encryption and decryption of data.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\DataStructures\AuthenticatedCipherData as CipherDataStructure;

/**
 * Interface AuthenticatedEncryptionInterface - Interface specification for authenticated encryption.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface AuthenticatedEncryptionInterface
{
    /**
     * The Encrypt-and-MAC (E&M) authenticated encryption mode representation.
     */
    const AUTHENTICATION_MODE_ENCRYPT_AND_MAC = 0;

    /**
     * The MAC-then-Encrypt (MtE) authenticated encryption mode representation.
     */
    const AUTHENTICATION_MODE_MAC_THEN_ENCRYPT = 1;

    /**
     * The Encrypt-then-MAC (EtM) authenticated encryption mode representation.
     */
    const AUTHENTICATION_MODE_ENCRYPT_THEN_MAC = 2;

    /**
     * Setter for authenticated encryption mode operation property.
     *
     * @param int $mode The authenticated encryption mode integer code value.
     *
     * @throws \Exception Validation errors.
     */
    public function setAuthenticationMode($mode);

    /**
     * Getter for the authenticated encryption mode operation property.
     *
     * @return int The authenticated encryption mode integer code value.
     */
    public function getAuthenticationMode();

    /**
     * Encrypts and authenticates the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return CipherDataStructure The authenticated cipher data object.
     * @throws \Exception Validation errors.
     */
    public function authenticatedEncryptData($plainData);

    /**
     * Decrypts and authenticates the given cipher data.
     *
     * @param CipherDataStructure $authenticatedCipherData The authenticated cipher data object.
     *
     * @return string The plain data information.
     * @throws \Exception Validation errors.
     */
    public function authenticatedDecryptData(CipherDataStructure $authenticatedCipherData);
}
