<?php

/**
 * Interface for the layered encryption and decryption of data.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\DataStructures\EncryptionLayer;

/**
 * Interface LayeredEncryptionInterface - Interface specification for layered encryption.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface LayeredEncryptionInterface
{
    /**
     * Setter for the encryption layers' configuration.
     *
     * @param EncryptionLayer[]|array $layers Collection of layers.
     *
     * @throws \Exception Validation errors.
     */
    public function setLayers(array $layers);

    /**
     * Add a single new layer at the last of the list.
     *
     * @param EncryptionLayer $layer The layer configuration.
     *
     * @throws \Exception Validation errors.
     */
    public function addLayer(EncryptionLayer $layer);

    /**
     * Getter for the encryption layers' configuration.
     *
     * @return EncryptionLayer[]|array Collection of used layers' configuration.
     *
     * @throws \Exception Validation errors.
     */
    public function getLayers();

    /**
     * Encrypts the given plain data multiple times with different algorithms as layers.
     *
     * @param string $plainData The plain input string.
     * @param string $oneTimePad The optional one-time pad key.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public function layeredEncryptData($plainData, $oneTimePad = '');

    /**
     * Decrypts the given cipher data multiple times with different algorithms as layers.
     *
     * @param string $cipherData The encrypted input string.
     * @param string $oneTimePad The optional one-time pad key.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function layeredDecryptData($cipherData, $oneTimePad = '');
}
