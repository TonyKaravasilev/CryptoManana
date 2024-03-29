<?php

/**
 * Testing the password-based authentication cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\PasswordBasedAuthentication;
use CryptoManana\Hashing\Bcrypt;

/**
 * Class PasswordBasedAuthenticationTest - Testing the password-based authentication cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class PasswordBasedAuthenticationTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return PasswordBasedAuthentication Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        return new PasswordBasedAuthentication();
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $tmp = clone $protocol;

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->identifyEntity('', ''));

        unset($tmp);
        $this->assertNotNull($protocol);

        $hasher = $this->getMockBuilder(Bcrypt::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $protocol->setVerificationAlgorithm($hasher);

        $this->assertEquals($hasher, $protocol->getVerificationAlgorithm());

        $tmp = clone $protocol;

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->identifyEntity('', ''));

        unset($tmp);
        $this->assertNotNull($protocol);
    }

    /**
     * Testing the object identification capabilities.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testClientEntityIdentificationAndVerificationCapabilities()
    {
        $entityId = '123жЯYd!`5';

        $protocol = $this->getCryptographicProtocolForTesting();

        $this->assertTrue($protocol->identifyEntity($entityId, $entityId));
        $this->assertFalse($protocol->identifyEntity($entityId, strrev($entityId)));
    }

    /**
     * Testing the object authentication capabilities.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testClientEntityAuthenticationCapabilities()
    {
        $entityPassword = 'рЯxD4!.x';

        $protocol = $this->getCryptographicProtocolForTesting();

        $this->assertTrue($protocol->authenticateEntity($entityPassword, $entityPassword));
        $this->assertFalse($protocol->authenticateEntity($entityPassword, strrev($entityPassword)));

        $hasher = $this->getMockBuilder(Bcrypt::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $hasher->expects($this->atLeast(0))
            ->method('verifyHash')
            ->willReturn(true);

        $protocol->setVerificationAlgorithm($hasher);
        $this->assertTrue($protocol->authenticateEntity($entityPassword, $entityPassword));
    }

    /**
     * Testing validation case for invalid type of user string for identification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfUserStringPassedForIdentification()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->identifyEntity('', ['none']);
        } else {
            $hasThrown = null;

            try {
                $protocol->identifyEntity('', ['none']);
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
     * Testing validation case for invalid type of correct string for identification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfCorrectStringPassedForIdentification()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->identifyEntity(['none'], '');
        } else {
            $hasThrown = null;

            try {
                $protocol->identifyEntity(['none'], '');
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
     * Testing validation case for invalid type of user string for authentication.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfUserStringPassedForAuthentication()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticateEntity('', ['none']);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticateEntity('', ['none']);
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
     * Testing validation case for invalid type of correct string for authentication.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfCorrectStringPassedForAuthentication()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticateEntity(['none'], '');
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticateEntity(['none'], '');
            } catch (\InvalidArgumentException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
