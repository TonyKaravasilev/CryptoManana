<?php

/**
 * Interface for specifying public and private key pair capabilities for asymmetric encryption/signing algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

use CryptoManana\DataStructures\KeyPair as KeyPairStructure;

/**
 * Interface KeyPairInterface - Interface for public and private key pair capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface KeyPairInterface
{
    /**
     * Setter for the whole key pair as an array.
     *
     * @param KeyPairStructure $keyPair The private and public key pair as an object.
     *
     * @throws \Exception Validation errors.
     */
    public function setKeyPair(KeyPairStructure $keyPair);

    /**
     * Getter for the whole key pair as an array.
     *
     * @return KeyPairStructure The private and public key pair as an object.
     * @throws \Exception Validation errors.
     */
    public function getKeyPair();

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
