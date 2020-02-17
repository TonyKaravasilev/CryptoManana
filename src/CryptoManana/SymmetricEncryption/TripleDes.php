<?php

/**
 * The 3DES-168 (T-DES) encryption algorithm class.
 */

namespace CryptoManana\SymmetricEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipherAlgorithm;

/**
 * Class TripleDes - The 3DES-168 (T-DES) encryption algorithm object.
 *
 * @package CryptoManana\SymmetricEncryption
 */
class TripleDes extends SymmetricBlockCipherAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'DES-EDE3';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 192 bits (24 bytes), but 168 bits (21 bytes) are usable
     */
    const KEY_SIZE = 24;

    /**
     * The internal initialization vector (IV) size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 64 bits (8 bytes)
     */
    const IV_SIZE = 8;

    /**
     * The internal operational block size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 64 bits (8 bytes)
     */
    const BLOCK_SIZE = 8;

    /**
     * List of valid block operation modes.
     *
     * @var array Block mode codes.
     */
    protected static $validBlockModes = [
        self::CBC_MODE,
        self::CFB_MODE,
        self::OFB_MODE,
        self::ECB_MODE
    ];

    /**
     * Fetch the correctly formatted internal encryption algorithm method name.
     *
     * @return string The symmetric encryption algorithm standard.
     */
    protected function fetchAlgorithmMethodName()
    {
        return ($this->mode === self::ECB_MODE) ? static::ALGORITHM_NAME : static::ALGORITHM_NAME . '-' . $this->mode;
    }

    /**
     * Internal method for the validation of the system support of the given block operation mode.
     *
     * @param string $mode The block mode name.
     *
     * @throws \Exception Validation errors.
     *
     * @codeCoverageIgnore
     */
    protected function validateBlockModeSupport($mode)
    {
        $mode = strtoupper($mode);

        $methodName = ($mode === self::ECB_MODE) ? static::ALGORITHM_NAME : static::ALGORITHM_NAME . '-' . $mode;

        if (!in_array($methodName, openssl_get_cipher_methods(), true)) {
            throw new \RuntimeException(
                'The algorithm `' . $methodName . '`is not supported under the current system configuration.'
            );
        }
    }
}
