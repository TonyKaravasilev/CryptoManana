<?php

/**
 * The asymmetric encryption/signature algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\KeyPairInterface as PublicKeyCipher;
use CryptoManana\Core\Traits\MessageEncryption\KeyPairTrait as PublicPrivateKeyPair;

/**
 * Class AbstractAsymmetricEncryptionAlgorithm - The asymmetric algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageEncryption
 *
 * @mixin PublicPrivateKeyPair
 */
abstract class AbstractAsymmetricEncryptionAlgorithm implements PublicKeyCipher
{
    /**
     * The asymmetric public and private key capabilities.
     *
     * {@internal Reusable implementation of `KeyPairInterface`. }}
     */
    use PublicPrivateKeyPair;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'none';

    /**
     * The internal key size measured in raw bytes length for the algorithm
     */
    const KEY_SIZE = 0;

    /**
     * The private key string property storage.
     *
     * @var string The private key string value.
     */
    protected $privateKey = '';

    /**
     * The public key string property storage.
     *
     * @var string The public key string value.
     */
    protected $publicKey = '';

    /**
     * Internal method for the validation of plain data used at encryption/signing operations.
     *
     * @param string $plainData The plain input string.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validatePlainData($plainData);

    /**
     * Internal method for the validation of cipher/signature data used at decryption/verifying operations.
     *
     * @param string $cipherOrSignatureData The encrypted input string or a signature string.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validateCipherOrSignatureData($cipherOrSignatureData);

    /**
     * Asymmetrical algorithm constructor.
     */
    abstract public function __construct();
}
