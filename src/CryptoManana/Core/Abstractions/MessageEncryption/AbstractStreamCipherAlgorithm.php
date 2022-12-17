<?php

/**
 * The symmetric stream cipher encryption algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractSymmetricEncryptionAlgorithm as SymmetricCipherAlgorithm;
use CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface as ObjectEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface as FileEncryption;
use CryptoManana\Core\Traits\MessageEncryption\ObjectEncryptionTrait as EncryptObjects;
use CryptoManana\Core\Traits\MessageEncryption\FileEncryptionTrait as EncryptFiles;

/**
 * Class AbstractStreamCipherAlgorithm - The symmetric stream cipher algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin EncryptObjects
 * @mixin EncryptFiles
 */
abstract class AbstractStreamCipherAlgorithm extends SymmetricCipherAlgorithm implements
    ObjectEncryption,
    FileEncryption
{
    /**
     * Object encryption and decryption capabilities.
     *
     * {@internal Reusable implementation of `ObjectEncryptionInterface`. }}
     */
    use EncryptObjects;

    /**
     * File content encryption and decryption capabilities.
     *
     * {@internal Reusable implementation of `FileEncryptionInterface`. }}
     */
    use EncryptFiles;

    /**
     * Fetch the correctly formatted internal encryption algorithm method name.
     *
     * @return string The symmetric encryption algorithm standard.
     */
    protected function fetchAlgorithmMethodName()
    {
        return static::ALGORITHM_NAME;
    }

    /**
     * Internal method for the validation of plain data used at encryption operations.
     *
     * @param string $plainData The plain input string.
     *
     * @throws \Exception Validation errors.
     */
    protected function validatePlainDataForEncryption($plainData)
    {
        if (!is_string($plainData)) {
            throw new \InvalidArgumentException('The data for encryption must be a string or a binary string.');
        }
    }

    /**
     * Internal method for the validation of cipher data used at decryption operations.
     *
     * @param string $cipherData The encrypted input string.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateCipherDataForDecryption($cipherData)
    {
        if (!is_string($cipherData)) {
            throw new \InvalidArgumentException('The data for decryption must be a string or a binary string.');
        } elseif ($cipherData === '') {
            throw new \InvalidArgumentException('The string is empty and there is nothing to decrypt from it.');
        }
    }

    /**
     * Stream cipher algorithm constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        /**
         * {@internal Serialization and initialization purposes for the default key. }}
         */
        if (strlen($this->key) < static::KEY_SIZE) {
            $this->key = str_pad($this->key, static::KEY_SIZE, "\x0", STR_PAD_RIGHT);
        }

        if (!in_array(strtolower($this->fetchAlgorithmMethodName()), openssl_get_cipher_methods(), true)) {
            throw new \RuntimeException(
                'The algorithm `' .
                $this->fetchAlgorithmMethodName() .
                '`is not supported under the current system configuration.'
            );
        }
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'standard' => static::ALGORITHM_NAME,
            'type' => 'symmetrical encryption or two-way stream cipher',
            'key size in bits' => static::KEY_SIZE * 8,
            'secret key' => $this->key,
            'internal algorithm full name' => $this->fetchAlgorithmMethodName(),
            'internal long data digestion algorithm' => 'HKDF-SHA-2-128',
        ];
    }

    /**
     * Encrypts the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     *
     * @codeCoverageIgnore
     */
    public function encryptData($plainData)
    {
        $this->validatePlainDataForEncryption($plainData);

        $plainData = ($plainData === '') ? ' ' : $plainData;

        $cipherData = openssl_encrypt($plainData, $this->fetchAlgorithmMethodName(), $this->key, OPENSSL_RAW_DATA, '');

        return $this->changeOutputFormat($cipherData, true);
    }

    /**
     * Decrypts the given cipher data.
     *
     * @param string $cipherData The encrypted input string.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     *
     * @codeCoverageIgnore
     */
    public function decryptData($cipherData)
    {
        $this->validateCipherDataForDecryption($cipherData);

        $cipherData = $this->changeOutputFormat($cipherData, false);

        $plainData = openssl_decrypt($cipherData, $this->fetchAlgorithmMethodName(), $this->key, OPENSSL_RAW_DATA, '');

        return ($plainData === false) ? '' : $plainData;
    }
}
