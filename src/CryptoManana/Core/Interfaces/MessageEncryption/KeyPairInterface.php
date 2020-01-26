<?php

/**
 * Interface for specifying public and private key pair capabilities for asymmetric encryption/signing algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface KeyPairInterface - Interface for public and private key pair capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface KeyPairInterface
{
    /**
     * The internal index name for the private key when exporting the key pair as an array/object.
     *
     * @see KeyPairInterface::getKeyPair() For exporting as an array/object.
     */
    const PRIVATE_KEY_INDEX_NAME = 'private';

    /**
     * The internal index name for the public key when exporting the key pair as an array/object.
     *
     * @see KeyPairInterface::getKeyPair() For exporting as an array/object.
     */
    const PUBLIC_KEY_INDEX_NAME = 'public';

    /**
     * Setter for the whole key pair as an array.
     *
     * @param string $privateKey The private key string.
     * @param string $publicKey The public key string.
     *
     * @throws \Exception Validation errors.
     */
    public function setKeyPair($privateKey, $publicKey);

    /**
     * Getter for the whole key pair as an array.
     *
     * @param bool|int|null $asArray Flag for exporting as an array, instead of an object.
     *
     * @return \stdClass|array The private and public key pair as an object.
     */
    public function getKeyPair($asArray = false);

    /**
     * Setter for the private key string property.
     *
     * @param string $privateKey The private key string.
     *
     * @throws \Exception Validation errors.
     */
    public function setPrivateKey($privateKey);

    /**
     * Getter for the private key string property.
     *
     * @return string The private key string.
     */
    public function getPrivateKey();

    /**
     * Setter for the public key string property.
     *
     * @param string $publicKey The public key string.
     *
     * @throws \Exception Validation errors.
     */
    public function setPublicKey($publicKey);

    /**
     * Getter for the public key string property.
     *
     * @return string The public key string.
     */
    public function getPublicKey();

    /**
     * Checks if the private key is present.
     *
     * @throws \Exception If there is no private key set.
     */
    public function checkIfThePrivateKeyIsSet();

    /**
     * Checks if the public key is present.
     *
     * @throws \Exception If there is no public key set.
     */
    public function checkIfThePublicKeyIsSet();
}
