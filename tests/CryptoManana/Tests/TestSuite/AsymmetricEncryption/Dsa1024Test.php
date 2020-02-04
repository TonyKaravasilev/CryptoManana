<?php

/**
 * Testing the DSA-1024 realization used for data signing/verification.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature;
use \CryptoManana\Core\Interfaces\MessageEncryption\SignatureDigestionInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\SignatureDataFormatsInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\FileSigningInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\ObjectSigningInterface;
use \CryptoManana\AsymmetricEncryption\Dsa1024;
use \CryptoManana\Utilities\TokenGenerator;

/**
 * Class Dsa1024Test - Testing the DSA-1024 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class Dsa1024Test extends AbstractUnitTest
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
     * @param bool|int|null $withoutKeys Flag for returning the object without any keys set.
     *
     * @return Dsa1024 Testing instance.
     * @throws \Exception If the system can not generate or fetch a proper key pair.
     */
    private function getDigitalSignatureAlgorithmInstanceForTesting($withoutKeys = false)
    {
        $dsa = new Dsa1024();

        if (self::$isKeyPairGenerated === false) {
            $generator = new TokenGenerator();

            $keyPair = $generator->getAsymmetricKeyPair($dsa::KEY_SIZE, $dsa::ALGORITHM_NAME);

            $this->writeToFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS, $keyPair->{$dsa::PRIVATE_KEY_INDEX_NAME});
            $this->writeToFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS, $keyPair->{$dsa::PUBLIC_KEY_INDEX_NAME});

            self::$isKeyPairGenerated = true;
        }

        if ($withoutKeys == false) {
            $privateKey = $this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS);
            $publicKey = $this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS);

            $dsa->setKeyPair($privateKey, $publicKey);
        }

        return $dsa;
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $tmp = clone $signature;

        $this->assertEquals($signature, $tmp);
        $this->assertNotEmpty($tmp->signData(''));

        unset($tmp);
        $this->assertNotNull($signature);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $tmp = serialize($signature);
        $tmp = unserialize($tmp);

        $this->assertEquals($signature, $tmp);
        $this->assertNotEmpty($tmp->signData(''));

        unset($tmp);
        $this->assertNotNull($signature);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertNotEmpty(var_export($signature, true));

        $reflection = new \ReflectionClass($signature);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($signature);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing the import and export of the key pair.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testKeyPairImportAndExportFeature()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $signature->checkIfThePrivateKeyIsSet();
        $signature->checkIfThePublicKeyIsSet();

        $keyPair = $signature->getKeyPair();

        $this->assertTrue($keyPair->{$signature::PRIVATE_KEY_INDEX_NAME} === $signature->getPrivateKey());
        $this->assertTrue($keyPair->{$signature::PUBLIC_KEY_INDEX_NAME} === $signature->getPublicKey());

        $signature->setKeyPair(
            $keyPair->{$signature::PRIVATE_KEY_INDEX_NAME},
            $keyPair->{$signature::PUBLIC_KEY_INDEX_NAME}
        );

        $keyPairCopy = $signature->getKeyPair(true);

        $this->assertTrue(
            $keyPair->{$signature::PRIVATE_KEY_INDEX_NAME} === $keyPairCopy[$signature::PRIVATE_KEY_INDEX_NAME]
        );
        $this->assertTrue(
            $keyPair->{$signature::PUBLIC_KEY_INDEX_NAME} === $keyPairCopy[$signature::PUBLIC_KEY_INDEX_NAME]
        );

        $signature->setPrivateKey($keyPair->{$signature::PRIVATE_KEY_INDEX_NAME});
        $signature->setPublicKey($keyPair->{$signature::PUBLIC_KEY_INDEX_NAME});
        $this->assertEquals($keyPair->{$signature::PRIVATE_KEY_INDEX_NAME}, $signature->getPrivateKey());
        $this->assertEquals($keyPair->{$signature::PUBLIC_KEY_INDEX_NAME}, $signature->getPublicKey());
    }

    /**
     * Testing if the basic data signing and signature verification.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataSigningAndSignatureVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof AbstractAsymmetricEncryptionAlgorithm);
        $this->assertTrue($signature instanceof AbstractDsaSignature);
        $this->assertTrue($signature instanceof DataSigningInterface);
        $this->assertTrue($signature instanceof Dsa1024);

        $randomData = random_bytes(16);

        $signature->setSignatureDigestion($signature::SHA1_SIGNING)
            ->setSignatureFormat($signature::SIGNATURE_OUTPUT_HEX_LOWER);

        $this->assertEquals($signature::SHA1_SIGNING, $signature->getSignatureDigestion());
        $this->assertEquals($signature::SIGNATURE_OUTPUT_HEX_LOWER, $signature->getSignatureFormat());

        $digest = $signature->signData($randomData);
        $verification = $signature->verifyDataSignature($digest, $randomData);

        $this->assertTrue($verification);
    }

    /**
     * Testing if the unicode data signing and signature verification.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataSigningAndSignatureVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof AbstractAsymmetricEncryptionAlgorithm);
        $this->assertTrue($signature instanceof AbstractDsaSignature);
        $this->assertTrue($signature instanceof DataSigningInterface);
        $this->assertTrue($signature instanceof Dsa1024);

        $unicodeData = "ZJm 2,Й%\v@UdrЯЙЪ";

        $signature->setSignatureDigestion($signature::SHA1_SIGNING)
            ->setSignatureFormat($signature::SIGNATURE_OUTPUT_HEX_LOWER);

        $this->assertEquals($signature::SHA1_SIGNING, $signature->getSignatureDigestion());
        $this->assertEquals($signature::SIGNATURE_OUTPUT_HEX_LOWER, $signature->getSignatureFormat());

        $digest = $signature->signData($unicodeData);
        $verification = $signature->verifyDataSignature($digest, $unicodeData);

        $this->assertTrue($verification);
    }

    /**
     * Testing if signing twice the same input returns different result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testSignTheSameDataTwiceReturnsDifferentPaddingData()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $randomData = random_bytes(16);

        $this->assertTrue($signature->signData($randomData) !== $signature->signData($randomData));
    }

    /**
     * Testing if signing twice the same input returns the same verification result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testVerifyTheSignatureDataTwice()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $randomData = random_bytes(16);
        $digestionData = $signature->signData($randomData);
        $digestionDataSecond = $signature->signData($randomData);

        $this->assertEquals(
            $signature->verifyDataSignature($digestionData, $randomData),
            $signature->verifyDataSignature($digestionData, $randomData)
        );

        $this->assertEquals(
            $signature->verifyDataSignature($digestionDataSecond, $randomData),
            $signature->verifyDataSignature($digestionDataSecond, $randomData)
        );
    }

    /**
     * Testing if the signing of data with importing just the private key works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testSigningTheDataWithJustThePrivateKeyImported()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(true);

        $signature->setPrivateKey($this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS));

        $randomData = random_bytes(16);

        $this->assertNotEmpty($signature->signData($randomData));
    }

    /**
     * Testing if the verifying a signature with importing just the public key works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testVerifyingTheSignatureDataWithJustThePublicKeyImported()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(false);

        $randomData = random_bytes(16);
        $signatureData = $signature->signData($randomData);

        $signature = null;
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(true);

        $signature->setPublicKey($this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS));

        $this->assertTrue($signature->verifyDataSignature($signatureData, $randomData));
    }

    /**
     * Testing the algorithm singing and verification for all padding standards.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDigitalSignatureDigestionStandards()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof SignatureDigestionInterface);

        $randomData = random_bytes(16);

        $validSignatureDigestions = [
            $signature::SHA1_SIGNING,
            $signature::SHA2_224_SIGNING,
            $signature::SHA2_256_SIGNING,
            $signature::SHA2_384_SIGNING,
            $signature::SHA2_512_SIGNING,
        ];

        foreach ($validSignatureDigestions as $digestion) {
            $signature->setSignatureDigestion($digestion);
            $this->assertEquals($digestion, $signature->getSignatureDigestion());

            $signatureData = $signature->signData($randomData);
            $verificationResult = $signature->verifyDataSignature($signatureData, $randomData);

            $this->assertTrue($verificationResult);
        }
    }

    /**
     * Testing the algorithm signing and verification for all digital signature internal digestion standard.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSignatureOutputFormats()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof SignatureDataFormatsInterface);

        $randomData = random_bytes(16);

        $hexLowerCasePattern = '/^[a-f0-9]+$/';
        $hexUpperCasePattern = '/^[A-F0-9]+$/';
        $base64Pattern = '%^[a-zA-Z0-9/+]*={0,2}$%';
        $base64UrlFriendlyPattern = '/^[a-zA-Z0-9_-]+$/';

        $signature->setSignatureFormat($signature::SIGNATURE_OUTPUT_RAW);
        $this->assertEquals($signature::SIGNATURE_OUTPUT_RAW, $signature->getSignatureFormat());

        $digestionData = $signature->signData($randomData);
        $verificationResult = $signature->verifyDataSignature($digestionData, $randomData);

        $this->assertTrue($verificationResult);

        $signature->setSignatureFormat($signature::SIGNATURE_OUTPUT_HEX_LOWER);
        $this->assertEquals($signature::SIGNATURE_OUTPUT_HEX_LOWER, $signature->getSignatureFormat());

        $digestionData = $signature->signData($randomData);
        $verificationResult = $signature->verifyDataSignature($digestionData, $randomData);

        $this->assertTrue($verificationResult);
        $this->assertEquals(1, preg_match($hexLowerCasePattern, $digestionData));

        $signature->setSignatureFormat($signature::SIGNATURE_OUTPUT_HEX_UPPER);
        $this->assertEquals($signature::SIGNATURE_OUTPUT_HEX_UPPER, $signature->getSignatureFormat());

        $digestionData = $signature->signData($randomData);
        $verificationResult = $signature->verifyDataSignature($digestionData, $randomData);

        $this->assertTrue($verificationResult);
        $this->assertEquals(1, preg_match($hexUpperCasePattern, $digestionData));

        $signature->setSignatureFormat($signature::SIGNATURE_OUTPUT_BASE_64);
        $this->assertEquals($signature::SIGNATURE_OUTPUT_BASE_64, $signature->getSignatureFormat());

        $digestionData = $signature->signData($randomData);
        $verificationResult = $signature->verifyDataSignature($digestionData, $randomData);

        $this->assertTrue($verificationResult);
        $this->assertEquals(1, preg_match($base64Pattern, $digestionData));

        $signature->setSignatureFormat($signature::SIGNATURE_OUTPUT_BASE_64_URL);
        $this->assertEquals($signature::SIGNATURE_OUTPUT_BASE_64_URL, $signature->getSignatureFormat());

        $digestionData = $signature->signData($randomData);
        $verificationResult = $signature->verifyDataSignature($digestionData, $randomData);

        $this->assertTrue($verificationResult);
        $this->assertEquals(1, preg_match($base64UrlFriendlyPattern, $digestionData));
    }

    /**
     * Testing simple object signing and verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testObjectSigningAndVerificationFeature()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof ObjectSigningInterface);

        $object = new \stdClass();
        $object->test = 'test';

        $stringSignature = $signature->signData(serialize($object));
        $objectSignature = $signature->signObject($object);

        $this->assertTrue($stringSignature !== $objectSignature);

        $this->assertEquals(
            $signature->verifyDataSignature($stringSignature, serialize($object)),
            $signature->verifyObjectSignature($objectSignature, $object)
        );
    }

    /**
     * Testing simple file signing and verification.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testFileSigningAndVerificationFeature()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertTrue($signature instanceof FileSigningInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $stringSignature = $signature->signData($this->readFromFile($fileName));
        $fileSignature = $signature->signFile($fileName);

        $this->assertTrue($stringSignature !== $fileSignature); // padding bytes

        $this->assertEquals(
            $signature->verifyDataSignature($stringSignature, $this->readFromFile($fileName)),
            $signature->verifyFileSignature($fileSignature, $fileName)
        );

        $this->deleteTheFile($fileName);
    }

    /**
     * Testing validation case for invalid input plain data used for signing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidPlainDataUsedForSigning()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->signData(['none']);
        } else {
            $hasThrown = null;

            try {
                $signature->signData(['none']);
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
     * Testing validation case for invalid input signature data used for verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSignatureDataUsedForVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyDataSignature(['none'], '1234');
        } else {
            $hasThrown = null;

            try {
                $signature->verifyDataSignature(['none'], '1234');
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
     * Testing validation case for invalid input plain data used for verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidPlainDataUsedForVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyDataSignature('1234', ['none']);
        } else {
            $hasThrown = null;

            try {
                $signature->verifyDataSignature('1234', ['none']);
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
     * Testing validation case for empty string signature data used for verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForEmptyStringSignatureDataUsedForVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyDataSignature('', '1234');
        } else {
            $hasThrown = null;

            try {
                $signature->verifyDataSignature('', '1234');
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
     * Testing validation case for non-signature data string used for verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonSignatureDataStringUsedForVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $this->assertFalse($signature->verifyDataSignature('яз!', '1234'));
    }

    /**
     * Testing validation case for trying to sign without setting keys first.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToSignWithoutAnyKeysSet()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $signature->signData('1234');
        } else {
            $hasThrown = null;

            try {
                $signature->signData('1234');
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
     * Testing validation case for trying to verify signature without setting keys first.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToVerifySignatureWithoutAnyKeysSet()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(false);

        $signatureData = $signature->signData('1234');
        $signature = null;

        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $signature->verifyFileSignature($signatureData, '1234');
        } else {
            $hasThrown = null;

            try {
                $signature->verifyFileSignature($signatureData, '1234');
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
     * Testing validation case for setting an invalid private key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidPrivateKey()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setKeyPair(['wrong'], $signature->getPublicKey());
        } else {
            $hasThrown = null;

            try {
                $signature->setKeyPair(['wrong'], $signature->getPublicKey());
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
     * Testing validation case for setting an invalid public key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidPublicKey()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setKeyPair($signature->getPrivateKey(), ['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->setKeyPair($signature->getPrivateKey(), ['wrong']);
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
     * Testing validation case for setting an invalid format of a private key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingWrongFormattedStringForPrivateKey()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setKeyPair('яаьц', $signature->getPublicKey());
        } else {
            $hasThrown = null;

            try {
                $signature->setKeyPair('яаьц', $signature->getPublicKey());
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
     * Testing validation case for setting an invalid format of a public key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingWrongFormattedStringForPublicKey()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setKeyPair($signature->getPrivateKey(), 'яаьц');
        } else {
            $hasThrown = null;

            try {
                $signature->setKeyPair($signature->getPrivateKey(), 'яаьц');
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
     * Testing validation case for setting an invalid digital signature internal digestion standard.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidSignatureDigestionStandard()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setSignatureDigestion(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->setSignatureDigestion(['wrong']);
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
     * Testing validation case for setting an invalid output signature format.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidSignatureOutputFormat()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->setSignatureFormat(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->setSignatureFormat(['wrong']);
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
     * Testing validation case for invalid type of filename used for file signing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForFileSigning()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->signFile(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->signFile(['wrong']);
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
     * Testing validation case for invalid type of signature data used for file verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSignatureDataUsedForFileVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyFileSignature(['wrong'], self::PUBLIC_KEY_FILENAME_FOR_TESTS);
        } else {
            $hasThrown = null;

            try {
                $signature->verifyFileSignature(['wrong'], self::PUBLIC_KEY_FILENAME_FOR_TESTS);
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
     * Testing validation case for invalid type of filename used for file verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForFileVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyFileSignature('1234', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->verifyFileSignature('1234', ['wrong']);
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
     * Testing validation case for invalid type of filename used for file signing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonExistingFileNameUsedForFileSigning()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $signature->signFile('non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $signature->signFile('non-existing.tmp');
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
     * Testing validation case for invalid type of filename used for file verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonExistingFileNameUsedForFileVerification()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $signature->verifyFileSignature('1234', 'non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $signature->verifyFileSignature('1234', 'non-existing.tmp');
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
     * Testing validation case for invalid type of input used for signing objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypePassedForSigningObjects()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->signObject(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->signObject(['wrong']);
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
     * Testing validation case for invalid type of signature data used for object verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSignatureDataPassedForVerifyingObjects()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyObjectSignature(['wrong'], new \stdClass());
        } else {
            $hasThrown = null;

            try {
                $signature->verifyObjectSignature(['wrong'], new \stdClass());
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
     * Testing validation case for invalid type of input used for verifying objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypePassedForVerifyingObjects()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyObjectSignature('1234', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                $signature->verifyObjectSignature('1234', ['wrong']);
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
     * Testing validation case for invalid type of serialized input used for verifying objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSerializedDataPassedForVerifyingObjects()
    {
        $signature = $this->getDigitalSignatureAlgorithmInstanceForTesting();

        $invalidSerializedData = $signature->signData(serialize(['wrong']));

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $signature->verifyFileSignature($invalidSerializedData, new \stdClass());
        } else {
            $hasThrown = null;

            try {
                $signature->verifyFileSignature($invalidSerializedData, new \stdClass());
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
