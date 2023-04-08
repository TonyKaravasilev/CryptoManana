<?php

/**
 * Testing the authenticated symmetric encryption cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\AuthenticatedEncryption;
use CryptoManana\SymmetricEncryption\Aes128;
use CryptoManana\Hashing\HmacShaTwo256;

/**
 * Class AuthenticatedEncryptionTest - Testing the authenticated symmetric encryption cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class AuthenticatedEncryptionTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return AuthenticatedEncryption Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $encryptor = $this->getMockBuilder(Aes128::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $encryptor->expects($this->atLeast(0))
            ->method('getSecretKey')
            ->willReturn('secret');

        $encryptor->expects($this->atLeast(0))
            ->method('getInitializationVector')
            ->willReturn('iv');

        $encryptor->expects($this->atLeast(0))
            ->method('setSecretKey')
            ->willReturnSelf();

        $encryptor->expects($this->atLeast(0))
            ->method('setInitializationVector')
            ->willReturnSelf();

        $hasher = $this->getMockBuilder(HmacShaTwo256::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $hasher->expects($this->atLeast(0))
            ->method('setKey')
            ->willReturnSelf();

        $hasher->expects($this->atLeast(0))
            ->method('setSalt')
            ->willReturnSelf();

        $hasher->expects($this->atLeast(0))
            ->method('getKey')
            ->willReturn('key');

        $hasher->expects($this->atLeast(0))
            ->method('getSalt')
            ->willReturn('salt');

        return new AuthenticatedEncryption($encryptor, $hasher);
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->getSymmetricCipher()
            ->method('encryptData')
            ->willReturn('AAAA');

        $protocol->getKeyedDigestionFunction()
            ->method('hashData')
            ->willReturn('FFFF');

        $tmp = clone $protocol;

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->authenticatedEncryptData(''));

        unset($tmp);
        $this->assertNotNull($protocol);
    }

    /**
     * Testing if the basic data encryption and decryption process works for Encrypt and MAC.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptAndMac()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $data = 'test';

        $protocol->getSymmetricCipher()
            ->method('encryptData')
            ->willReturn('AAAA');

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data);

        $protocol->getKeyedDigestionFunction()
            ->method('hashData')
            ->willReturn('FFFF');

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC);
        $this->assertEquals($protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC, $protocol->getAuthenticationMode());

        $encryptedData = $protocol->authenticatedEncryptData($data);
        $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

        $this->assertEquals($data, $decryptedData);

        // Test MAC failure
        $encryptedData->authenticationTag = 'AAAAAAAAAAAAAA';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
        } else {
            $hasThrown = null;

            try {
                $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
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
     * Testing if the basic data encryption and decryption process works for Encrypt Then MAC.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptThenMac()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $data = 'test';

        $protocol->getSymmetricCipher()
            ->method('encryptData')
            ->willReturn('AAAA');

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data);

        $protocol->getKeyedDigestionFunction()
            ->method('hashData')
            ->willReturn('FFFF');

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC);
        $this->assertEquals($protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC, $protocol->getAuthenticationMode());

        $encryptedData = $protocol->authenticatedEncryptData($data);
        $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

        $this->assertEquals($data, $decryptedData);

        // Test MAC failure
        $encryptedData->authenticationTag = 'AAAAAAAAAAAAAA';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
        } else {
            $hasThrown = null;

            try {
                $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
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
     * Testing if the basic data encryption and decryption process works for MAC then Encrypt.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testMacThenEncrypt()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $data = 'test';

        $protocol->getSymmetricCipher()
            ->method('encryptData')
            ->willReturn('AAAA');

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data . '%7C__%7CFFFF');

        $protocol->getKeyedDigestionFunction()
            ->method('hashData')
            ->willReturn('FFFF');

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT);
        $this->assertEquals($protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT, $protocol->getAuthenticationMode());

        $encryptedData = $protocol->authenticatedEncryptData($data);
        $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

        $this->assertEquals($data, $decryptedData);

        // Test MAC failure
        $encryptedData->authenticationTag = 'AAAAAAAAAAAAAA';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
        } else {
            $hasThrown = null;

            try {
                $decryptedData = $protocol->authenticatedDecryptData($encryptedData);
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
     * Testing validation case for invalid type of symmetric service used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfSymmetricEncryptionServicePassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new AuthenticatedEncryption(null, null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new AuthenticatedEncryption(null, null);
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
     * Testing validation case for invalid type of input data for encryption passed.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForEncryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticatedEncryptData(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedEncryptData(['wrong']);
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
     * Testing validation case for invalid type of authentication mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfAuthenticationMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->setAuthenticationMode(-1000);
        } else {
            $hasThrown = null;

            try {
                $protocol->setAuthenticationMode(-1000);
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
     * Testing validation case for invalid internal authentication mode used for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInternalAuthenticationModeUsedForEncryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();


        $reflectionMbString = new \ReflectionProperty(
            AuthenticatedEncryption::class,
            'authenticationMode'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($protocol, -1000);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\OutOfBoundsException::class);

            $protocol->authenticatedEncryptData('1234');
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedEncryptData('1234');
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
     * Testing validation case for invalid internal authentication mode used for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInternalAuthenticationModeUsedForDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->getSymmetricCipher()
            ->method('encryptData')
            ->willReturn('AAAA');

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn('test');

        $protocol->getKeyedDigestionFunction()
            ->method('hashData')
            ->willReturn('FFFF');

        $authenticationCipherData = $protocol->authenticatedEncryptData('');

        $reflectionMbString = new \ReflectionProperty(
            AuthenticatedEncryption::class,
            'authenticationMode'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($protocol, -1000);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\OutOfBoundsException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
            } catch (\OutOfBoundsException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
