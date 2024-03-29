<?php

/**
 * Testing the digital envelope cryptographic protocol object.
 */

namespace CryptoManana\Tests\TestSuite\CryptographicProtocol;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\CryptographicProtocol\DigitalEnvelope;
use CryptoManana\AsymmetricEncryption\Rsa1024;
use CryptoManana\SymmetricEncryption\Aes128;
use CryptoManana\Hashing\HmacShaThree384;
use CryptoManana\Randomness\CryptoRandom;
use CryptoManana\DataStructures\KeyPair;

/**
 * Class DigitalEnvelopeTest - Testing the digital envelope cryptographic protocol object.
 *
 * @package CryptoManana\Tests\TestSuite\CryptographicProtocol
 */
final class DigitalEnvelopeTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return DigitalEnvelope Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getCryptographicProtocolForTesting()
    {
        $rsa = $this->getMockBuilder(Rsa1024::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $rsa->setKeyPair(new KeyPair(base64_encode('1234'), base64_encode('1234')));

        $rsa->expects($this->atLeast(0))
            ->method('setKeyPair')
            ->willReturnSelf();

        $rsa->expects($this->atLeast(0))
            ->method('encryptData')
            ->willReturn('AAAA');

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

        $hasher = $this->getMockBuilder(HmacShaThree384::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $hasher->expects($this->atLeast(0))
            ->method('hashData')
            ->willReturn('CCCC');

        $randomness = $this->getMockBuilder(CryptoRandom::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $randomness->expects($this->atLeast(0))
            ->method('getBytes')
            ->willReturn("\0");

        $protocol = new DigitalEnvelope($rsa, $aes);

        $protocol->setKeyedDigestionFunction($hasher)
            ->setRandomGenerator($randomness);

        return $protocol;
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

        $tmp = clone $protocol;

        $this->assertEquals($protocol, $tmp);
        $this->assertNotEmpty($tmp->sealEnvelope(''));

        unset($tmp);
        $this->assertNotNull($protocol);
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

        $data = 'test';

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data);

        $protocol->getSymmetricCipher()
            ->method('decryptData')
            ->willReturn($data);

        $protocol->getKeyedDigestionFunction()
            ->method('verifyHash')
            ->willReturn(true);

        $envelopeObject = $protocol->sealEnvelope($data);
        $receivedData = $protocol->openEnvelope($envelopeObject);

        $this->assertEquals($data, $receivedData);
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
     * Testing validation case for invalid authentication tag on opening of envelope.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAuthenticationTagOnEnvelopeOpening()
    {
        $protocol = $this->getCryptographicProtocolForTesting();

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
}
