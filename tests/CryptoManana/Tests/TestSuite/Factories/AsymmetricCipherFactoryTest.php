<?php

/**
 * Testing the AsymmetricCipherFactory component used for easier asymmetric encryption/signature object instancing.
 */

namespace CryptoManana\Tests\TestSuite\Factories;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature;
use \CryptoManana\AsymmetricEncryption\Rsa1024;
use \CryptoManana\AsymmetricEncryption\Rsa2048;
use \CryptoManana\AsymmetricEncryption\Rsa3072;
use \CryptoManana\AsymmetricEncryption\Rsa4096;
use \CryptoManana\AsymmetricEncryption\Dsa1024;
use \CryptoManana\AsymmetricEncryption\Dsa2048;
use \CryptoManana\AsymmetricEncryption\Dsa3072;
use \CryptoManana\AsymmetricEncryption\Dsa4096;
use \CryptoManana\Factories\AsymmetricCipherFactory;

/**
 * Class AsymmetricCipherFactoryTest - Tests the asymmetric encryption/signature algorithm factory class.
 *
 * @package CryptoManana\Tests\TestSuite\Factories
 */
final class AsymmetricCipherFactoryTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return AsymmetricCipherFactory Testing instance.
     */
    private function getAsymmetricCipherFactoryForTesting()
    {
        return new AsymmetricCipherFactory();
    }

    /**
     * Testing the cloning of an instance.
     */
    public function testCloningCapabilities()
    {
        $factory = $this->getAsymmetricCipherFactoryForTesting();

        $tmp = clone $factory;

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::RSA_1024));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $factory = $this->getAsymmetricCipherFactoryForTesting();

        $tmp = serialize($factory);
        $tmp = unserialize($tmp);

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::RSA_1024));

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
        $factory = $this->getAsymmetricCipherFactoryForTesting();

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
        $factory = $this->getAsymmetricCipherFactoryForTesting();

        $this->assertTrue($factory instanceof AsymmetricCipherFactory);
        $this->assertTrue($factory instanceof AbstractFactory);

        $tmp = $factory->create(AsymmetricCipherFactory::RSA_1024);
        $this->assertTrue($tmp instanceof Rsa1024);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::RSA_2048);
        $this->assertTrue($tmp instanceof Rsa2048);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::RSA_3072);
        $this->assertTrue($tmp instanceof Rsa3072);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::RSA_4096);
        $this->assertTrue($tmp instanceof Rsa4096);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::DSA_1024);
        $this->assertTrue($tmp instanceof Dsa1024);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::DSA_2048);
        $this->assertTrue($tmp instanceof Dsa2048);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::DSA_3072);
        $this->assertTrue($tmp instanceof Dsa3072);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = $factory->create(AsymmetricCipherFactory::DSA_4096);
        $this->assertTrue($tmp instanceof Dsa4096);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $this->assertNull($factory->create(\stdClass::class));
    }

    /**
     * Testing the static instancing calls.
     */
    public function testStaticInstancingCalls()
    {
        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::RSA_1024);
        $this->assertTrue($tmp instanceof Rsa1024);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::RSA_2048);
        $this->assertTrue($tmp instanceof Rsa2048);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::RSA_3072);
        $this->assertTrue($tmp instanceof Rsa3072);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::RSA_4096);
        $this->assertTrue($tmp instanceof Rsa4096);
        $this->assertTrue($tmp instanceof AbstractRsaEncryption);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::DSA_1024);
        $this->assertTrue($tmp instanceof Dsa1024);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::DSA_2048);
        $this->assertTrue($tmp instanceof Dsa2048);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::DSA_3072);
        $this->assertTrue($tmp instanceof Dsa3072);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $tmp = AsymmetricCipherFactory::createInstance(AsymmetricCipherFactory::DSA_4096);
        $this->assertTrue($tmp instanceof Dsa4096);
        $this->assertTrue($tmp instanceof AbstractDsaSignature);
        $this->assertTrue($tmp instanceof AbstractAsymmetricEncryptionAlgorithm);

        $this->assertNull(AsymmetricCipherFactory::createInstance(\stdClass::class));
    }
}
