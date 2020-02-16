<?php

/**
 * Testing the authenticated cipher structure object for authenticated encryption output data storage.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\DataStructures\AuthenticatedCipherData;

/**
 * Class AuthenticatedCipherDataTest - Testing the authenticated cipher structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class AuthenticatedCipherDataTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return AuthenticatedCipherData Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new AuthenticatedCipherData();
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
     * Testing the setting of both valid cipher data and authentication tag.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingBothValidCipherDataAndTag()
    {
        $cipherData = '1234';
        $macTag = '4321';

        $structure = new AuthenticatedCipherData($cipherData, $macTag);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticatedCipherData);

        $this->assertEquals($cipherData, $structure->cipherData);
        $this->assertEquals($macTag, $structure->authenticationTag);

        $structure->cipherData = strrev($cipherData);
        $structure->authenticationTag = strrev($macTag);

        $this->assertEquals(strrev($cipherData), $structure->cipherData);
        $this->assertEquals(strrev($macTag), $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the cipher data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheCipherData()
    {
        $cipherData = '1234';
        $structure = new AuthenticatedCipherData($cipherData);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticatedCipherData);

        $this->assertNotEmpty($structure->cipherData);
        $this->assertEmpty($structure->authenticationTag);

        $this->assertEquals($cipherData, $structure->cipherData);
        $this->assertEquals('', $structure->authenticationTag);

        $structure->cipherData = strrev($structure->cipherData);
        $structure->authenticationTag = strrev($structure->authenticationTag);

        $this->assertEquals(strrev($cipherData), $structure->cipherData);
        $this->assertEquals('', $structure->authenticationTag);
    }

    /**
     * Testing the setting of only the authentication tag
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheAuthenticationTag()
    {
        $macTag = '4321';
        $structure = new AuthenticatedCipherData('', $macTag);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticatedCipherData);

        $this->assertEmpty($structure->cipherData);
        $this->assertNotEmpty($structure->authenticationTag);

        $this->assertEquals('', $structure->cipherData);
        $this->assertEquals($macTag, $structure->authenticationTag);

        $structure->cipherData = strrev($structure->cipherData);
        $structure->authenticationTag = strrev($structure->authenticationTag);

        $this->assertEquals('', $structure->cipherData);
        $this->assertEquals(strrev($macTag), $structure->authenticationTag);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = new AuthenticatedCipherData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->cipherData = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->cipherData = ['1234'];
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
        $structure = new AuthenticatedCipherData();

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
        $structure = new AuthenticatedCipherData();

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
     * Testing the unsetting of a non-existent property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForUnsettingNonExistentProperty()
    {
        $structure = new AuthenticatedCipherData();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            unset($structure->cipherData);
        } else {
            $hasThrown = null;

            try {
                unset($structure->cipherData);
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
