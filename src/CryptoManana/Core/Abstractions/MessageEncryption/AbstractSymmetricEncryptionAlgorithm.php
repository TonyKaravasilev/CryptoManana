<?php

/**
 * The symmetric encryption algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as SymmetricDataEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\CipherDataFormatsInterface as CipherDataFormatting;
use CryptoManana\Core\Interfaces\MessageEncryption\SecretKeyInterface as SecretKeyCipher;
use CryptoManana\Core\Traits\MessageEncryption\CipherDataFormatsTrait as CipherDataFormats;
use CryptoManana\Core\Traits\MessageEncryption\SecretKeyTrait as TwoWaySecretKey;

/**
 * Class AbstractSymmetricEncryptionAlgorithm - The symmetric encryption algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin TwoWaySecretKey
 * @mixin CipherDataFormats
 */
abstract class AbstractSymmetricEncryptionAlgorithm implements
    SymmetricDataEncryption,
    SecretKeyCipher,
    CipherDataFormatting
{
    /**
     * Secret encryption key capabilities.
     *
     * {@internal Reusable implementation of `SecretKeyInterface`. }}
     */
    use TwoWaySecretKey;

    /**
     * Cipher data outputting formats.
     *
     * {@internal Reusable implementation of `CipherDataFormatsInterface`. }}
     */
    use CipherDataFormats;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'none';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     */
    const KEY_SIZE = 0;

    /**
     * The encryption/decryption secret key property storage.
     *
     * @var string The encryption/decryption secret key string value.
     */
    protected $key = '';

    /**
     * The output cipher format property storage.
     *
     * @var int The output cipher format integer code value.
     */
    protected $cipherFormat = self::ENCRYPTION_OUTPUT_BASE_64_URL;

    /**
     * Fetch the correctly formatted internal encryption algorithm method name.
     *
     * @return string The symmetric encryption algorithm standard.
     */
    abstract protected function fetchAlgorithmMethodName();

    /**
     * Internal method for the validation of plain data used at encryption operations.
     *
     * @param string $plainData The plain input string.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validatePlainDataForEncryption($plainData);

    /**
     * Internal method for the validation of cipher data used at decryption operations.
     *
     * @param string $cipherData The encrypted input string.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validateCipherDataForDecryption($cipherData);

    /**
     * Symmetrical encryption algorithm constructor.
     */
    abstract public function __construct();
}
