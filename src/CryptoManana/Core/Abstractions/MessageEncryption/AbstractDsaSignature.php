<?php

/**
 * The asymmetric DSA algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricAlgorithm;
use CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DigitalSignatureVerification;
use CryptoManana\Core\Interfaces\MessageEncryption\SignatureDigestionInterface as SignatureDataDigestion;
use CryptoManana\Core\Interfaces\MessageEncryption\ObjectSigningInterface as ObjectSigning;
use CryptoManana\Core\Interfaces\MessageEncryption\FileSigningInterface as FileSigning;
use CryptoManana\Core\Interfaces\MessageEncryption\SignatureDataFormatsInterface as SignatureDataFormatting;
use CryptoManana\Core\Traits\MessageEncryption\SignatureDataFormatsTrait as SignatureDataFormats;
use CryptoManana\Core\Traits\MessageEncryption\ObjectSigningTrait as SignAndVerifyObjects;
use CryptoManana\Core\Traits\MessageEncryption\FileSigningTrait as SignAndVerifyFiles;
use CryptoManana\Core\Traits\MessageEncryption\SignatureDigestionTrait as SignatureDigestionAlgorithms;

/**
 * Class AbstractDsaSignature - The DSA signature algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin SignatureDigestionAlgorithms
 * @mixin SignAndVerifyObjects
 * @mixin SignAndVerifyFiles
 * @mixin SignatureDataFormats
 */
abstract class AbstractDsaSignature extends AsymmetricAlgorithm implements
    DigitalSignatureVerification,
    SignatureDataDigestion,
    ObjectSigning,
    FileSigning,
    SignatureDataFormatting
{
    /**
     * Signature digestion algorithms.
     *
     * {@internal Reusable implementation of `SignatureDigestionInterface`. }}
     */
    use SignatureDigestionAlgorithms;

    /**
     * Generating signatures for objects and verifying them.
     *
     * {@internal Reusable implementation of `ObjectSigningInterface`. }}
     */
    use SignAndVerifyObjects;

    /**
     * Generating signatures for file content and verifying them.
     *
     * {@internal Reusable implementation of `FileSigningInterface`. }}
     */
    use SignAndVerifyFiles;

    /**
     * Signature data outputting formats.
     *
     * {@internal Reusable implementation of `SignatureDataFormatsInterface`. }}
     */
    use SignatureDataFormats;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = OPENSSL_KEYTYPE_DSA;

    /**
     * The signature's internal digestion algorithm property.
     *
     * @var int The digestion algorithm integer code value.
     */
    protected $digestion = self::SHA2_384_SIGNING;

    /**
     * The output signature format property storage.
     *
     * @var int The output signature format integer code value.
     */
    protected $signatureFormat = self::SIGNATURE_OUTPUT_HEX_UPPER;

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
            throw new \InvalidArgumentException('The data for signing must be a string or a binary string.');
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
            throw new \InvalidArgumentException('The signature data must be a string or a binary string.');
        } elseif ($cipherOrSignatureData === '') {
            throw new \InvalidArgumentException(
                'The signature string is empty and there is nothing to verify from it.'
            );
        }
    }

    /**
     * DSA asymmetric algorithm constructor.
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
            'standard' => 'DSA/DSS',
            'type' => 'asymmetrical signing or digital signature algorithm',
            'key size in bits' => static::KEY_SIZE,
            'signature digestion algorithm' => $this->digestion,
            'private key' => $this->privateKey,
            'public key' => $this->publicKey,
        ];
    }

    /**
     * Generates a signature of the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signData($plainData)
    {
        $this->checkIfThePrivateKeyIsSet();
        $this->validatePlainData($plainData);

        $privateKeyResource = openssl_pkey_get_private(base64_decode($this->privateKey));

        // @codeCoverageIgnoreStart
        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to use the current private key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }
        // @codeCoverageIgnoreEnd

        $signatureData = '';
        openssl_sign($plainData, $signatureData, $privateKeyResource, $this->digestion);

        // Free the private key (resource cleanup)
        @openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        return $this->changeOutputFormat($signatureData, true);
    }

    /**
     * Verifies that the signature is correct for the given plain data.
     *
     * @param string $signatureData The signature input string.
     * @param string $plainData The plain input string.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyDataSignature($signatureData, $plainData)
    {
        $this->checkIfThePublicKeyIsSet();
        $this->validatePlainData($plainData);
        $this->validateCipherOrSignatureData($signatureData);

        $signatureData = $this->changeOutputFormat($signatureData, false);
        $publicKeyResource = openssl_pkey_get_public(base64_decode($this->publicKey));

        // @codeCoverageIgnoreStart
        if ($publicKeyResource === false) {
            throw new \RuntimeException(
                'Failed to use the current public key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }
        // @codeCoverageIgnoreEnd

        $verified = (openssl_verify($plainData, $signatureData, $publicKeyResource, $this->digestion) === 1);

        // Free the public key (resource cleanup)
        @openssl_free_key($publicKeyResource);
        $publicKeyResource = null;

        return $verified;
    }
}
