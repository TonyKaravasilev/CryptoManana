<?php

/**
 * Testing the SymmetricCipherFactory component used for easier symmetric encryption algorithm object instancing.
 */

namespace CryptoManana\Tests\TestSuite\Factories;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractSymmetricEncryptionAlgorithm;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm;
use \CryptoManana\SymmetricEncryption\Aes128;
use \CryptoManana\SymmetricEncryption\Aes192;
use \CryptoManana\SymmetricEncryption\Aes256;
use \CryptoManana\SymmetricEncryption\Camellia128;
use \CryptoManana\SymmetricEncryption\Camellia192;
use \CryptoManana\SymmetricEncryption\Camellia256;
use \CryptoManana\Factories\SymmetricCipherFactory;

/**
 * Class SymmetricCipherFactoryTest - Tests the symmetric encryption algorithm factory class.
 *
 * @package CryptoManana\Tests\TestSuite\Factories
 */
final class SymmetricCipherFactoryTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return SymmetricCipherFactory Testing instance.
     */
    private function getSymmetricCipherFactoryForTesting()
    {
        return new SymmetricCipherFactory();
    }

    /**
     * Testing the cloning of an instance.
     */
    public function testCloningCapabilities()
    {
        $factory = $this->getSymmetricCipherFactoryForTesting();

        $tmp = clone $factory;

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::AES_128));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $factory = $this->getSymmetricCipherFactoryForTesting();

        $tmp = serialize($factory);
        $tmp = unserialize($tmp);

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::AES_128));

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
        $factory = $this->getSymmetricCipherFactoryForTesting();

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
        $factory = $this->getSymmetricCipherFactoryForTesting();

        $this->assertTrue($factory instanceof SymmetricCipherFactory);
        $this->assertTrue($factory instanceof AbstractFactory);

        $tmp = $factory->create(SymmetricCipherFactory::AES_128);
        $this->assertTrue($tmp instanceof Aes128);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = $factory->create(SymmetricCipherFactory::AES_192);
        $this->assertTrue($tmp instanceof Aes192);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = $factory->create(SymmetricCipherFactory::AES_256);
        $this->assertTrue($tmp instanceof Aes256);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = $factory->create(SymmetricCipherFactory::CAMELLIA_128);
        $this->assertTrue($tmp instanceof Camellia128);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = $factory->create(SymmetricCipherFactory::CAMELLIA_192);
        $this->assertTrue($tmp instanceof Camellia192);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = $factory->create(SymmetricCipherFactory::CAMELLIA_256);
        $this->assertTrue($tmp instanceof Camellia256);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $this->assertNull($factory->create(\stdClass::class));
    }

    /**
     * Testing the static instancing calls.
     */
    public function testStaticInstancingCalls()
    {
        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::AES_128);
        $this->assertTrue($tmp instanceof Aes128);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::AES_192);
        $this->assertTrue($tmp instanceof Aes192);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::AES_256);
        $this->assertTrue($tmp instanceof Aes256);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::CAMELLIA_128);
        $this->assertTrue($tmp instanceof Camellia128);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::CAMELLIA_192);
        $this->assertTrue($tmp instanceof Camellia192);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $tmp = SymmetricCipherFactory::createInstance(SymmetricCipherFactory::CAMELLIA_256);
        $this->assertTrue($tmp instanceof Camellia256);
        $this->assertTrue($tmp instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($tmp instanceof AbstractSymmetricEncryptionAlgorithm);

        $this->assertNull(SymmetricCipherFactory::createInstance(\stdClass::class));
    }
}
