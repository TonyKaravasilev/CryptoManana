<?php

/**
 * Testing the asymmetric/public key authentication cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface;
use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator;
use CryptoManana\CryptographicProtocol\PublicKeyAuthentication;
use CryptoManana\AsymmetricEncryption\Rsa1024;
use CryptoManana\Utilities\TokenGenerator;
use CryptoManana\DataStructures\KeyPair;

/**
 * Class PublicKeyAuthenticationTest - Testing the asymmetric/public key authentication cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class PublicKeyAuthenticationTest extends AbstractUnitTest
{
    /**
     * The filename for the private key temporary file.
     */
    const PRIVATE_KEY_FILENAME_FOR_TESTS = 'rsa_1024_private.key';

    /**
     * The filename for the public key temporary file.
     */
    const PUBLIC_KEY_FILENAME_FOR_TESTS = 'rsa_1024_public.key';

    /**
     * Internal flag for checking of there is a key pair ready for testing.
     *
     * Note: `false` => auto-check on next call, `true` => already generated.
     *
     * @var null|bool Is the key pair generated.
     */
    protected static $isKeyPairGenerated = false;

    /**
     * Creates new instances for testing.
     *
     * @return PublicKeyAuthentication Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $rsa = new Rsa1024();

        if (self::$isKeyPairGenerated === false) {
            $generator = new TokenGenerator();

            $keyPair = $generator->getAsymmetricKeyPair($rsa::KEY_SIZE, $rsa::ALGORITHM_NAME);

            $this->writeToFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS, $keyPair->private);
            $this->writeToFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS, $keyPair->public);

            self::$isKeyPairGenerated = true;
        }

        $privateKey = $this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS);
        $publicKey = $this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS);

        $keyPair = new KeyPair($privateKey, $publicKey);

        $rsa->setKeyPair($keyPair);

        return new PublicKeyAuthentication($rsa);
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
        $this->assertNotEmpty($tmp->identifyEntity('', ''));

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

        $protocol->setAsymmetricCipher($protocol->getAsymmetricCipher());
        $protocol->setRandomGenerator($protocol->getRandomGenerator());

        $this->assertTrue($protocol->getRandomGenerator() instanceof AbstractGenerator);
        $this->assertTrue($protocol->getAsymmetricCipher() instanceof AbstractAsymmetricEncryptionAlgorithm);
        $this->assertTrue($protocol->getAsymmetricCipher() instanceof DataEncryptionInterface);

        $tokenObject = $protocol->generateAuthenticationToken();
        $userDecryptedToken = $protocol->extractAuthenticationToken($tokenObject->cipherData);

        $this->assertTrue($protocol->authenticateEntity($tokenObject->tokenData, $userDecryptedToken));
        $this->assertFalse($protocol->authenticateEntity($tokenObject->tokenData, strrev($userDecryptedToken)));
    }

    /**
     * Testing validation case for invalid type of asymmetric service used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfAsymmetricEncryptionServicePassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new PublicKeyAuthentication(null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new PublicKeyAuthentication(null);
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

    /**
     * Testing validation case for invalid cipher token passed for token extraction.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidCipherTokenPassedForTokenExtraction()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->extractAuthenticationToken(['none']);
        } else {
            $hasThrown = null;

            try {
                $protocol->extractAuthenticationToken(['none']);
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
     * Testing the resource cleanup operation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testKeyPairResourceCleanupOperation()
    {
        $this->assertTrue(self::$isKeyPairGenerated);

        $this->deleteTheFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS);
        $this->deleteTheFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS);

        self::$isKeyPairGenerated = null;
    }
}
