<?php

/**
 * Testing the multiple layered symmetric encryption cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\CryptographicProtocol\LayeredEncryption;
use CryptoManana\DataStructures\EncryptionLayer;
use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\SymmetricEncryption\Aes128;

/**
 * Class LayeredEncryptionTest - Testing the multiple layered symmetric encryption cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class LayeredEncryptionTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return LayeredEncryption Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $layers = [
            new EncryptionLayer(
                Aes128::class,
                'cryptomanana',
                'framework',
                Aes128::CBC_MODE,
                Aes128::PKCS7_PADDING,
                Aes128::ENCRYPTION_OUTPUT_RAW
            ),
            new EncryptionLayer(
                Aes128::class,
                'hit hard',
                'and run',
                Aes128::CFB_MODE
            )
        ];

        return new LayeredEncryption($layers);
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
        $this->assertNotEmpty($tmp->layeredEncryptData(''));

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
        $this->assertNotEmpty($tmp->layeredEncryptData(''));

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

        $protocol->setLayers($protocol->getLayers());

        $randomData = random_bytes(16);

        $encryptedData = $protocol->layeredEncryptData($randomData);
        $decryptedData = $protocol->layeredDecryptData($encryptedData);

        $this->assertEquals($randomData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($randomData, '1'));
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($randomData, 'xx'));
        $this->assertNotEquals($randomData, $protocol->layeredDecryptData($encryptedData, 'fake'));

        $layers = $protocol->getLayers();
        $layers [] = new EncryptionLayer(
            Aes128::class,
            'manana',
            'power',
            Aes128::OFB_MODE,
            Aes128::ZERO_PADDING,
            Aes128::ENCRYPTION_OUTPUT_HEX_LOWER
        );

        $protocol = new LayeredEncryption($layers);

        $encryptedData = $protocol->layeredEncryptData($randomData, 'pad me hard again');
        $decryptedData = $protocol->layeredDecryptData($encryptedData, 'pad me hard again');

        $this->assertEquals($randomData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($randomData, '22'));
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($randomData, 'я'));
        $this->assertNotEquals($randomData, $protocol->layeredDecryptData($encryptedData, 'fake'));
        $this->assertNotEquals($randomData, $protocol->layeredDecryptData($encryptedData));
    }

    /**
     * Testing if the unicode data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataEncryptionAndDataDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setLayers($protocol->getLayers());

        $unicodeData = "йМx 3-Й$\v@UdrЯ9Ю";

        $encryptedData = $protocol->layeredEncryptData($unicodeData);
        $decryptedData = $protocol->layeredDecryptData($encryptedData);

        $this->assertEquals($unicodeData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($encryptedData, '1'));
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($encryptedData, 'xx'));
        $this->assertNotEquals($unicodeData, $protocol->layeredDecryptData($encryptedData, 'nonce'));

        $layers = $protocol->getLayers();
        $layers [] = new EncryptionLayer(
            Aes128::class,
            'manana',
            'power',
            Aes128::OFB_MODE,
            Aes128::ZERO_PADDING,
            Aes128::ENCRYPTION_OUTPUT_HEX_LOWER
        );

        $protocol->setLayers($layers);

        $key = random_bytes(strlen($unicodeData) + 2);
        $encryptedData = $protocol->layeredEncryptData($unicodeData, $key);
        $decryptedData = $protocol->layeredDecryptData($encryptedData, $key);

        $this->assertEquals($unicodeData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($encryptedData, '22'));
        $this->assertNotEquals($encryptedData, $protocol->layeredEncryptData($encryptedData, 'я'));
        $this->assertNotEquals($unicodeData, $protocol->layeredDecryptData($encryptedData));
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
            $protocol->layeredEncryptData($randomData),
            $protocol->layeredEncryptData($randomData)
        );

        $this->assertEquals(
            $protocol->layeredEncryptData($randomData, 'yes'),
            $protocol->layeredEncryptData($randomData, 'yes')
        );

        $this->assertNotEquals(
            $protocol->layeredEncryptData($randomData, 'yes'),
            $protocol->layeredEncryptData($randomData, 'sir')
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
        $encryptedData = $protocol->layeredEncryptData($randomData);

        $this->assertEquals(
            $protocol->layeredDecryptData($encryptedData),
            $protocol->layeredDecryptData($encryptedData)
        );

        $encryptedData = $protocol->layeredEncryptData($randomData, 'secret');

        $this->assertEquals(
            $protocol->layeredDecryptData($encryptedData, 'secret'),
            $protocol->layeredDecryptData($encryptedData, 'secret')
        );

        $this->assertNotEquals(
            $protocol->layeredDecryptData($encryptedData, 'secret'),
            $protocol->layeredDecryptData($encryptedData, 'power')
        );
    }

    /**
     * Testing validation case for invalid type of layer configuration used on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfLayerConfigurationPassedOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new LayeredEncryption([null, null]);
        } else {
            $hasThrown = null;

            try {
                $protocol = new LayeredEncryption([null, null]);
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
     * Testing validation case for invalid number of layers set on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidNumberOfLayersOnInitialization()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new LayeredEncryption([1]);
        } else {
            $hasThrown = null;

            try {
                $protocol = new LayeredEncryption([1]);
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
     * Testing validation case for one invalid layer set on initialization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForOneInvalidLayerOnInitialization()
    {
        $layers = [
            new EncryptionLayer(Aes128::class, 'test', 'me', Aes128::ECB_MODE),
            new EncryptionLayer('\HittingItHarder', 'test', 'me', Aes128::ECB_MODE),
        ];

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $protocol = new LayeredEncryption($layers);
        } else {
            $hasThrown = null;

            try {
                $protocol = new LayeredEncryption($layers);
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

            $protocol->layeredEncryptData(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->layeredEncryptData(['wrong']);
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
     * Testing validation case for invalid type or value of the one-time padding string used for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidOneTimePaddingStringForEncryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->layeredEncryptData('', [-1000]);
        } else {
            $hasThrown = null;

            try {
                $protocol->layeredEncryptData('', [-1000]);
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
     * Testing validation case for invalid type of input data for decryption passed.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypeOfInputDataForDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->layeredDecryptData(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $protocol->layeredDecryptData(['wrong']);
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
     * Testing validation case for invalid type or value of the iteration count used for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidIterationCountForDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $encryptedData = $protocol->layeredEncryptData('');

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->layeredDecryptData($encryptedData, -1000);
        } else {
            $hasThrown = null;

            try {
                $protocol->layeredDecryptData($encryptedData, -1000);
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
