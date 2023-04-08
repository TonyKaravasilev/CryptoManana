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
}
