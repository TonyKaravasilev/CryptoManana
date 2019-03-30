<?php

/**
 * Testing the RandomnessFactory component used for easier randomness sources instancing.
 */

namespace CryptoManana\Tests\TestSuite\Factories;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory;
use \CryptoManana\Core\Abstractions\Randomness\AbstractRandomness;
use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator;
use \CryptoManana\Randomness\QuasiRandom;
use \CryptoManana\Randomness\PseudoRandom;
use \CryptoManana\Randomness\CryptoRandom;
use \CryptoManana\Factories\RandomnessFactory;

/**
 * Class RandomnessFactoryTest - Tests the randomness factory class.
 *
 * @package CryptoManana\Tests\TestSuite\Factories
 */
final class RandomnessFactoryTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return RandomnessFactory Testing instance.
     */
    private function getRandomnessFactoryForTesting()
    {
        return new RandomnessFactory();
    }

    /**
     * Testing the cloning of an instance.
     */
    public function testCloningCapabilities()
    {
        $factory = $this->getRandomnessFactoryForTesting();

        $tmp = clone $factory;

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::PSEUDO_SOURCE));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $factory = $this->getRandomnessFactoryForTesting();

        $tmp = serialize($factory);
        $tmp = unserialize($tmp);

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::PSEUDO_SOURCE));

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
        $factory = $this->getRandomnessFactoryForTesting();

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
        $factory = $this->getRandomnessFactoryForTesting();

        $this->assertTrue($factory instanceof RandomnessFactory);
        $this->assertTrue($factory instanceof AbstractFactory);

        $tmp = $factory->create(RandomnessFactory::QUASI_SOURCE);
        $this->assertTrue($tmp instanceof QuasiRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $tmp = $factory->create(RandomnessFactory::PSEUDO_SOURCE);
        $this->assertTrue($tmp instanceof PseudoRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $tmp = $factory->create(RandomnessFactory::CRYPTO_SOURCE);
        $this->assertTrue($tmp instanceof CryptoRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $this->assertNull($factory->create(\stdClass::class));
    }

    /**
     * Testing the static instancing calls.
     */
    public function testStaticInstancingCalls()
    {
        $tmp = RandomnessFactory::createInstance(RandomnessFactory::QUASI_SOURCE);
        $this->assertTrue($tmp instanceof QuasiRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $tmp = RandomnessFactory::createInstance(RandomnessFactory::PSEUDO_SOURCE);
        $this->assertTrue($tmp instanceof PseudoRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $tmp = RandomnessFactory::createInstance(RandomnessFactory::CRYPTO_SOURCE);
        $this->assertTrue($tmp instanceof CryptoRandom);
        $this->assertTrue($tmp instanceof AbstractGenerator);
        $this->assertTrue($tmp instanceof AbstractRandomness);

        $this->assertNull(RandomnessFactory::createInstance(\stdClass::class));
    }
}
