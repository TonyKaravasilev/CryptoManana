<?php

/**
 * Testing the authenticated symmetric encryption cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\CryptographicProtocol\AuthenticatedEncryption;
use \CryptoManana\DataStructures\AuthenticatedCipherData;
use \CryptoManana\SymmetricEncryption\Aes128;

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
        return new AuthenticatedEncryption(new Aes128());
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
        $this->assertNotEmpty($tmp->authenticatedEncryptData(''));

        unset($tmp);
        $this->assertNotNull($protocol);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $tmp = serialize($protocol);
        $tmp = unserialize($tmp);

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->authenticatedEncryptData(''));

        unset($tmp);
        $this->assertNotNull($protocol);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDebugCapabilities()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $this->assertNotEmpty(var_export($protocol, true));
    }

    /**
     * Testing if the basic data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataEncryptionAndDataDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSymmetricCipher($protocol->getSymmetricCipher());
        $protocol->setKeyedDigestionFunction($protocol->getKeyedDigestionFunction());

        $authenticationMode = [
            $protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC,
            $protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT,
            $protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC,
        ];

        $randomData = random_bytes(16);

        foreach ($authenticationMode as $mode) {
            $protocol->setAuthenticationMode($mode);
            $this->assertEquals($mode, $protocol->getAuthenticationMode());

            $encryptedData = $protocol->authenticatedEncryptData($randomData);
            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

            $this->assertEquals($randomData, $decryptedData);
        }

        $protocol->setKeyedDigestionFunction($protocol->getKeyedDigestionFunction()->setSalt('1234'));

        $protocol = new AuthenticatedEncryption(
            $protocol->getSymmetricCipher(),
            $protocol->getKeyedDigestionFunction()
        );

        foreach ($authenticationMode as $mode) {
            $protocol->setAuthenticationMode($mode);
            $this->assertEquals($mode, $protocol->getAuthenticationMode());

            $encryptedData = $protocol->authenticatedEncryptData($randomData);
            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

            $this->assertEquals($randomData, $decryptedData);
        }
    }

    /**
     * Testing if the unicode data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataEncryptionAndDataDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSymmetricCipher($protocol->getSymmetricCipher());
        $protocol->setKeyedDigestionFunction($protocol->getKeyedDigestionFunction());

        $authenticationMode = [
            $protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC,
            $protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT,
            $protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC,
        ];

        $unicodeData = "йЗъ 7-М%\v@UОrЯBZ";

        foreach ($authenticationMode as $mode) {
            $protocol->setAuthenticationMode($mode);
            $this->assertEquals($mode, $protocol->getAuthenticationMode());

            $encryptedData = $protocol->authenticatedEncryptData($unicodeData);
            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

            $this->assertEquals($unicodeData, $decryptedData);
        }

        $protocol->setKeyedDigestionFunction($protocol->getKeyedDigestionFunction()->setSalt('1234'));

        $protocol = new AuthenticatedEncryption(
            $protocol->getSymmetricCipher(),
            $protocol->getKeyedDigestionFunction()
        );

        foreach ($authenticationMode as $mode) {
            $protocol->setAuthenticationMode($mode);
            $this->assertEquals($mode, $protocol->getAuthenticationMode());

            $encryptedData = $protocol->authenticatedEncryptData($unicodeData);
            $decryptedData = $protocol->authenticatedDecryptData($encryptedData);

            $this->assertEquals($unicodeData, $decryptedData);
        }
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptingTheSameDataTwice()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $randomData = random_bytes(16);

        $this->assertEquals(
            $protocol->authenticatedEncryptData($randomData),
            $protocol->authenticatedEncryptData($randomData)
        );
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDecryptingTheSameDataTwice()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $randomData = random_bytes(16);
        $encryptedData = $protocol->authenticatedEncryptData($randomData);

        $this->assertEquals(
            $protocol->authenticatedDecryptData($encryptedData),
            $protocol->authenticatedDecryptData($encryptedData)
        );
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
     * Testing validation case for invalid type of input data for decryption passed in E&M mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForDecryptionInEncryptAndMacMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC);
        $authenticationCipherData = new AuthenticatedCipherData('1234', '1234');

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
     * Testing validation case for invalid type of input data for decryption passed in MtE mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForDecryptionInMacThenEncryptMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT);
        $authenticationCipherData = new AuthenticatedCipherData('1234', '1234');

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
     * Testing validation case for invalid type of input data for decryption passed in EtM mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForDecryptionInEncryptThenMacMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC);
        $authenticationCipherData = new AuthenticatedCipherData('1234', '1234');

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
     * Testing validation case for invalid authentication tag for decryption in E&M mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAuthenticationTagForDecryptionInEncryptAndMacMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_AND_MAC);
        $authenticationCipherData = $protocol->authenticatedEncryptData('1234');
        $authenticationCipherData->authenticationTag = 'invalid';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
     * Testing validation case for invalid authentication tag for decryption passed in MtE mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAuthenticationTagForDecryptionInMacThenEncryptMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT);
        $authenticationCipherData = $protocol->authenticatedEncryptData('1234');
        $authenticationCipherData->authenticationTag = 'invalid';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
     * Testing validation case for invalid authentication tag for decryption passed in EtM mode.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAuthenticationTagForDecryptionInEncryptThenMacMode()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setAuthenticationMode($protocol::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC);
        $authenticationCipherData = $protocol->authenticatedEncryptData('1234');
        $authenticationCipherData->authenticationTag = 'invalid';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->authenticatedDecryptData($authenticationCipherData);
        } else {
            $hasThrown = null;

            try {
                $protocol->authenticatedDecryptData($authenticationCipherData);
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
