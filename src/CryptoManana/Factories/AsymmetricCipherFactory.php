<?php

/**
 * Factory for object asymmetric encryption/signature algorithm object instancing.
 */

namespace CryptoManana\Factories;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory as FactoryPattern;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as EncryptionAlgorithm;
use CryptoManana\AsymmetricEncryption\Rsa1024 as Rsa1024;
use CryptoManana\AsymmetricEncryption\Rsa2048 as Rsa2048;
use CryptoManana\AsymmetricEncryption\Rsa3072 as Rsa3072;
use CryptoManana\AsymmetricEncryption\Rsa4096 as Rsa4096;
use CryptoManana\AsymmetricEncryption\Dsa1024 as Dsa1024;
use CryptoManana\AsymmetricEncryption\Dsa2048 as Dsa2048;
use CryptoManana\AsymmetricEncryption\Dsa3072 as Dsa3072;
use CryptoManana\AsymmetricEncryption\Dsa4096 as Dsa4096;

/**
 * Class AsymmetricCipherFactory - Factory for object asymmetric encryption/signature algorithm object instancing.
 *
 * @package CryptoManana\Factories
 */
class AsymmetricCipherFactory extends FactoryPattern
{
    /**
     * The RSA-1024 type.
     */
    const RSA_1024 = Rsa1024::class;

    /**
     * The RSA-2048 type.
     */
    const RSA_2048 = Rsa2048::class;

    /**
     * The RSA-3072 type.
     */
    const RSA_3072 = Rsa3072::class;

    /**
     * The RSA-4096 type.
     */
    const RSA_4096 = Rsa4096::class;

    /**
     * The DSA-1024 type.
     */
    const DSA_1024 = Dsa1024::class;

    /**
     * The DSA-2048 type.
     */
    const DSA_2048 = Dsa2048::class;

    /**
     * The DSA-3072 type.
     */
    const DSA_3072 = Dsa3072::class;

    /**
     * The DSA-4096 type.
     */
    const DSA_4096 = Dsa4096::class;

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            self::class . '::RSA_1024' => self::RSA_1024,
            self::class . '::RSA_2048' => self::RSA_2048,
            self::class . '::RSA_3072' => self::RSA_3072,
            self::class . '::RSA_4096' => self::RSA_4096,
            self::class . '::DSA_1024' => self::DSA_1024,
            self::class . '::DSA_2048' => self::DSA_2048,
            self::class . '::DSA_3072' => self::DSA_3072,
            self::class . '::DSA_4096' => self::DSA_4096,
        ];
    }

    /**
     * Create an encryption or digital signature algorithm object
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return EncryptionAlgorithm|object|null An encryption/signature algorithm object or null.
     */
    public function create($type)
    {
        return self::createInstance($type);
    }

    /**
     * Create an encryption or digital signature algorithm object
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return EncryptionAlgorithm|object|null An encryption/signature algorithm object or null.
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
}
