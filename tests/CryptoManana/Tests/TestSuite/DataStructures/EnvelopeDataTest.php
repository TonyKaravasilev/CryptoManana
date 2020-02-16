<?php

/**
 * Testing the digital envelope structure object for secure data transfers.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\DataStructures\EnvelopeData;

/**
 * Class EnvelopeDataTest - Testing the digital envelope structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class EnvelopeDataTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return EnvelopeData Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new EnvelopeData();
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
        $key = '1234';
        $iv = '4321';
        $cipherData = '----';
        $tag = 'FFFF';

        $structure = new EnvelopeData($key, $iv, $cipherData, $tag);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EnvelopeData);

        $this->assertEquals($key, $structure->key);
        $this->assertEquals($iv, $structure->iv);
        $this->assertEquals($cipherData, $structure->cipherData);
        $this->assertEquals($tag, $structure->authenticationTag);

        $structure->key = strrev($key);
        $structure->iv = strrev($iv);
        $structure->cipherData = strrev($cipherData);
        $structure->authenticationTag = strrev($tag);

        $this->assertEquals(strrev($key), $structure->key);
        $this->assertEquals(strrev($iv), $structure->iv);
        $this->assertEquals(strrev($cipherData), $structure->cipherData);
        $this->assertEquals(strrev($tag), $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the encrypted key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheEncryptedKey()
    {
        $key = '1234';
        $structure = new EnvelopeData($key);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EnvelopeData);

        $this->assertNotEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->cipherData);
        $this->assertEmpty($structure->authenticationTag);

        $this->assertEquals($key, $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->cipherData);
        $this->assertEquals('', $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the encrypted initialization vector.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheEncryptedInitializationVector()
    {
        $iv = '1234';
        $structure = new EnvelopeData('', $iv);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EnvelopeData);

        $this->assertNotEmpty($structure->iv);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->cipherData);
        $this->assertEmpty($structure->authenticationTag);

        $this->assertEquals($iv, $structure->iv);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->cipherData);
        $this->assertEquals('', $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the cipher data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheCipherData()
    {
        $cipherData = '1234';
        $structure = new EnvelopeData('', '', $cipherData);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EnvelopeData);

        $this->assertNotEmpty($structure->cipherData);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->authenticationTag);

        $this->assertEquals($cipherData, $structure->cipherData);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the authentication tag.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheAuthenticationTag()
    {
        $tag = '1234';
        $structure = new EnvelopeData('', '', '', $tag);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EnvelopeData);

        $this->assertNotEmpty($structure->authenticationTag);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->cipherData);

        $this->assertEquals($tag, $structure->authenticationTag);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->cipherData);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = new EnvelopeData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->key = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->key = ['1234'];
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
        $structure = new EnvelopeData();

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
        $structure = new EnvelopeData();

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
        $structure = new EnvelopeData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            unset($structure->key);
        } else {
            $hasThrown = null;

            try {
                unset($structure->key);
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
