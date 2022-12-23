<?php

/**
 * Testing the multiple symmetric encryption cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\MultipleEncryption;
use CryptoManana\SymmetricEncryption\Aes128;

/**
 * Class MultipleEncryptionTest - Testing the multiple symmetric encryption cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class MultipleEncryptionTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return MultipleEncryption Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        return new MultipleEncryption(new Aes128());
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
        $this->assertNotEmpty($tmp->multipleEncryptData(''));

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
        $this->assertNotEmpty($tmp->multipleEncryptData(''));

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
        $protocol->setKeyExpansionFunction($protocol->getKeyExpansionFunction());

        $randomData = random_bytes(16);

        $encryptedData = $protocol->multipleEncryptData($randomData, 5);
        $decryptedData = $protocol->multipleDecryptData($encryptedData, 5);

        $this->assertEquals($randomData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($randomData, 4));
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($randomData, 6));

        $protocol->setKeyExpansionFunction($protocol->getKeyExpansionFunction()->setSalt('1234'));
        $protocol = new MultipleEncryption($protocol->getSymmetricCipher(), $protocol->getKeyExpansionFunction());

        $encryptedData = $protocol->multipleEncryptData($randomData, 5);
        $decryptedData = $protocol->multipleDecryptData($encryptedData, 5);

        $this->assertEquals($randomData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($randomData, 4));
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($randomData, 6));
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
        $protocol->setKeyExpansionFunction($protocol->getKeyExpansionFunction());

        $unicodeData = "йМx 3-Й$\v@UdrЯ9Ю";

        $encryptedData = $protocol->multipleEncryptData($unicodeData, 5);
        $decryptedData = $protocol->multipleDecryptData($encryptedData, 5);

        $this->assertEquals($unicodeData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($unicodeData, 4));
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($unicodeData, 6));

        $protocol->setKeyExpansionFunction($protocol->getKeyExpansionFunction()->setSalt('1234'));
        $protocol = new MultipleEncryption($protocol->getSymmetricCipher(), $protocol->getKeyExpansionFunction());

        $encryptedData = $protocol->multipleEncryptData($unicodeData, 5);
        $decryptedData = $protocol->multipleDecryptData($encryptedData, 5);

        $this->assertEquals($unicodeData, $decryptedData);
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($unicodeData, 4));
        $this->assertNotEquals($encryptedData, $protocol->multipleEncryptData($unicodeData, 6));
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
            $protocol->multipleEncryptData($randomData),
            $protocol->multipleEncryptData($randomData)
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
        $encryptedData = $protocol->multipleEncryptData($randomData);

        $this->assertEquals(
            $protocol->multipleDecryptData($encryptedData),
            $protocol->multipleDecryptData($encryptedData)
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

            $protocol = new MultipleEncryption(null, null);
        } else {
            $hasThrown = null;

            try {
                $protocol = new MultipleEncryption(null, null);
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

            $protocol->multipleEncryptData(['wrong'], 2);
        } else {
            $hasThrown = null;

            try {
                $protocol->multipleEncryptData(['wrong'], 2);
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
     * Testing validation case for invalid type or value of the iteration count used for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidIterationCountForEncryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->multipleEncryptData('', -1000);
        } else {
            $hasThrown = null;

            try {
                $protocol->multipleEncryptData('', -1000);
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

            $protocol->multipleDecryptData(['wrong'], 2);
        } else {
            $hasThrown = null;

            try {
                $protocol->multipleDecryptData(['wrong'], 2);
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

        $encryptedData = $protocol->multipleEncryptData('');

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $protocol->multipleDecryptData($encryptedData, -1000);
        } else {
            $hasThrown = null;

            try {
                $protocol->multipleDecryptData($encryptedData, -1000);
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
