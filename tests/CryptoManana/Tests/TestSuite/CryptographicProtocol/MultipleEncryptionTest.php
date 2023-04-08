<?php

/**
 * Testing the multiple symmetric encryption cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\MultipleEncryption;
use CryptoManana\SymmetricEncryption\Aes128;
use CryptoManana\Hashing\HkdfShaTwo384;

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

        $hasher = $this->getMockBuilder(HkdfShaTwo384::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $hasher->expects($this->atLeast(0))
            ->method('hashData')
            ->willReturn('CCCC');

        return new MultipleEncryption($aes, $hasher);
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
     * Testing if the basic data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataEncryptionAndDataDecryption()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSymmetricCipher($protocol->getSymmetricCipher());
        $protocol->setKeyExpansionFunction($protocol->getKeyExpansionFunction());

        $data = 'test';

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data);

        $encryptedData = $protocol->multipleEncryptData($data, 5);
        $decryptedData = $protocol->multipleDecryptData($encryptedData, 5);

        $this->assertEquals($data, $decryptedData);
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
