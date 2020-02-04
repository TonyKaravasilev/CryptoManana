<?php

/**
 * The RC4-128 encryption algorithm class.
 */

namespace CryptoManana\SymmetricEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractStreamCipherAlgorithm as SymmetricStreamCipherAlgorithm;

/**
 * Class Rc4 - The RC4-128 encryption algorithm object.
 *
 * @package CryptoManana\SymmetricEncryption
 */
class Rc4 extends SymmetricStreamCipherAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'RC4';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 128 bits (16 bytes)
     */
    const KEY_SIZE = 16;

    /**
     * Flag to force native code polyfill realizations, if available.
     *
     * @var bool Flag to force native realizations.
     */
    protected $useNative = false;

    /**
     * Stream cipher algorithm constructor.
     */
    public function __construct()
    {
        if (in_array(static::ALGORITHM_NAME, openssl_get_cipher_methods(), true)) {
            parent::__construct();
        } else {
            // @codeCoverageIgnoreStart
            $this->useNative = true;

            /**
             * {@internal Serialization and initialization purposes for the default key. }}
             */
            if (strlen($this->key) < static::KEY_SIZE) {
                $this->key = str_pad($this->key, static::KEY_SIZE, "\x0", STR_PAD_RIGHT);
            }
            // @codeCoverageIgnoreEnd
        }
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
        if ($this->useNative) {
            return parent::encryptData($plainData);
        } else {
            $this->validatePlainDataForEncryption($plainData);

            $plainData = ($plainData === '') ? ' ' : $plainData;

            $cipherData = \CryptoManana\Compatibility\NativeRc4::encryptData($this->key, $plainData);

            return $this->changeOutputFormat($cipherData, true);
        }
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
        if ($this->useNative) {
            return parent::decryptData($cipherData);
        } else {
            $this->validateCipherDataForDecryption($cipherData);

            $cipherData = $this->changeOutputFormat($cipherData, false);

            return \CryptoManana\Compatibility\NativeRc4::decryptData($this->key, $cipherData);
        }
    }
}
