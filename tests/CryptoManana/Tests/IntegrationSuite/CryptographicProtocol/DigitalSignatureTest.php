<?php

/**
 * Testing the digital signature standard cryptographic protocol object.
 */

namespace CryptoManana\Tests\IntegrationSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractIntegrationTest;
use CryptoManana\CryptographicProtocol\DigitalSignature;
use CryptoManana\AsymmetricEncryption\Dsa1024;
use CryptoManana\Utilities\TokenGenerator;
use CryptoManana\DataStructures\KeyPair;

/**
 * Class DigitalSignatureTest - Testing the digital signature standard cryptographic protocol object.
 *
 * @package CryptoManana\Tests\IntegrationSuite\CryptographicProtocol
 */
final class DigitalSignatureTest extends AbstractIntegrationTest
{
    /**
     * The filename for the private key temporary file.
     */
    const PRIVATE_KEY_FILENAME_FOR_TESTS = 'dsa_1024_private.key';

    /**
     * The filename for the public key temporary file.
     */
    const PUBLIC_KEY_FILENAME_FOR_TESTS = 'dsa_1024_public.key';

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
     * @return DigitalSignature Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $dsa = new Dsa1024();

        if (self::$isKeyPairGenerated === false) {
            $generator = new TokenGenerator();

            $keyPair = $generator->getAsymmetricKeyPair($dsa::KEY_SIZE, $dsa::ALGORITHM_NAME);

            $this->writeToFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS, $keyPair->private);
            $this->writeToFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS, $keyPair->public);

            self::$isKeyPairGenerated = true;
        }

        $privateKey = $this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS);
        $publicKey = $this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS);

        $keyPair = new KeyPair($privateKey, $publicKey);

        $dsa->setKeyPair($keyPair);

        return new DigitalSignature($dsa);
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
        $this->assertNotEmpty($tmp->createSignedData(''));

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
        $this->assertNotEmpty($tmp->createSignedData(''));

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
     * Testing if the basic data signature generation and verification process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDataSignatureGenerationAndVerification()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSignatureStandard($protocol->getSignatureStandard());

        $randomData = random_bytes(16);

        $signedDataObject = $protocol->createSignedData($randomData);
        $extractedVerifiedData = $protocol->extractVerifiedData($signedDataObject);

        $this->assertEquals($randomData, $extractedVerifiedData);
    }

    /**
     * Testing validation case for invalid type of digital signature service used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfDigitalSignatureServicePassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new DigitalSignature(null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new DigitalSignature(null);
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
     * Testing validation case for invalid type of input data for signing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForSigning()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->createSignedData(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->createSignedData(['wrong']);
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
     * Testing validation case for invalid type of input data for signing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSignedDataForVerification()
    {
        $protocol = $this->getCryptographicProtocolForTesting();
        $signedData = $protocol->createSignedData('1234');
        $signedData->signature = 'FFFF';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol->extractVerifiedData($signedData);
        } else {
            $hasThrown = null;

            try {
                $protocol->extractVerifiedData($signedData);
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
