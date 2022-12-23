<?php

/**
 * Testing the encryption layer configuration object.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use CryptoManana\DataStructures\EncryptionLayer;
use CryptoManana\Tests\TestTypes\AbstractUnitTest;

/**
 * Class EncryptionLayerTest - Testing the encryption layer configuration object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class EncryptionLayerTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return EncryptionLayer Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new EncryptionLayer();
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
        $cipher = 'CryptoManana\SymmetricEncryption\Aes128';
        $key = '1234';
        $iv = '4321';
        $mode = 'CBC';
        $padding = 0;
        $format = 0;

        $structure = new EncryptionLayer($cipher, $key, $iv, $mode, $padding, $format);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertEquals($cipher, $structure->cipher);
        $this->assertEquals($key, $structure->key);
        $this->assertEquals($iv, $structure->iv);
        $this->assertEquals($mode, $structure->mode);
        $this->assertEquals($padding, $structure->padding);
        $this->assertEquals($format, $structure->format);

        $structure->cipher = strrev($cipher);
        $structure->key = strrev($key);
        $structure->iv = strrev($iv);
        $structure->mode = strrev($mode);
        $structure->padding = $padding + 1;
        $structure->format = $format + 1;

        $this->assertEquals(strrev($cipher), $structure->cipher);
        $this->assertEquals(strrev($key), $structure->key);
        $this->assertEquals(strrev($iv), $structure->iv);
        $this->assertEquals(strrev($mode), $structure->mode);
        $this->assertEquals($padding + 1, $structure->padding);
        $this->assertEquals($format + 1, $structure->format);
    }

    /**
     * Testing the setting of only the cipher name.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheCipherName()
    {
        $cipher = 'CryptoManana\SymmetricEncryption\Aes128';
        $structure = new EncryptionLayer($cipher);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->cipher);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->mode);
        $this->assertEquals(1, $structure->padding);
        $this->assertEquals(3, $structure->format);

        $this->assertEquals($cipher, $structure->cipher);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->mode);
    }

    /**
     * Testing the setting of only the encrypted key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheEncryptedKey()
    {
        $key = '1234';
        $structure = new EncryptionLayer('', $key);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->key);
        $this->assertEmpty($structure->cipher);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->mode);
        $this->assertEquals(1, $structure->padding);
        $this->assertEquals(3, $structure->format);

        $this->assertEquals($key, $structure->key);
        $this->assertEquals('', $structure->cipher);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->mode);
    }

    /**
     * Testing the setting of only the encrypted initialization vector.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheEncryptedInitializationVector()
    {
        $iv = '1234';
        $structure = new EncryptionLayer('', '', $iv);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->iv);
        $this->assertEmpty($structure->cipher);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->mode);
        $this->assertEquals(1, $structure->padding);
        $this->assertEquals(3, $structure->format);

        $this->assertEquals($iv, $structure->iv);
        $this->assertEquals('', $structure->cipher);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->mode);
    }

    /**
     * Testing the setting of only the block mode setting.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheBlockMode()
    {
        $mode = 'CBC';
        $structure = new EncryptionLayer('', '', '', $mode);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->mode);
        $this->assertEmpty($structure->cipher);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEquals(1, $structure->padding);
        $this->assertEquals(3, $structure->format);

        $this->assertEquals($mode, $structure->mode);
        $this->assertEquals('', $structure->cipher);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
    }

    /**
     * Testing the setting of only the padding standard setting.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePaddingStandard()
    {
        $padding = 2;
        $structure = new EncryptionLayer('', '', '', '', $padding);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->padding);
        $this->assertEmpty($structure->cipher);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->mode);
        $this->assertEquals(3, $structure->format);

        $this->assertEquals($padding, $structure->padding);
        $this->assertEquals('', $structure->cipher);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->mode);
    }

    /**
     * Testing the setting of only the output format setting.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheOutputFormat()
    {
        $format = 3;
        $structure = new EncryptionLayer('', '', '', '', 1, $format);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof EncryptionLayer);

        $this->assertNotEmpty($structure->format);
        $this->assertEmpty($structure->cipher);
        $this->assertEmpty($structure->key);
        $this->assertEmpty($structure->iv);
        $this->assertEmpty($structure->mode);
        $this->assertEquals(1, $structure->padding);

        $this->assertEquals($format, $structure->format);
        $this->assertEquals('', $structure->cipher);
        $this->assertEquals('', $structure->key);
        $this->assertEquals('', $structure->iv);
        $this->assertEquals('', $structure->mode);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = $this->getDataStructureForTesting();

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
        $structure = $this->getDataStructureForTesting();

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
        $structure = $this->getDataStructureForTesting();

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
        $structure = $this->getDataStructureForTesting();

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
