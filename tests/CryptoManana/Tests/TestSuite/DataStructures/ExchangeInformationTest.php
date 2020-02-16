<?php

/**
 * Testing the key exchange information structure object for secure key transfers.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\DataStructures\ExchangeInformation;

/**
 * Class ExchangeInformationTest - Testing the key exchange structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class ExchangeInformationTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return ExchangeInformation Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new ExchangeInformation();
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $structure = $this->getDataStructureForTesting();

        $tmp = clone $structure;

        $this->assertEquals($structure, $tmp);
        $this->assertNotEmpty($tmp->__toString());

        unset($tmp);
        $this->assertNotNull($structure);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $structure = $this->getDataStructureForTesting();

        $tmp = serialize($structure);
        $tmp = unserialize($tmp);

        $this->assertEquals($structure, $tmp);
        $this->assertNotEmpty($tmp->__toString());

        unset($tmp);
        $this->assertNotNull($structure);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDebugCapabilities()
    {
        $structure = $this->getDataStructureForTesting();

        $this->assertNotEmpty(var_export($structure, true));
    }

    /**
     * Testing the setting of all properties with valid values.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingAllPropertiesWithValidValues()
    {
        $prime = '1234';
        $generator = '4321';
        $privateKey = 'AAAA';
        $publicKey = 'FFFF';

        $structure = new ExchangeInformation($prime, $generator, $privateKey, $publicKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof ExchangeInformation);

        $this->assertEquals($prime, $structure->prime);
        $this->assertEquals($generator, $structure->generator);
        $this->assertEquals($privateKey, $structure->private);
        $this->assertEquals($publicKey, $structure->public);

        $structure->prime = strrev($prime);
        $structure->generator = strrev($generator);
        $structure->private = strrev($privateKey);
        $structure->public = strrev($publicKey);

        $this->assertEquals(strrev($prime), $structure->prime);
        $this->assertEquals(strrev($generator), $structure->generator);
        $this->assertEquals(strrev($privateKey), $structure->private);
        $this->assertEquals(strrev($publicKey), $structure->public);
    }

    /**
     * Testing the setting of only the prime number.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePrimeNumber()
    {
        $prime = '1234';
        $structure = new ExchangeInformation($prime);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof ExchangeInformation);

        $this->assertNotEmpty($structure->prime);
        $this->assertEmpty($structure->generator);
        $this->assertEmpty($structure->private);
        $this->assertEmpty($structure->public);

        $this->assertEquals($prime, $structure->prime);
        $this->assertEquals('', $structure->generator);
        $this->assertEquals('', $structure->private);
        $this->assertEquals('', $structure->public);
    }

    /**
     * Testing the setting of only the generator number.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheGeneratorNumber()
    {
        $generator = '1234';
        $structure = new ExchangeInformation('', $generator);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof ExchangeInformation);

        $this->assertNotEmpty($structure->generator);
        $this->assertEmpty($structure->prime);
        $this->assertEmpty($structure->private);
        $this->assertEmpty($structure->public);

        $this->assertEquals($generator, $structure->generator);
        $this->assertEquals('', $structure->prime);
        $this->assertEquals('', $structure->private);
        $this->assertEquals('', $structure->public);
    }

    /**
     * Testing the setting of only the private key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePrivateKey()
    {
        $privateKey = '1234';
        $structure = new ExchangeInformation('', '', $privateKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof ExchangeInformation);

        $this->assertNotEmpty($structure->private);
        $this->assertEmpty($structure->prime);
        $this->assertEmpty($structure->generator);
        $this->assertEmpty($structure->public);

        $this->assertEquals($privateKey, $structure->private);
        $this->assertEquals('', $structure->prime);
        $this->assertEquals('', $structure->generator);
        $this->assertEquals('', $structure->public);
    }

    /**
     * Testing the setting of only the public key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePublicKey()
    {
        $publicKey = '1234';
        $structure = new ExchangeInformation('', '', '', $publicKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof ExchangeInformation);

        $this->assertNotEmpty($structure->public);
        $this->assertEmpty($structure->prime);
        $this->assertEmpty($structure->generator);
        $this->assertEmpty($structure->private);

        $this->assertEquals($publicKey, $structure->public);
        $this->assertEquals('', $structure->prime);
        $this->assertEquals('', $structure->generator);
        $this->assertEquals('', $structure->private);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = new ExchangeInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->prime = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->prime = ['1234'];
            } catch (\InvalidArgumentException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing the setting of a non-existent property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingNonExistentProperty()
    {
        $structure = new ExchangeInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->test = 1234;
        } else {
            $hasThrown = null;

            try {
                $structure->test = 1234;
            } catch (\InvalidArgumentException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing the getting of a non-existent property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAccessingNonExistentProperty()
    {
        $structure = new ExchangeInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\OutOfBoundsException::class);

            $tmp = $structure->test;
        } else {
            $hasThrown = null;

            try {
                $tmp = $structure->test;
            } catch (\OutOfBoundsException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing the unsetting of an existent property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForUnsettingAnExistentProperty()
    {
        $structure = new ExchangeInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            unset($structure->prime);
        } else {
            $hasThrown = null;

            try {
                unset($structure->prime);
            } catch (\LogicException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
