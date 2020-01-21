<?php

/**
 * Factory object encryption algorithm object instancing.
 */

namespace CryptoManana\Factories;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory as FactoryPattern;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractSymmetricEncryptionAlgorithm as EncryptionAlgorithm;
use \CryptoManana\SymmetricEncryption\Aes128 as Aes128;
use \CryptoManana\SymmetricEncryption\Aes192 as Aes192;
use \CryptoManana\SymmetricEncryption\Aes256 as Aes256;
use \CryptoManana\SymmetricEncryption\Camellia128 as Camellia128;
use \CryptoManana\SymmetricEncryption\Camellia192 as Camellia192;
use \CryptoManana\SymmetricEncryption\Camellia256 as Camellia256;

/**
 * Class SymmetricCipherFactory - Factory object encryption algorithm object instancing.
 *
 * @package CryptoManana\Factories
 */
class SymmetricCipherFactory extends FactoryPattern
{
    /**
     * The AES-128 type.
     */
    const AES_128 = Aes128::class;

    /**
     * The AES-192 type.
     */
    const AES_192 = Aes192::class;

    /**
     * The AES-256 type.
     */
    const AES_256 = Aes256::class;

    /**
     * The CAMELLIA-128 type.
     */
    const CAMELLIA_128 = Camellia128::class;

    /**
     * The CAMELLIA-192 type.
     */
    const CAMELLIA_192 = Camellia192::class;

    /**
     * The CAMELLIA-256 type.
     */
    const CAMELLIA_256 = Camellia256::class;

    /**
     * Create an encryption algorithm object
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return EncryptionAlgorithm|object|null An encryption algorithm object or null.
     */
    public function create($type)
    {
        return self::createInstance($type);
    }

    /**
     * Create an encryption algorithm object
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return EncryptionAlgorithm|object|null An encryption algorithm object or null.
     */
    public static function createInstance($type)
    {
        /**
         * Check if class exists and has a correct base class
         *
         * @var EncryptionAlgorithm|null $exception Object instance.
         */
        if (class_exists($type) && is_subclass_of($type, EncryptionAlgorithm::class)) {
            $exception = new $type();
        } else {
            $exception = null; // Invalid type given
        }

        return $exception;
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            self::class . '::AES_128' => self::AES_128,
            self::class . '::AES_192' => self::AES_192,
            self::class . '::AES_256' => self::AES_256,
            self::class . '::CAMELLIA_128' => self::CAMELLIA_128,
            self::class . '::CAMELLIA_192' => self::CAMELLIA_192,
            self::class . '::CAMELLIA_256' => self::CAMELLIA_256,
        ];
    }
}
