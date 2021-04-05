<?php

/**
 * The asymmetric RSA algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricAlgorithm;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as AsymmetricDataEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\AsymmetricPaddingInterface as AsymmetricDataPadding;
use CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface as ObjectEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface as FileEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\CipherDataFormatsInterface as CipherDataFormatting;
use CryptoManana\Core\Traits\MessageEncryption\AsymmetricPaddingTrait as RsaPaddingStandards;
use CryptoManana\Core\Traits\MessageEncryption\ObjectEncryptionTrait as EncryptObjects;
use CryptoManana\Core\Traits\MessageEncryption\FileEncryptionTrait as EncryptFiles;
use CryptoManana\Core\Traits\MessageEncryption\CipherDataFormatsTrait as CipherDataFormats;

/**
 * Class AbstractRsaEncryption - The RSA encryption algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin RsaPaddingStandards
 * @mixin CipherDataFormats
 */
abstract class AbstractRsaEncryption extends AsymmetricAlgorithm implements
    AsymmetricDataEncryption,
    AsymmetricDataPadding,
    ObjectEncryption,
    FileEncryption,
    CipherDataFormatting
{
    /**
     * The RSA data padding basic standards.
     *
     * {@internal Reusable implementation of `AsymmetricPaddingInterface`. }}
     */
    use RsaPaddingStandards;

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
     * Cipher data outputting formats.
     *
     * {@internal Reusable implementation of `CipherDataFormatsInterface`. }}
     */
    use CipherDataFormats;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = OPENSSL_KEYTYPE_RSA;

    /**
     * The asymmetric data padding operation property.
     *
     * @var int The data padding operation integer code value.
     */
    protected $padding = self::OAEP_PADDING;

    /**
     * The output cipher format property storage.
     *
     * @var int The output cipher format integer code value.
     */
    protected $cipherFormat = self::ENCRYPTION_OUTPUT_BASE_64;

    /**
     * Flag for enabling/disabling data processing via chunks.
     *
     * @var bool Flag to for data processing via chunks.
     */
    protected $useChunks = false;

    /**
     * Internal method for the validation of plain data used at encryption/signing operations.
     *
     * @param string $plainData The plain input string.
     *
     * @throws \Exception Validation errors.
     */
    protected function validatePlainData($plainData)
    {
        if (!is_string($plainData)) {
            throw new \InvalidArgumentException("The data for encryption must be a string or a binary string.");
        } elseif ($this->useChunks === false) {
            $chunkSize = (int)ceil(static::KEY_SIZE / 8) - $this->getPaddingReservedSize();

            if (strlen($plainData) > $chunkSize) {
                throw new \InvalidArgumentException(
                    "The data for encryption must be less or equal of $chunkSize bytes. Another option is " .
                    "to allow chunk processing via the `enableChunkProcessing` method, which is not recommended."
                );
            }
        }
    }

    /**
     * Internal method for the validation of cipher/signature data used at decryption/verifying operations.
     *
     * @param string $cipherOrSignatureData The encrypted input string or a signature string.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateCipherOrSignatureData($cipherOrSignatureData)
    {
        if (!is_string($cipherOrSignatureData)) {
            throw new \InvalidArgumentException("The data for decryption must be a string or a binary string.");
        } elseif ($cipherOrSignatureData === '') {
            throw new \InvalidArgumentException("The string is empty and there is nothing to decrypt from it.");
        }

        $chunkSize = (int)ceil(static::KEY_SIZE / 8);
        $rawDataSize = strlen($this->changeOutputFormat($cipherOrSignatureData, false));

        if ($this->useChunks === false && $rawDataSize > $chunkSize) {
            throw new \InvalidArgumentException(
                "The data for decryption must be less or equal of $chunkSize bytes. Another option is " .
                "to allow chunk processing via the `enableChunkProcessing` method, which is not recommended."
            );
        } elseif ($rawDataSize % $chunkSize !== 0) {
            throw new \InvalidArgumentException(
                "The data length for decryption must dividable by $chunkSize byte blocks."
            );
        }
    }

    /**
     * RSA asymmetric algorithm constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'standard' => 'RSA',
            'type' => 'asymmetrical encryption or public-key cipher',
            'key size in bits' => static::KEY_SIZE,
            'maximum input in bits' => static::KEY_SIZE - (($this->padding === OPENSSL_PKCS1_PADDING) ? 88 : 336),
            'is chunk processing enabled' => $this->useChunks,
            'padding standard' => $this->padding === self::OAEP_PADDING ? 'OAEP' : 'PKCS1',
            'private key' => $this->privateKey,
            'public key' => $this->publicKey,
        ];
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
        $this->checkIfThePublicKeyIsSet();
        $this->validatePlainData($plainData);

        $publicKeyResource = openssl_pkey_get_public(base64_decode($this->publicKey));

        // @codeCoverageIgnoreStart
        if ($publicKeyResource === false) {
            throw new \RuntimeException(
                'Failed to use the current public key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }
        // @codeCoverageIgnoreEnd

        $chunkSize = (int)ceil(static::KEY_SIZE / 8) - $this->getPaddingReservedSize();
        $needsOnePass = ($plainData === '');
        $cipherData = '';

        while ($plainData || $needsOnePass) {
            $dataChunk = substr($plainData, 0, $chunkSize);
            $plainData = substr($plainData, $chunkSize);
            $encryptedChunk = '';

            if (!openssl_public_encrypt($dataChunk, $encryptedChunk, $publicKeyResource, $this->padding)) {
                throw new \InvalidArgumentException('The data encryption failed because of a wrong format');
            }

            $cipherData .= $encryptedChunk;
            $needsOnePass = false;
        }

        // Free the public key (resource cleanup)
        @openssl_free_key($publicKeyResource);
        $publicKeyResource = null;

        return $this->changeOutputFormat($cipherData, true);
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
        $this->checkIfThePrivateKeyIsSet();
        $this->validateCipherOrSignatureData($cipherData);

        $cipherData = $this->changeOutputFormat($cipherData, false);
        $privateKeyResource = openssl_pkey_get_private(base64_decode($this->privateKey));

        // @codeCoverageIgnoreStart
        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to use the current private key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }
        // @codeCoverageIgnoreEnd

        /**
         * {@internal The block size must be exactly dividable by the chunks size. }}
         */
        $chunkSize = (int)ceil(static::KEY_SIZE / 8);
        $plainData = '';

        while ($cipherData) {
            $chunkData = substr($cipherData, 0, $chunkSize);
            $cipherData = substr($cipherData, $chunkSize);
            $decryptedChunk = '';

            if (!openssl_private_decrypt($chunkData, $decryptedChunk, $privateKeyResource, $this->padding)) {
                throw new \InvalidArgumentException('The data decryption failed because of a wrong format');
            }

            $plainData .= $decryptedChunk;
        }

        // Free the private key (resource cleanup)
        @openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        return $plainData;
    }
}
