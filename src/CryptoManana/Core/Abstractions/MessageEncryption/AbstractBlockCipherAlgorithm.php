<?php

/**
 * The symmetric block cipher encryption algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractSymmetricEncryptionAlgorithm as SymmetricCipherAlgorithm;
use \CryptoManana\Core\Interfaces\MessageEncryption\BlockOperationsInterface as CipherBlockOperations;
use \CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface as ObjectEncryption;
use \CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface as FileEncryption;
use \CryptoManana\Core\Traits\MessageEncryption\BlockOperationsTrait as CipherBlockInteractions;
use \CryptoManana\Core\Traits\MessageEncryption\ObjectEncryptionTrait as EncryptObjects;
use \CryptoManana\Core\Traits\MessageEncryption\FileEncryptionTrait as EncryptFiles;

/**
 * Class AbstractBlockCipherAlgorithm - The symmetric block cipher algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin CipherBlockInteractions
 * @mixin EncryptObjects
 * @mixin EncryptFiles
 */
abstract class AbstractBlockCipherAlgorithm extends SymmetricCipherAlgorithm implements
    CipherBlockOperations,
    ObjectEncryption,
    FileEncryption
{
    /**
     * Block cipher capabilities and data operations.
     *
     * {@internal Reusable implementation of `BlockOperationsInterface`. }}
     */
    use CipherBlockInteractions;

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
     * The internal operational block size measured in raw bytes length for the algorithm
     */
    const BLOCK_SIZE = 0;

    /**
     * The internal initialization vector (IV) size measured in raw bytes length for the algorithm
     */
    const IV_SIZE = 0;

    /**
     * The initialization vector (IV) string property storage.
     *
     * @var string The initialization vector (IV) string value.
     */
    protected $iv = '';

    /**
     * The block encryption operation mode string property.
     *
     * @var string The block encryption operation mode string value.
     */
    protected $mode = self::CBC_MODE;

    /**
     * The final block padding operation property.
     *
     * @var int The final block padding operation string value.
     */
    protected $padding = self::PKCS7_PADDING;

    /**
     * Fetch the correctly formatted internal encryption algorithm method name.
     *
     * @return string The symmetric encryption algorithm standard.
     */
    protected function fetchAlgorithmMethodName()
    {
        return static::ALGORITHM_NAME . '-' . (static::KEY_SIZE * 8) . '-' . $this->mode;
    }

    /**
     * Block cipher algorithm constructor.
     */
    public function __construct()
    {
        /**
         * {@internal Serialization and initialization purposes for both the default key and IV. }}
         */
        if ($this->key === '') {
            $this->key = str_pad($this->key, static::KEY_SIZE, "\x0", STR_PAD_RIGHT);
        }

        if ($this->iv === '') {
            $this->iv = str_pad($this->iv, static::BLOCK_SIZE, "\x0", STR_PAD_RIGHT);
        }

        // @codeCoverageIgnoreStart
        if (!in_array($this->fetchAlgorithmMethodName(), openssl_get_cipher_methods(), true)) {
            throw new \RuntimeException(
                'The algorithm `' .
                $this->fetchAlgorithmMethodName() .
                '`is not supported under the current system configuration.'
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Encrypts the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public function encryptData($plainData)
    {
        if (!is_string($plainData)) {
            throw new \InvalidArgumentException('The data for encryption must be a string or a binary string.');
        }

        if ($plainData === '') {
            $plainData = str_pad($plainData, static::BLOCK_SIZE, "\x0", STR_PAD_RIGHT);
        }

        /**
         * {@internal The encryption standard is 8-bit wise (don not use StringBuilder) and utilizes performance. }}
         */
        if ($this->padding === self::ZERO_PADDING && strlen($plainData) % static::BLOCK_SIZE !== 0) {
            $length = (static::BLOCK_SIZE - (strlen($plainData) % static::BLOCK_SIZE));

            for ($i = 0; $i < $length; $i++) {
                $plainData .= "\x0";
            }
        }

        $method = $this->fetchAlgorithmMethodName();

        $iv = ($this->mode === self::ECB_MODE) ? '' : $this->iv;

        $cipherData = openssl_encrypt($plainData, $method, $this->key, $this->padding, $iv);

        /**
         * {@internal The zero padding in raw mode comes as Base64 string from OpenSSL by specification. }}
         */
        if ($this->padding === self::ZERO_PADDING) {
            $cipherData = base64_decode($cipherData);
        }

        $cipherData = $this->changeOutputFormat($cipherData, true);

        return $cipherData;
    }

    /**
     * Decrypts the given cipher data.
     *
     * @param string $cipherData The encrypted input string.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function decryptData($cipherData)
    {
        if (!is_string($cipherData)) {
            throw new \InvalidArgumentException('The data for decryption must be a string or a binary string.');
        } elseif ($cipherData === '') {
            throw new \InvalidArgumentException('The string is empty and there is nothing to decrypt from it.');
        }

        $cipherData = $this->changeOutputFormat($cipherData, false);

        /**
         * {@internal The zero padding in raw mode comes as Base64 string from OpenSSL by specification. }}
         */
        if ($this->padding === self::ZERO_PADDING) {
            $cipherData = base64_encode($cipherData);
        }

        $method = $this->fetchAlgorithmMethodName();

        $iv = ($this->mode === self::ECB_MODE) ? '' : $this->iv;

        $plainData = openssl_decrypt($cipherData, $method, $this->key, $this->padding, $iv);

        // Wrong format verification
        if ($plainData === false) {
            throw new \RuntimeException(
                "The passed string was not from the chosen outputting format `{$this->getCipherFormat()}`."
            );
        }

        if ($this->padding === self::ZERO_PADDING) {
            $plainData = rtrim($plainData, "\x0");
        }

        return $plainData;
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
            'type' => 'symmetrical encryption or two-way block cipher',
            'block size in bits' => static::BLOCK_SIZE * 8,
            'key size in bits' => static::KEY_SIZE * 8,
            'iv size in bits' => static::IV_SIZE * 8,
            'block operation mode' => $this->mode,
            'padding standard' => $this->padding === self::PKCS7_PADDING ? 'PKCS7' : 'zero padding',
            'secret key' => $this->key,
            'initialization vector' => $this->iv,
            'internal algorithm full name' => $this->fetchAlgorithmMethodName(),
            'internal long data digestion algorithm' => 'HKDF-SHA-2-128',
        ];
    }
}
