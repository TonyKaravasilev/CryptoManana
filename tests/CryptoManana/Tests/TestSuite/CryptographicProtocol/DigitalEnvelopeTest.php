<?php

/**
 * Testing the digital envelope cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\DigitalEnvelope;
use CryptoManana\AsymmetricEncryption\Rsa1024;
use CryptoManana\SymmetricEncryption\Aes128;
use CryptoManana\Utilities\TokenGenerator;
use CryptoManana\DataStructures\KeyPair;
use CryptoManana\Hashing\HmacShaTwo384;

/**
 * Class DigitalEnvelopeTest - Testing the digital envelope cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class DigitalEnvelopeTest extends AbstractUnitTest
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
     * @return DigitalEnvelope Testing instance.
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

        return new DigitalEnvelope($rsa, new Aes128());
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
        $this->assertNotEmpty($tmp->sealEnvelope(''));

        unset($tmp);
        $this->assertNotNull($protocol);

        $protocol->setKeyedDigestionFunction(new HmacShaTwo384());
        $tmp = clone $protocol;

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->sealEnvelope(''));

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
        $this->assertNotEmpty($tmp->sealEnvelope(''));

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
     * Testing if the digital sealing and opening process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDigitalEnvelopeSealingAndOpening()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setRandomGenerator($protocol->getRandomGenerator());
        $protocol->setSymmetricCipher($protocol->getSymmetricCipher());
        $protocol->setAsymmetricCipher($protocol->getAsymmetricCipher());

        $randomData = random_bytes(16);

        $envelopeObject = $protocol->sealEnvelope($randomData);
        $receivedData = $protocol->openEnvelope($envelopeObject);

        $this->assertEquals($randomData, $receivedData);
        $this->assertEmpty($envelopeObject->authenticationTag);

        $hasher = new HmacShaTwo384();

        $protocol->setKeyedDigestionFunction($hasher);
        $this->assertEquals($hasher, $protocol->getKeyedDigestionFunction());

        $envelopeObject = $protocol->sealEnvelope($randomData);
        $receivedData = $protocol->openEnvelope($envelopeObject);

        $this->assertEquals($randomData, $receivedData);
        $this->assertNotEmpty($envelopeObject->authenticationTag);
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

            $protocol = new DigitalEnvelope(null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new DigitalEnvelope(null);
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
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new DigitalEnvelope($protocol->getAsymmetricCipher(), null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new DigitalEnvelope($protocol->getAsymmetricCipher(), null);
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
     * Testing validation case for invalid type of input data for sealing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForEnvelopeSealing()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->sealEnvelope(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->sealEnvelope(['wrong']);
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
     * Testing validation case for invalid authentication tag on opening of envelope.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAuthenticationTagOnEnvelopeOpening()
    {
        $protocol = $this->getCryptographicProtocolForTesting();
        $protocol->setKeyedDigestionFunction(new HmacShaTwo384());

        $envelopeObject = $protocol->sealEnvelope('1234');
        $envelopeObject->authenticationTag = 'FFFF';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->openEnvelope($envelopeObject);
        } else {
            $hasThrown = null;

            try {
                $protocol->openEnvelope($envelopeObject);
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
