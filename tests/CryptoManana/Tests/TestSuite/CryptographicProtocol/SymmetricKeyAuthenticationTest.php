<?php

/**
 * Testing the symmetric key authentication cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm;
use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface;
use CryptoManana\CryptographicProtocol\SymmetricKeyAuthentication;
use CryptoManana\SymmetricEncryption\Aes128;
use CryptoManana\Randomness\CryptoRandom;

/**
 * Class SymmetricKeyAuthenticationTest - Testing the symmetric key authentication cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class SymmetricKeyAuthenticationTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return SymmetricKeyAuthentication Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $aes = $this->getMockBuilder(Aes128::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $aes->expects($this->atLeast(0))
            ->method('getSecretKey')
            ->willReturn('secret');

        $aes->expects($this->atLeast(0))
            ->method('getInitializationVector')
            ->willReturn('iv');

        $aes->expects($this->atLeast(0))
            ->method('setSecretKey')
            ->willReturnSelf();

        $aes->expects($this->atLeast(0))
            ->method('setInitializationVector')
            ->willReturnSelf();

        $aes->expects($this->atLeast(0))
            ->method('encryptData')
            ->willReturn('FFFF');

        $randomness = $this->getMockBuilder(CryptoRandom::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $randomness->expects($this->atLeast(0))
            ->method('getBytes')
            ->willReturn("\0");

        $randomness->expects($this->atLeast(0))
            ->method('getAlphaNumeric')
            ->willReturn('A1');

        $protocol = new SymmetricKeyAuthentication($aes);

        $protocol->setRandomGenerator($randomness);

        return $protocol;
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
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSymmetricCipher($protocol->getSymmetricCipher());
        $protocol->setRandomGenerator($protocol->getRandomGenerator());

        $this->assertTrue($protocol->getRandomGenerator() instanceof AbstractGenerator);
        $this->assertTrue($protocol->getSymmetricCipher() instanceof AbstractBlockCipherAlgorithm);
        $this->assertTrue($protocol->getSymmetricCipher() instanceof DataEncryptionInterface);

        $tokenObject = $protocol->generateAuthenticationToken();

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($tokenObject->tokenData);

        $userDecryptedToken = $protocol->extractAuthenticationToken($tokenObject->cipherData);

        $this->assertTrue($protocol->authenticateEntity($tokenObject->tokenData, $userDecryptedToken));
        $this->assertFalse($protocol->authenticateEntity($tokenObject->tokenData, strrev($userDecryptedToken)));
    }

    /**
     * Testing validation case for invalid type of symmetric service used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfSymmetricEncryptionServicePassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new SymmetricKeyAuthentication(null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new SymmetricKeyAuthentication(null);
            } catch (\RuntimeException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
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

    /**
     * Testing validation case for invalid output length passed for token generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidOutputLengthPassedForTokenGeneration()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LengthException::class);

            $protocol->generateAuthenticationToken(-1000);
        } else {
            $hasThrown = null;

            try {
                $protocol->generateAuthenticationToken(-1000);
            } catch (\LengthException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
