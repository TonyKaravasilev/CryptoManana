<?php

/**
 * Testing the digital signature standard cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\DigitalSignature;
use CryptoManana\AsymmetricEncryption\Dsa1024;
use CryptoManana\DataStructures\KeyPair;

/**
 * Class DigitalSignatureTest - Testing the digital signature standard cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class DigitalSignatureTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return DigitalSignature Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $dsa = $this->getMockBuilder(Dsa1024::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $dsa->setKeyPair(new KeyPair(base64_encode('1234'), base64_encode('1234')));

        $dsa->expects($this->atLeast(0))
            ->method('setKeyPair')
            ->willReturnSelf();

        $dsa->expects($this->atLeast(0))
            ->method('signData')
            ->willReturn('FFFF');

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
     * Testing if the basic data signature generation and verification process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDataSignatureGenerationAndVerification()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

        $protocol->setSignatureStandard($protocol->getSignatureStandard());

        $randomData = random_bytes(16);

        $protocol->getSignatureStandard()
            ->method('verifyDataSignature')
            ->willReturn(true);

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

        $protocol->getSignatureStandard()
            ->method('verifyDataSignature')
            ->willReturn(false);

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
}
