<?php

/**
 * Testing the HashAlgorithmFactory component used for easier hash algorithm object instancing.
 */

namespace CryptoManana\Tests\TestSuite\Factories;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHardwareResistantDerivation;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractIterativeSlowDerivation;
use CryptoManana\Hashing\Md5;
use CryptoManana\Hashing\Sha1;
use CryptoManana\Hashing\ShaTwo224;
use CryptoManana\Hashing\ShaTwo256;
use CryptoManana\Hashing\ShaTwo384;
use CryptoManana\Hashing\ShaTwo512;
use CryptoManana\Hashing\ShaThree224;
use CryptoManana\Hashing\ShaThree256;
use CryptoManana\Hashing\ShaThree384;
use CryptoManana\Hashing\ShaThree512;
use CryptoManana\Hashing\Ripemd128;
use CryptoManana\Hashing\Ripemd160;
use CryptoManana\Hashing\Ripemd256;
use CryptoManana\Hashing\Ripemd320;
use CryptoManana\Hashing\Whirlpool;
use CryptoManana\Hashing\HmacMd5;
use CryptoManana\Hashing\HmacSha1;
use CryptoManana\Hashing\HmacShaThree224;
use CryptoManana\Hashing\HmacShaThree256;
use CryptoManana\Hashing\HmacShaThree384;
use CryptoManana\Hashing\HmacShaThree512;
use CryptoManana\Hashing\HmacShaTwo224;
use CryptoManana\Hashing\HmacShaTwo256;
use CryptoManana\Hashing\HmacShaTwo384;
use CryptoManana\Hashing\HmacShaTwo512;
use CryptoManana\Hashing\HmacRipemd128;
use CryptoManana\Hashing\HmacRipemd160;
use CryptoManana\Hashing\HmacRipemd256;
use CryptoManana\Hashing\HmacRipemd320;
use CryptoManana\Hashing\HmacWhirlpool;
use CryptoManana\Hashing\HkdfMd5;
use CryptoManana\Hashing\HkdfSha1;
use CryptoManana\Hashing\HkdfShaThree224;
use CryptoManana\Hashing\HkdfShaThree256;
use CryptoManana\Hashing\HkdfShaThree384;
use CryptoManana\Hashing\HkdfShaThree512;
use CryptoManana\Hashing\HkdfShaTwo224;
use CryptoManana\Hashing\HkdfShaTwo256;
use CryptoManana\Hashing\HkdfShaTwo384;
use CryptoManana\Hashing\HkdfShaTwo512;
use CryptoManana\Hashing\HkdfRipemd128;
use CryptoManana\Hashing\HkdfRipemd160;
use CryptoManana\Hashing\HkdfRipemd256;
use CryptoManana\Hashing\HkdfRipemd320;
use CryptoManana\Hashing\HkdfWhirlpool;
use CryptoManana\Hashing\Pbkdf2Md5;
use CryptoManana\Hashing\Pbkdf2Sha1;
use CryptoManana\Hashing\Pbkdf2ShaThree224;
use CryptoManana\Hashing\Pbkdf2ShaThree256;
use CryptoManana\Hashing\Pbkdf2ShaThree384;
use CryptoManana\Hashing\Pbkdf2ShaThree512;
use CryptoManana\Hashing\Pbkdf2ShaTwo224;
use CryptoManana\Hashing\Pbkdf2ShaTwo256;
use CryptoManana\Hashing\Pbkdf2ShaTwo384;
use CryptoManana\Hashing\Pbkdf2ShaTwo512;
use CryptoManana\Hashing\Pbkdf2Ripemd128;
use CryptoManana\Hashing\Pbkdf2Ripemd160;
use CryptoManana\Hashing\Pbkdf2Ripemd256;
use CryptoManana\Hashing\Pbkdf2Ripemd320;
use CryptoManana\Hashing\Pbkdf2Whirlpool;
use CryptoManana\Hashing\Bcrypt;
use CryptoManana\Hashing\Argon2;
use CryptoManana\Factories\HashAlgorithmFactory;

/**
 * Class HashAlgorithmFactoryTest - Tests the hash algorithm factory class.
 *
 * @package CryptoManana\Tests\TestSuite\Factories
 */
final class HashAlgorithmFactoryTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return HashAlgorithmFactory Testing instance.
     */
    private function getHashAlgorithmFactoryForTesting()
    {
        return new HashAlgorithmFactory();
    }

    /**
     * Testing the cloning of an instance.
     */
    public function testCloningCapabilities()
    {
        $factory = $this->getHashAlgorithmFactoryForTesting();

        $tmp = clone $factory;

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::MD5));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $factory = $this->getHashAlgorithmFactoryForTesting();

        $tmp = serialize($factory);
        $tmp = unserialize($tmp);

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::MD5));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $factory = $this->getHashAlgorithmFactoryForTesting();

        $this->assertNotEmpty(var_export($factory, true));

        $reflection = new \ReflectionClass($factory);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($factory);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing the dynamic instancing calls.
     */
    public function testDynamicInstancingCalls()
    {
        $factory = $this->getHashAlgorithmFactoryForTesting();

        $this->assertTrue($factory instanceof HashAlgorithmFactory);
        $this->assertTrue($factory instanceof AbstractFactory);

        $tmp = $factory->create(HashAlgorithmFactory::MD5);
        $this->assertTrue($tmp instanceof Md5);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_MD5);
        $this->assertTrue($tmp instanceof HmacMd5);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_MD5);
        $this->assertTrue($tmp instanceof HkdfMd5);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_MD5);
        $this->assertTrue($tmp instanceof Pbkdf2Md5);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA1);
        $this->assertTrue($tmp instanceof Sha1);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA1);
        $this->assertTrue($tmp instanceof HmacSha1);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA1);
        $this->assertTrue($tmp instanceof HkdfSha1);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA1);
        $this->assertTrue($tmp instanceof Pbkdf2Sha1);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA2_224);
        $this->assertTrue($tmp instanceof ShaTwo224);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA2_224);
        $this->assertTrue($tmp instanceof HmacShaTwo224);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA2_224);
        $this->assertTrue($tmp instanceof HkdfShaTwo224);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA2_224);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo224);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA2_256);
        $this->assertTrue($tmp instanceof ShaTwo256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA2_256);
        $this->assertTrue($tmp instanceof HmacShaTwo256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA2_256);
        $this->assertTrue($tmp instanceof HkdfShaTwo256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA2_256);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA2_384);
        $this->assertTrue($tmp instanceof ShaTwo384);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA2_384);
        $this->assertTrue($tmp instanceof HmacShaTwo384);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA2_384);
        $this->assertTrue($tmp instanceof HkdfShaTwo384);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA2_384);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo384);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA2_512);
        $this->assertTrue($tmp instanceof ShaTwo512);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA2_512);
        $this->assertTrue($tmp instanceof HmacShaTwo512);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA2_512);
        $this->assertTrue($tmp instanceof HkdfShaTwo512);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA2_512);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo512);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA3_224);
        $this->assertTrue($tmp instanceof ShaThree224);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA3_224);
        $this->assertTrue($tmp instanceof HmacShaThree224);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA3_224);
        $this->assertTrue($tmp instanceof HkdfShaThree224);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA3_224);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree224);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA3_256);
        $this->assertTrue($tmp instanceof ShaThree256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA3_256);
        $this->assertTrue($tmp instanceof HmacShaThree256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA3_256);
        $this->assertTrue($tmp instanceof HkdfShaThree256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA3_256);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA3_384);
        $this->assertTrue($tmp instanceof ShaThree384);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA3_384);
        $this->assertTrue($tmp instanceof HmacShaThree384);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA3_384);
        $this->assertTrue($tmp instanceof HkdfShaThree384);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA3_384);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree384);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::SHA3_512);
        $this->assertTrue($tmp instanceof ShaThree512);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_SHA3_512);
        $this->assertTrue($tmp instanceof HmacShaThree512);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_SHA3_512);
        $this->assertTrue($tmp instanceof HkdfShaThree512);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_SHA3_512);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree512);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::RIPEMD_128);
        $this->assertTrue($tmp instanceof Ripemd128);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_RIPEMD_128);
        $this->assertTrue($tmp instanceof HmacRipemd128);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_RIPEMD_128);
        $this->assertTrue($tmp instanceof HkdfRipemd128);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_RIPEMD_128);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd128);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::RIPEMD_160);
        $this->assertTrue($tmp instanceof Ripemd160);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_RIPEMD_160);
        $this->assertTrue($tmp instanceof HmacRipemd160);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_RIPEMD_160);
        $this->assertTrue($tmp instanceof HkdfRipemd160);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_RIPEMD_160);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd160);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::RIPEMD_256);
        $this->assertTrue($tmp instanceof Ripemd256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_RIPEMD_256);
        $this->assertTrue($tmp instanceof HmacRipemd256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_RIPEMD_256);
        $this->assertTrue($tmp instanceof HkdfRipemd256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_RIPEMD_256);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::RIPEMD_320);
        $this->assertTrue($tmp instanceof Ripemd320);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_RIPEMD_320);
        $this->assertTrue($tmp instanceof HmacRipemd320);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_RIPEMD_320);
        $this->assertTrue($tmp instanceof HkdfRipemd320);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_RIPEMD_320);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd320);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::WHIRLPOOL);
        $this->assertTrue($tmp instanceof Whirlpool);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HMAC_WHIRLPOOL);
        $this->assertTrue($tmp instanceof HmacWhirlpool);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::HKDF_WHIRLPOOL);
        $this->assertTrue($tmp instanceof HkdfWhirlpool);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::PBKDF2_WHIRLPOOL);
        $this->assertTrue($tmp instanceof Pbkdf2Whirlpool);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = $factory->create(HashAlgorithmFactory::BCRYPT);
        $this->assertTrue($tmp instanceof Bcrypt);
        $this->assertTrue($tmp instanceof AbstractHardwareResistantDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        if (in_array(PASSWORD_ARGON2I, password_algos(), true)) {
            $tmp = $factory->create(HashAlgorithmFactory::ARGON2);
            $this->assertTrue($tmp instanceof Argon2);
            $this->assertTrue($tmp instanceof AbstractHardwareResistantDerivation);
            $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
            $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
            $this->assertTrue($tmp instanceof AbstractHashAlgorithm);
        }

        $this->assertNull($factory->create(\stdClass::class));
    }

    /**
     * Testing the static instancing calls.
     */
    public function testStaticInstancingCalls()
    {
        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::MD5);
        $this->assertTrue($tmp instanceof Md5);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_MD5);
        $this->assertTrue($tmp instanceof HmacMd5);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_MD5);
        $this->assertTrue($tmp instanceof HkdfMd5);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_MD5);
        $this->assertTrue($tmp instanceof Pbkdf2Md5);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA1);
        $this->assertTrue($tmp instanceof Sha1);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA1);
        $this->assertTrue($tmp instanceof HmacSha1);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA1);
        $this->assertTrue($tmp instanceof HkdfSha1);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA1);
        $this->assertTrue($tmp instanceof Pbkdf2Sha1);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA2_224);
        $this->assertTrue($tmp instanceof ShaTwo224);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA2_224);
        $this->assertTrue($tmp instanceof HmacShaTwo224);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA2_224);
        $this->assertTrue($tmp instanceof HkdfShaTwo224);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA2_224);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo224);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA2_256);
        $this->assertTrue($tmp instanceof ShaTwo256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA2_256);
        $this->assertTrue($tmp instanceof HmacShaTwo256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA2_256);
        $this->assertTrue($tmp instanceof HkdfShaTwo256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA2_256);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA2_384);
        $this->assertTrue($tmp instanceof ShaTwo384);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA2_384);
        $this->assertTrue($tmp instanceof HmacShaTwo384);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA2_384);
        $this->assertTrue($tmp instanceof HkdfShaTwo384);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA2_384);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo384);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA2_512);
        $this->assertTrue($tmp instanceof ShaTwo512);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA2_512);
        $this->assertTrue($tmp instanceof HmacShaTwo512);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA2_512);
        $this->assertTrue($tmp instanceof HkdfShaTwo512);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA2_512);
        $this->assertTrue($tmp instanceof Pbkdf2ShaTwo512);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA3_224);
        $this->assertTrue($tmp instanceof ShaThree224);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA3_224);
        $this->assertTrue($tmp instanceof HmacShaThree224);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA3_224);
        $this->assertTrue($tmp instanceof HkdfShaThree224);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA3_224);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree224);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA3_256);
        $this->assertTrue($tmp instanceof ShaThree256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA3_256);
        $this->assertTrue($tmp instanceof HmacShaThree256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA3_256);
        $this->assertTrue($tmp instanceof HkdfShaThree256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA3_256);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA3_384);
        $this->assertTrue($tmp instanceof ShaThree384);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA3_384);
        $this->assertTrue($tmp instanceof HmacShaThree384);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA3_384);
        $this->assertTrue($tmp instanceof HkdfShaThree384);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA3_384);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree384);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::SHA3_512);
        $this->assertTrue($tmp instanceof ShaThree512);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_SHA3_512);
        $this->assertTrue($tmp instanceof HmacShaThree512);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_SHA3_512);
        $this->assertTrue($tmp instanceof HkdfShaThree512);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_SHA3_512);
        $this->assertTrue($tmp instanceof Pbkdf2ShaThree512);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::RIPEMD_128);
        $this->assertTrue($tmp instanceof Ripemd128);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_RIPEMD_128);
        $this->assertTrue($tmp instanceof HmacRipemd128);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_RIPEMD_128);
        $this->assertTrue($tmp instanceof HkdfRipemd128);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_RIPEMD_128);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd128);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::RIPEMD_160);
        $this->assertTrue($tmp instanceof Ripemd160);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_RIPEMD_160);
        $this->assertTrue($tmp instanceof HmacRipemd160);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_RIPEMD_160);
        $this->assertTrue($tmp instanceof HkdfRipemd160);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_RIPEMD_160);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd160);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::RIPEMD_256);
        $this->assertTrue($tmp instanceof Ripemd256);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_RIPEMD_256);
        $this->assertTrue($tmp instanceof HmacRipemd256);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_RIPEMD_256);
        $this->assertTrue($tmp instanceof HkdfRipemd256);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_RIPEMD_256);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd256);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::RIPEMD_320);
        $this->assertTrue($tmp instanceof Ripemd320);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_RIPEMD_320);
        $this->assertTrue($tmp instanceof HmacRipemd320);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_RIPEMD_320);
        $this->assertTrue($tmp instanceof HkdfRipemd320);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_RIPEMD_320);
        $this->assertTrue($tmp instanceof Pbkdf2Ripemd320);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::WHIRLPOOL);
        $this->assertTrue($tmp instanceof Whirlpool);
        $this->assertTrue($tmp instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HMAC_WHIRLPOOL);
        $this->assertTrue($tmp instanceof HmacWhirlpool);
        $this->assertTrue($tmp instanceof AbstractKeyedHashFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::HKDF_WHIRLPOOL);
        $this->assertTrue($tmp instanceof HkdfWhirlpool);
        $this->assertTrue($tmp instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::PBKDF2_WHIRLPOOL);
        $this->assertTrue($tmp instanceof Pbkdf2Whirlpool);
        $this->assertTrue($tmp instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::BCRYPT);
        $this->assertTrue($tmp instanceof Bcrypt);
        $this->assertTrue($tmp instanceof AbstractHardwareResistantDerivation);
        $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($tmp instanceof AbstractHashAlgorithm);

        if (in_array(PASSWORD_ARGON2I, password_algos(), true)) {
            $tmp = HashAlgorithmFactory::createInstance(HashAlgorithmFactory::ARGON2);
            $this->assertTrue($tmp instanceof Argon2);
            $this->assertTrue($tmp instanceof AbstractHardwareResistantDerivation);
            $this->assertTrue($tmp instanceof AbstractPasswordBasedDerivationFunction);
            $this->assertTrue($tmp instanceof AbstractKeyStretchingFunction);
            $this->assertTrue($tmp instanceof AbstractHashAlgorithm);
        }

        $this->assertNull(HashAlgorithmFactory::createInstance(\stdClass::class));
    }
}
