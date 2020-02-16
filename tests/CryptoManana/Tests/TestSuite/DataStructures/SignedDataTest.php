<?php

/**
 * Testing the signed data structure object for digital signatures.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\DataStructures\SignedData;

/**
 * Class SignedDataTest - Testing the signed data structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class SignedDataTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return SignedData Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new SignedData();
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
     * Testing the setting of both valid data and signature.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingBothValidDataAndSignature()
    {
        $messageData = '1234';
        $signatureData = '4321';

        $structure = new SignedData($messageData, $signatureData);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof SignedData);

        $this->assertEquals($messageData, $structure->data);
        $this->assertEquals($signatureData, $structure->signature);

        $structure->data = strrev($messageData);
        $structure->signature = strrev($signatureData);

        $this->assertEquals(strrev($messageData), $structure->data);
        $this->assertEquals(strrev($signatureData), $structure->signature);
    }

    /**
     * Testing the setting of only the message data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheMessageData()
    {
        $messageData = '1234';
        $structure = new SignedData($messageData);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof SignedData);

        $this->assertNotEmpty($structure->data);
        $this->assertEmpty($structure->signature);

        $this->assertEquals($messageData, $structure->data);
        $this->assertEquals('', $structure->signature);

        $structure->data = strrev($structure->data);
        $structure->signature = strrev($structure->signature);

        $this->assertEquals(strrev($messageData), $structure->data);
        $this->assertEquals('', $structure->signature);
    }

    /**
     * Testing the setting of only the digital signature data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheSignatureData()
    {
        $signatureData = '4321';
        $structure = new SignedData('', $signatureData);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof SignedData);

        $this->assertEmpty($structure->data);
        $this->assertNotEmpty($structure->signature);

        $this->assertEquals('', $structure->data);
        $this->assertEquals($signatureData, $structure->signature);

        $structure->data = strrev($structure->data);
        $structure->signature = strrev($structure->signature);

        $this->assertEquals('', $structure->data);
        $this->assertEquals(strrev($signatureData), $structure->signature);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = new SignedData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->data = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->data = ['1234'];
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
        $structure = new SignedData();

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
        $structure = new SignedData();

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
        $structure = new SignedData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            unset($structure->data);
        } else {
            $hasThrown = null;

            try {
                unset($structure->data);
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
