<?php

/**
 * Factory object for hash algorithm object instancing.
 */

namespace CryptoManana\Factories;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory as FactoryPattern;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;
use \CryptoManana\Hashing\Md5 as Md5;
use \CryptoManana\Hashing\Sha1 as Sha1;
use \CryptoManana\Hashing\ShaTwo224 as ShaTwo224;
use \CryptoManana\Hashing\ShaTwo256 as ShaTwo256;
use \CryptoManana\Hashing\ShaTwo384 as ShaTwo384;
use \CryptoManana\Hashing\ShaTwo512 as ShaTwo512;
use \CryptoManana\Hashing\ShaThree224 as ShaThree224;
use \CryptoManana\Hashing\ShaThree256 as ShaThree256;
use \CryptoManana\Hashing\ShaThree384 as ShaThree384;
use \CryptoManana\Hashing\ShaThree512 as ShaThree512;
use \CryptoManana\Hashing\Ripemd128 as Ripemd128;
use \CryptoManana\Hashing\Ripemd160 as Ripemd160;
use \CryptoManana\Hashing\Ripemd256 as Ripemd256;
use \CryptoManana\Hashing\Ripemd320 as Ripemd320;
use \CryptoManana\Hashing\Whirlpool as Whirlpool;
use \CryptoManana\Hashing\HmacMd5 as HmacMd5;
use \CryptoManana\Hashing\HmacSha1 as HmacSha1;
use \CryptoManana\Hashing\HmacShaThree224 as HmacShaThree224;
use \CryptoManana\Hashing\HmacShaThree256 as HmacShaThree256;
use \CryptoManana\Hashing\HmacShaThree384 as HmacShaThree384;
use \CryptoManana\Hashing\HmacShaThree512 as HmacShaThree512;
use \CryptoManana\Hashing\HmacShaTwo224 as HmacShaTwo224;
use \CryptoManana\Hashing\HmacShaTwo256 as HmacShaTwo256;
use \CryptoManana\Hashing\HmacShaTwo384 as HmacShaTwo384;
use \CryptoManana\Hashing\HmacShaTwo512 as HmacShaTwo512;
use \CryptoManana\Hashing\HmacRipemd128 as HmacRipemd128;
use \CryptoManana\Hashing\HmacRipemd160 as HmacRipemd160;
use \CryptoManana\Hashing\HmacRipemd256 as HmacRipemd256;
use \CryptoManana\Hashing\HmacRipemd320 as HmacRipemd320;
use \CryptoManana\Hashing\HmacWhirlpool as HmacWhirlpool;
use \CryptoManana\Hashing\HkdfMd5 as HkdfMd5;
use \CryptoManana\Hashing\HkdfSha1 as HkdfSha1;
use \CryptoManana\Hashing\HkdfShaThree224 as HkdfShaThree224;
use \CryptoManana\Hashing\HkdfShaThree256 as HkdfShaThree256;
use \CryptoManana\Hashing\HkdfShaThree384 as HkdfShaThree384;
use \CryptoManana\Hashing\HkdfShaThree512 as HkdfShaThree512;
use \CryptoManana\Hashing\HkdfShaTwo224 as HkdfShaTwo224;
use \CryptoManana\Hashing\HkdfShaTwo256 as HkdfShaTwo256;
use \CryptoManana\Hashing\HkdfShaTwo384 as HkdfShaTwo384;
use \CryptoManana\Hashing\HkdfShaTwo512 as HkdfShaTwo512;
use \CryptoManana\Hashing\HkdfRipemd128 as HkdfRipemd128;
use \CryptoManana\Hashing\HkdfRipemd160 as HkdfRipemd160;
use \CryptoManana\Hashing\HkdfRipemd256 as HkdfRipemd256;
use \CryptoManana\Hashing\HkdfRipemd320 as HkdfRipemd320;
use \CryptoManana\Hashing\HkdfWhirlpool as HkdfWhirlpool;
use \CryptoManana\Hashing\Pbkdf2Md5 as Pbkdf2Md5;
use \CryptoManana\Hashing\Pbkdf2Sha1 as Pbkdf2Sha1;
use \CryptoManana\Hashing\Pbkdf2ShaThree224 as Pbkdf2ShaThree224;
use \CryptoManana\Hashing\Pbkdf2ShaThree256 as Pbkdf2ShaThree256;
use \CryptoManana\Hashing\Pbkdf2ShaThree384 as Pbkdf2ShaThree384;
use \CryptoManana\Hashing\Pbkdf2ShaThree512 as Pbkdf2ShaThree512;
use \CryptoManana\Hashing\Pbkdf2ShaTwo224 as Pbkdf2ShaTwo224;
use \CryptoManana\Hashing\Pbkdf2ShaTwo256 as Pbkdf2ShaTwo256;
use \CryptoManana\Hashing\Pbkdf2ShaTwo384 as Pbkdf2ShaTwo384;
use \CryptoManana\Hashing\Pbkdf2ShaTwo512 as Pbkdf2ShaTwo512;
use \CryptoManana\Hashing\Pbkdf2Ripemd128 as Pbkdf2Ripemd128;
use \CryptoManana\Hashing\Pbkdf2Ripemd160 as Pbkdf2Ripemd160;
use \CryptoManana\Hashing\Pbkdf2Ripemd256 as Pbkdf2Ripemd256;
use \CryptoManana\Hashing\Pbkdf2Ripemd320 as Pbkdf2Ripemd320;
use \CryptoManana\Hashing\Pbkdf2Whirlpool as Pbkdf2Whirlpool;
use \CryptoManana\Hashing\Bcrypt as Bcrypt;
use \CryptoManana\Hashing\Argon2 as Argon2;

/**
 * Class HashAlgorithmFactory - Factory object for hash algorithm object instancing.
 *
 * @package CryptoManana\Factories
 */
class HashAlgorithmFactory extends FactoryPattern
{
    /**
     * The MD5 type.
     */
    const MD5 = Md5::class;

    /**
     * The HMAC-MD5 type.
     */
    const HMAC_MD5 = HmacMd5::class;

    /**
     * The HKDF-MD5 type.
     */
    const HKDF_MD5 = HkdfMd5::class;

    /**
     * The PBKDF2-MD5 type.
     */
    const PBKDF2_MD5 = Pbkdf2Md5::class;

    /**
     * The SHA-1 type.
     */
    const SHA1 = Sha1::class;

    /**
     * The HMAC-SHA-1 type.
     */
    const HMAC_SHA1 = HmacSha1::class;

    /**
     * The HKDF-SHA-1 type.
     */
    const HKDF_SHA1 = HkdfSha1::class;

    /**
     * The PBKDF2-SHA-1 type.
     */
    const PBKDF2_SHA1 = Pbkdf2Sha1::class;

    /**
     * The SHA-2-224 type.
     */
    const SHA2_224 = ShaTwo224::class;

    /**
     * The HMAC-SHA-2-224 type.
     */
    const HMAC_SHA2_224 = HmacShaTwo224::class;

    /**
     * The HKDF-SHA-2-224 type.
     */
    const HKDF_SHA2_224 = HkdfShaTwo224::class;

    /**
     * The PBKDF2-SHA-2-224 type.
     */
    const PBKDF2_SHA2_224 = Pbkdf2ShaTwo224::class;

    /**
     * The SHA-2-256 type.
     */
    const SHA2_256 = ShaTwo256::class;

    /**
     * The HMAC-SHA-2-256 type.
     */
    const HMAC_SHA2_256 = HmacShaTwo256::class;

    /**
     * The HKDF-SHA-2-256 type.
     */
    const HKDF_SHA2_256 = HkdfShaTwo256::class;

    /**
     * The PBKDF2-SHA-2-256 type.
     */
    const PBKDF2_SHA2_256 = Pbkdf2ShaTwo256::class;

    /**
     * The SHA-2-384 type.
     */
    const SHA2_384 = ShaTwo384::class;

    /**
     * The HMAC-SHA-2-384 type.
     */
    const HMAC_SHA2_384 = HmacShaTwo384::class;

    /**
     * The HKDF-SHA-2-384 type.
     */
    const HKDF_SHA2_384 = HkdfShaTwo384::class;

    /**
     * The PBKDF2-SHA-2-384 type.
     */
    const PBKDF2_SHA2_384 = Pbkdf2ShaTwo384::class;

    /**
     * The SHA-2-512 type.
     */
    const SHA2_512 = ShaTwo512::class;

    /**
     * The HMAC-SHA-2-512 type.
     */
    const HMAC_SHA2_512 = HmacShaTwo512::class;

    /**
     * The HKDF-SHA-2-512 type.
     */
    const HKDF_SHA2_512 = HkdfShaTwo512::class;

    /**
     * The PBKDF2-SHA-2-512 type.
     */
    const PBKDF2_SHA2_512 = Pbkdf2ShaTwo512::class;

    /**
     * The SHA-3-224 type.
     */
    const SHA3_224 = ShaThree224::class;

    /**
     * The HMAC-SHA-3-224 type.
     */
    const HMAC_SHA3_224 = HmacShaThree224::class;

    /**
     * The HKDF-SHA-3-224 type.
     */
    const HKDF_SHA3_224 = HkdfShaThree224::class;

    /**
     * The PBKDF2-SHA-3-224 type.
     */
    const PBKDF2_SHA3_224 = Pbkdf2ShaThree224::class;

    /**
     * The SHA-3-256 type.
     */
    const SHA3_256 = ShaThree256::class;

    /**
     * The HMAC-SHA-3-256 type.
     */
    const HMAC_SHA3_256 = HmacShaThree256::class;

    /**
     * The HKDF-SHA-3-256 type.
     */
    const HKDF_SHA3_256 = HkdfShaThree256::class;

    /**
     * The PBKDF2-SHA-3-256 type.
     */
    const PBKDF2_SHA3_256 = Pbkdf2ShaThree256::class;

    /**
     * The SHA-3-384 type.
     */
    const SHA3_384 = ShaThree384::class;

    /**
     * The HMAC-SHA-3-384 type.
     */
    const HMAC_SHA3_384 = HmacShaThree384::class;

    /**
     * The HKDF-SHA-3-384 type.
     */
    const HKDF_SHA3_384 = HkdfShaThree384::class;

    /**
     * The PBKDF2-SHA-3-384 type.
     */
    const PBKDF2_SHA3_384 = Pbkdf2ShaThree384::class;

    /**
     * The SHA-3-512 type.
     */
    const SHA3_512 = ShaThree512::class;

    /**
     * The HMAC-SHA-3-512 type.
     */
    const HMAC_SHA3_512 = HmacShaThree512::class;

    /**
     * The HKDF-SHA-3-512 type.
     */
    const HKDF_SHA3_512 = HkdfShaThree512::class;

    /**
     * The PBKDF2-SHA-3-512 type.
     */
    const PBKDF2_SHA3_512 = Pbkdf2ShaThree512::class;

    /**
     * The RIPEMD-128 type.
     */
    const RIPEMD_128 = Ripemd128::class;

    /**
     * The HMAC-RIPEMD-128 type.
     */
    const HMAC_RIPEMD_128 = HmacRipemd128::class;

    /**
     * The HKDF-RIPEMD-128 type.
     */
    const HKDF_RIPEMD_128 = HkdfRipemd128::class;

    /**
     * The PBKDF2-RIPEMD-128 type.
     */
    const PBKDF2_RIPEMD_128 = Pbkdf2Ripemd128::class;

    /**
     * The RIPEMD-160 type.
     */
    const RIPEMD_160 = Ripemd160::class;

    /**
     * The HMAC-RIPEMD-160 type.
     */
    const HMAC_RIPEMD_160 = HmacRipemd160::class;

    /**
     * The HKDF-RIPEMD-160 type.
     */
    const HKDF_RIPEMD_160 = HkdfRipemd160::class;

    /**
     * The PBKDF2-RIPEMD-160 type.
     */
    const PBKDF2_RIPEMD_160 = Pbkdf2Ripemd160::class;

    /**
     * The RIPEMD-256 type.
     */
    const RIPEMD_256 = Ripemd256::class;

    /**
     * The HMAC-RIPEMD-256 type.
     */
    const HMAC_RIPEMD_256 = HmacRipemd256::class;

    /**
     * The HKDF-RIPEMD-256 type.
     */
    const HKDF_RIPEMD_256 = HkdfRipemd256::class;

    /**
     * The PBKDF2-RIPEMD-256 type.
     */
    const PBKDF2_RIPEMD_256 = Pbkdf2Ripemd256::class;

    /**
     * The RIPEMD-320 type.
     */
    const RIPEMD_320 = Ripemd320::class;

    /**
     * The HMAC-RIPEMD-320 type.
     */
    const HMAC_RIPEMD_320 = HmacRipemd320::class;

    /**
     * The HKDF-RIPEMD-320 type.
     */
    const HKDF_RIPEMD_320 = HkdfRipemd320::class;

    /**
     * The PBKDF2-RIPEMD-320 type.
     */
    const PBKDF2_RIPEMD_320 = Pbkdf2Ripemd320::class;

    /**
     * The Whirlpool type.
     */
    const WHIRLPOOL = Whirlpool::class;

    /**
     * The HMAC-Whirlpool type.
     */
    const HMAC_WHIRLPOOL = HmacWhirlpool::class;

    /**
     * The HKDF-Whirlpool type.
     */
    const HKDF_WHIRLPOOL = HkdfWhirlpool::class;

    /**
     * The PBKDF2-Whirlpool type.
     */
    const PBKDF2_WHIRLPOOL = Pbkdf2Whirlpool::class;

    /**
     * The Bcrypt type.
     */
    const BCRYPT = Bcrypt::class;

    /**
     * The Argon2 type.
     */
    const ARGON2 = Argon2::class;

    /**
     * Get the array of containing all supported unkeyed hash algorithms by the factory.
     *
     * @return array An array of available unkeyed hash algorithms.
     */
    protected static function getUnkeyedHashAlgorithms()
    {
        return [
            self::class . '::MD5' => self::MD5,
            self::class . '::SHA1' => self::SHA1,
            self::class . '::SHA2_224' => self::SHA2_224,
            self::class . '::SHA2_256' => self::SHA2_256,
            self::class . '::SHA2_384' => self::SHA2_384,
            self::class . '::SHA2_512' => self::SHA2_512,
            self::class . '::SHA3_224' => self::SHA3_224,
            self::class . '::SHA3_256' => self::SHA3_256,
            self::class . '::SHA3_384' => self::SHA3_384,
            self::class . '::SHA3_512' => self::SHA3_512,
            self::class . '::RIPEMD_128' => self::RIPEMD_128,
            self::class . '::RIPEMD_160' => self::RIPEMD_160,
            self::class . '::RIPEMD_256' => self::RIPEMD_256,
            self::class . '::RIPEMD_320' => self::RIPEMD_320,
            self::class . '::WHIRLPOOL' => self::WHIRLPOOL,
        ];
    }

    /**
     * Get the array of containing all supported keyed hash algorithms by the factory.
     *
     * @return array An array of available keyed hash algorithms.
     */
    protected static function getKeyedHashAlgorithms()
    {
        return [
            self::class . '::HMAC_MD5' => self::HMAC_MD5,
            self::class . '::HMAC_SHA1' => self::HMAC_SHA1,
            self::class . '::HMAC_SHA2_224' => self::HMAC_SHA2_224,
            self::class . '::HMAC_SHA2_256' => self::HMAC_SHA2_256,
            self::class . '::HMAC_SHA2_384' => self::HMAC_SHA2_384,
            self::class . '::HMAC_SHA2_512' => self::HMAC_SHA2_512,
            self::class . '::HMAC_SHA3_224' => self::HMAC_SHA3_224,
            self::class . '::HMAC_SHA3_256' => self::HMAC_SHA3_256,
            self::class . '::HMAC_SHA3_384' => self::HMAC_SHA3_384,
            self::class . '::HMAC_SHA3_512' => self::HMAC_SHA3_512,
            self::class . '::HMAC_RIPEMD_128' => self::HMAC_RIPEMD_128,
            self::class . '::HMAC_RIPEMD_160' => self::HMAC_RIPEMD_160,
            self::class . '::HMAC_RIPEMD_256' => self::HMAC_RIPEMD_256,
            self::class . '::HMAC_RIPEMD_320' => self::HMAC_RIPEMD_320,
            self::class . '::HMAC_WHIRLPOOL' => self::HMAC_WHIRLPOOL,
        ];
    }

    /**
     * Get the array of containing all supported key derivation algorithms by the factory.
     *
     * @return array An array of available key derivation algorithms.
     */
    protected static function getKeyDerivationAlgorithms()
    {
        return [
            self::class . '::HKDF_MD5' => self::HKDF_MD5,
            self::class . '::HKDF_SHA1' => self::HKDF_SHA1,
            self::class . '::HKDF_SHA2_224' => self::HKDF_SHA2_224,
            self::class . '::HKDF_SHA2_256' => self::HKDF_SHA2_256,
            self::class . '::HKDF_SHA2_384' => self::HKDF_SHA2_384,
            self::class . '::HKDF_SHA2_512' => self::HKDF_SHA2_512,
            self::class . '::HKDF_SHA3_224' => self::HKDF_SHA3_224,
            self::class . '::HKDF_SHA3_256' => self::HKDF_SHA3_256,
            self::class . '::HKDF_SHA3_384' => self::HKDF_SHA3_384,
            self::class . '::HKDF_SHA3_512' => self::HKDF_SHA3_512,
            self::class . '::HKDF_RIPEMD_128' => self::HKDF_RIPEMD_128,
            self::class . '::HKDF_RIPEMD_160' => self::HKDF_RIPEMD_160,
            self::class . '::HKDF_RIPEMD_256' => self::HKDF_RIPEMD_256,
            self::class . '::HKDF_RIPEMD_320' => self::HKDF_RIPEMD_320,
            self::class . '::HKDF_WHIRLPOOL' => self::HKDF_WHIRLPOOL,
        ];
    }

    /**
     * Get the array of containing all supported password-based derivation algorithms by the factory.
     *
     * @return array An array of available password-based derivation algorithms.
     */
    protected static function getPasswordDerivationAlgorithms()
    {
        return [
            self::class . '::PBKDF2_MD5' => self::PBKDF2_MD5,
            self::class . '::PBKDF2_SHA1' => self::PBKDF2_SHA1,
            self::class . '::PBKDF2_SHA2_224' => self::PBKDF2_SHA2_224,
            self::class . '::PBKDF2_SHA2_256' => self::PBKDF2_SHA2_256,
            self::class . '::PBKDF2_SHA2_384' => self::PBKDF2_SHA2_384,
            self::class . '::PBKDF2_SHA2_512' => self::PBKDF2_SHA2_512,
            self::class . '::PBKDF2_SHA3_224' => self::PBKDF2_SHA3_224,
            self::class . '::PBKDF2_SHA3_256' => self::PBKDF2_SHA3_256,
            self::class . '::PBKDF2_SHA3_384' => self::PBKDF2_SHA3_384,
            self::class . '::PBKDF2_SHA3_512' => self::PBKDF2_SHA3_512,
            self::class . '::PBKDF2_RIPEMD_128' => self::PBKDF2_RIPEMD_128,
            self::class . '::PBKDF2_RIPEMD_160' => self::PBKDF2_RIPEMD_160,
            self::class . '::PBKDF2_RIPEMD_256' => self::PBKDF2_RIPEMD_256,
            self::class . '::PBKDF2_RIPEMD_320' => self::PBKDF2_RIPEMD_320,
            self::class . '::PBKDF2_WHIRLPOOL' => self::PBKDF2_WHIRLPOOL,
            self::class . '::BCRYPT' => self::BCRYPT,
            self::class . '::ARGON2' => self::ARGON2,
        ];
    }

    /**
     * Create a hash algorithm object.
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return HashAlgorithm|object|null A hash algorithm object or null.
     */
    public function create($type)
    {
        return self::createInstance($type);
    }

    /**
     * Create a hash algorithm object
     *
     * @param string|null $type The algorithm class name as type for creation.
     *
     * @return HashAlgorithm|object|null A hash algorithm object or null.
     */
    public static function createInstance($type)
    {
        /**
         * Check if class exists and has a correct base class
         *
         * @var HashAlgorithm|null $exception Object instance.
         */
        if (class_exists($type) && is_subclass_of($type, HashAlgorithm::class)) {
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
        $supportedAlgorithms = array_merge(self::getUnkeyedHashAlgorithms(), self::getKeyedHashAlgorithms());
        $supportedAlgorithms = array_merge($supportedAlgorithms, self::getKeyDerivationAlgorithms());
        $supportedAlgorithms = array_merge($supportedAlgorithms, self::getPasswordDerivationAlgorithms());

        return $supportedAlgorithms;
    }
}
