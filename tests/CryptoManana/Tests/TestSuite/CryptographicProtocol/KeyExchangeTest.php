<?php

/**
 * Testing the key exchange cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\KeyExchange;
use CryptoManana\Hashing\HkdfShaTwo384;

/**
 * Class KeyExchangeTest - Testing the key exchange cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class KeyExchangeTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return KeyExchange Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $exchange = new KeyExchange(new HkdfShaTwo384());
        $exchange->setKeyExchangeSize(384);

        return $exchange;
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
        $this->assertNotEmpty($tmp->generateExchangeRequestInformation());

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
        $this->assertNotEmpty($tmp->generateExchangeRequestInformation());

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
     * Testing if the key exchange process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testKeyExchangeBetweenTwoParties()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setKeyExchangeSize(15360);
        $this->assertEquals(15360, $protocol->getKeyExchangeSize());
        $protocol->setKeyExchangeSize(4096);
        $this->assertEquals(4096, $protocol->getKeyExchangeSize());
        $protocol->setKeyExchangeSize(2048);
        $this->assertEquals(2048, $protocol->getKeyExchangeSize());
        $protocol->setKeyExchangeSize(384);
        $this->assertEquals(384, $protocol->getKeyExchangeSize());

        // Alice generates key exchange information
        $aliceInformation = $protocol->generateExchangeRequestInformation();

        $alicePrivateKey = $aliceInformation->private;
        $aliceInformation->private = '';

        // Sends to Bob the prime, generator and public key
        $bobInformation = $protocol->generateExchangeResponseInformation(
            $aliceInformation->prime,
            $aliceInformation->generator
        );

        $bobPrivateKey = $bobInformation->private;
        $bobInformation->private = '';

        // Bob computes the shared key
        $bobSharedKey = $protocol->computeSharedSecret($aliceInformation->public, $bobPrivateKey);

        // Sends to Alice the public key (optionally the prime and generator)
        $aliceSharedKey = $protocol->computeSharedSecret($bobInformation->public, $alicePrivateKey);

        // Both sides must now have the same key
        $this->assertEquals($aliceSharedKey, $bobSharedKey);
    }

    /**
     * Testing validation case for invalid key pair size for the key exchange generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidKeyPairSizeForTheKeyExchangeGeneration()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->setKeyExchangeSize(200);
        } else {
            $hasThrown = null;

            try {
                $protocol->setKeyExchangeSize(200);
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
     * Testing validation case for invalid type of key expansion service used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfKeyExpansionServicePassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new KeyExchange(null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new KeyExchange(null);
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
     * Testing validation case for invalid prime number for key exchange information generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidPrimeNumberForKeyExchangeGeneration()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->generateExchangeResponseInformation(['wrong'], bin2hex(decbin(2)));
        } else {
            $hasThrown = null;

            try {
                $protocol->generateExchangeResponseInformation(['wrong'], bin2hex(decbin(2)));
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
     * Testing validation case for invalid generator number for key exchange information generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidGeneratorNumberForKeyExchangeGeneration()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->generateExchangeResponseInformation(bin2hex(decbin(17)), ['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->generateExchangeResponseInformation(bin2hex(decbin(17)), ['wrong']);
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
     * Testing validation case for invalid remote public key used for computing the shared key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidRemotePublicKeyUsedForComputingOfSharedKey()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $exchangeInformation = $protocol->generateExchangeRequestInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->computeSharedSecret($exchangeInformation->private, $exchangeInformation->private);
        } else {
            $hasThrown = null;

            try {
                $protocol->computeSharedSecret($exchangeInformation->private, $exchangeInformation->private);
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
     * Testing validation case for invalid local private key used for computing the shared key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidLocalPrivateKeyUsedForComputingOfSharedKey()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $exchangeInformation = $protocol->generateExchangeRequestInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->computeSharedSecret($exchangeInformation->public, base64_encode('1234'));
        } else {
            $hasThrown = null;

            try {
                $protocol->computeSharedSecret($exchangeInformation->public, base64_encode('1234'));
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
     * Testing validation case for invalid format of remote public key used for computing the shared key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingWrongFormattedStringForPublicKey()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $exchangeInformation = $protocol->generateExchangeRequestInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->computeSharedSecret('яяяя', $exchangeInformation->private);
        } else {
            $hasThrown = null;

            try {
                $protocol->computeSharedSecret('яяяя', $exchangeInformation->private);
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
     * Testing validation case for invalid format of remote public key used for computing the shared key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingWrongFormattedStringForPrivateKey()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $exchangeInformation = $protocol->generateExchangeRequestInformation();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->computeSharedSecret($exchangeInformation->public, 'яяяя');
        } else {
            $hasThrown = null;

            try {
                $protocol->computeSharedSecret($exchangeInformation->public, 'яяяя');
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
