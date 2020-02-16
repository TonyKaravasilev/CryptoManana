<?php

/**
 * Testing the key-pair structure object for asymmetric keys storage.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\DataStructures\KeyPair;

/**
 * Class KeyPairTest - Testing the key-pair structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class KeyPairTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return KeyPair Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new KeyPair();
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
     * Testing the setting of both valid private and public keys.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingBothValidKeys()
    {
        $privateKey = '1234';
        $publicKey = '4321';

        $structure = new KeyPair($privateKey, $publicKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof KeyPair);

        $this->assertEquals($privateKey, $structure->private);
        $this->assertEquals($publicKey, $structure->public);

        $structure->private = strrev($privateKey);
        $structure->public = strrev($publicKey);

        $this->assertEquals(strrev($privateKey), $structure->private);
        $this->assertEquals(strrev($publicKey), $structure->public);
    }

    /**
     * Testing the setting of only a private key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePrivateKey()
    {
        $privateKey = '1234';
        $structure = new KeyPair($privateKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof KeyPair);

        $this->assertNotEmpty($structure->private);
        $this->assertEmpty($structure->public);

        $this->assertEquals($privateKey, $structure->private);
        $this->assertEquals('', $structure->public);

        $structure->private = strrev($structure->private);
        $structure->public = strrev($structure->public);

        $this->assertEquals(strrev($privateKey), $structure->private);
        $this->assertEquals('', $structure->public);
    }

    /**
     * Testing the setting of only a public key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePublicKey()
    {
        $publicKey = '1234';
        $structure = new KeyPair('', $publicKey);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof KeyPair);

        $this->assertEmpty($structure->private);
        $this->assertNotEmpty($structure->public);

        $this->assertEquals('', $structure->private);
        $this->assertEquals($publicKey, $structure->public);

        $structure->private = strrev($structure->private);
        $structure->public = strrev($structure->public);

        $this->assertEquals('', $structure->private);
        $this->assertEquals(strrev($publicKey), $structure->public);
    }

    /**
     * Testing the setting of invalid type for a property.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingInvalidPropertyType()
    {
        $structure = new KeyPair();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $structure->private = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->private = ['1234'];
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
        $structure = new KeyPair();

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
        $structure = new KeyPair();

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
        $structure = new KeyPair();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            unset($structure->private);
        } else {
            $hasThrown = null;

            try {
                unset($structure->private);
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
