<?php

/**
 * Testing the token structure object for authentication purposes.
 */

namespace CryptoManana\Tests\TestSuite\DataStructures;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\DataStructures\AuthenticationToken;

/**
 * Class AuthenticationTokenTest - Testing the authentication token structure object.
 *
 * @package CryptoManana\Tests\TestSuite\DataStructures
 */
final class AuthenticationTokenTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return AuthenticationToken Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataStructureForTesting()
    {
        return new AuthenticationToken();
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
     * Testing the setting of both valid raw and cipher token representations.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingBothValidTokenRepresentations()
    {
        $token = '1234';
        $encrypted = 'MTIzNA==';

        $structure = new AuthenticationToken($token, $encrypted);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticationToken);

        $this->assertEquals($token, $structure->tokenData);
        $this->assertEquals($encrypted, $structure->cipherData);

        $structure->tokenData = strrev($token);
        $structure->cipherData = strrev($encrypted);

        $this->assertEquals(strrev($token), $structure->tokenData);
        $this->assertEquals(strrev($encrypted), $structure->cipherData);
    }

    /**
     * Testing the setting of only a plain token.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyThePlainToken()
    {
        $token = '1234';
        $structure = new AuthenticationToken($token);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticationToken);

        $this->assertNotEmpty($structure->tokenData);
        $this->assertEmpty($structure->cipherData);

        $this->assertEquals($token, $structure->tokenData);
        $this->assertEquals('', $structure->cipherData);

        $structure->tokenData = strrev($structure->tokenData);
        $structure->cipherData = strrev($structure->cipherData);

        $this->assertEquals(strrev($token), $structure->tokenData);
        $this->assertEquals('', $structure->cipherData);
    }

    /**
     * Testing the setting of only an encrypted token.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSettingOnlyTheEncryptedToken()
    {
        $encrypted = '1234';
        $structure = new AuthenticationToken('', $encrypted);

        $this->assertNotEmpty($structure);
        $this->assertTrue(is_object($structure));
        $this->assertTrue($structure instanceof AuthenticationToken);

        $this->assertEmpty($structure->tokenData);
        $this->assertNotEmpty($structure->cipherData);

        $this->assertEquals('', $structure->tokenData);
        $this->assertEquals($encrypted, $structure->cipherData);

        $structure->tokenData = strrev($structure->tokenData);
        $structure->cipherData = strrev($structure->cipherData);

        $this->assertEquals('', $structure->tokenData);
        $this->assertEquals(strrev($encrypted), $structure->cipherData);
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

            $structure->tokenData = ['1234'];
        } else {
            $hasThrown = null;

            try {
                $structure->tokenData = ['1234'];
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

            unset($structure->tokenData);
        } else {
            $hasThrown = null;

            try {
                unset($structure->tokenData);
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
