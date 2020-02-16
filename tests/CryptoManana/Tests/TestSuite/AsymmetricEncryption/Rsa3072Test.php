<?php

/**
 * Testing the RSA-3072 realization used for data encryption/decryption.
 */

namespace CryptoManana\Tests\TestSuite\AsymmetricEncryption;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption;
use \CryptoManana\Core\Interfaces\MessageEncryption\AsymmetricPaddingInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\CipherDataFormatsInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface;
use \CryptoManana\AsymmetricEncryption\Rsa3072;
use \CryptoManana\Utilities\TokenGenerator;
use \CryptoManana\DataStructures\KeyPair;

/**
 * Class Rsa3072Test - Testing the RSA-3072 class.
 *
 * @package CryptoManana\Tests\TestSuite\AsymmetricEncryption
 */
final class Rsa3072Test extends AbstractUnitTest
{
    /**
     * The filename for the private key temporary file.
     */
    const PRIVATE_KEY_FILENAME_FOR_TESTS = 'rsa_3072_private.key';

    /**
     * The filename for the public key temporary file.
     */
    const PUBLIC_KEY_FILENAME_FOR_TESTS = 'rsa_3072_public.key';

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
     * @return Rsa3072 Testing instance.
     * @throws \Exception If the system can not generate or fetch a proper key pair.
     */
    private function getAsymmetricEncryptionAlgorithmInstanceForTesting($withoutKeys = false)
    {
        $rsa = new Rsa3072();

        if (self::$isKeyPairGenerated === false) {
            $generator = new TokenGenerator();

            $keyPair = $generator->getAsymmetricKeyPair($rsa::KEY_SIZE, $rsa::ALGORITHM_NAME);

            $this->writeToFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS, $keyPair->private);
            $this->writeToFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS, $keyPair->public);

            self::$isKeyPairGenerated = true;
        }

        if ($withoutKeys == false) {
            $privateKey = $this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS);
            $publicKey = $this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS);

            $keyPair = new KeyPair($privateKey, $publicKey);

            $rsa->setKeyPair($keyPair);
        }

        return $rsa;
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $tmp = clone $crypter;

        $this->assertEquals($crypter, $tmp);
        $this->assertNotEmpty($tmp->encryptData(''));

        unset($tmp);
        $this->assertNotNull($crypter);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $tmp = serialize($crypter);
        $tmp = unserialize($tmp);

        $this->assertEquals($crypter, $tmp);
        $this->assertNotEmpty($tmp->encryptData(''));

        unset($tmp);
        $this->assertNotNull($crypter);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertNotEmpty(var_export($crypter, true));

        $reflection = new \ReflectionClass($crypter);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($crypter);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing the import and export of the key pair.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testKeyPairImportAndExportFeature()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $crypter->checkIfThePrivateKeyIsSet();
        $crypter->checkIfThePublicKeyIsSet();

        $keyPair = $crypter->getKeyPair();

        $this->assertTrue($keyPair->private === $crypter->getPrivateKey());
        $this->assertTrue($keyPair->public === $crypter->getPublicKey());

        $crypter->setKeyPair($keyPair);
    }

    /**
     * Testing if the basic data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataEncryptionAndDataDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof AbstractAsymmetricEncryptionAlgorithm);
        $this->assertTrue($crypter instanceof AbstractRsaEncryption);
        $this->assertTrue($crypter instanceof DataEncryptionInterface);
        $this->assertTrue($crypter instanceof Rsa3072);

        $testCases = [true, false];

        foreach ($testCases as $chunkProcessingFlag) {
            ($chunkProcessingFlag) ? $crypter->enableChunkProcessing() : $crypter->disableChunkProcessing();

            $dataLength = ($chunkProcessingFlag) ? ($crypter::KEY_SIZE * 8 + 10) : 5;
            $dummyData = str_repeat('1', $dataLength);

            $crypter->setPaddingStandard($crypter::OAEP_PADDING)->setCipherFormat($crypter::ENCRYPTION_OUTPUT_RAW);

            $this->assertEquals($crypter::OAEP_PADDING, $crypter->getPaddingStandard());
            $this->assertEquals($crypter::ENCRYPTION_OUTPUT_RAW, $crypter->getCipherFormat());

            $encryptedData = $crypter->encryptData($dummyData);
            $decryptedData = $crypter->decryptData($encryptedData);

            $this->assertEquals($dummyData, $decryptedData);
        }
    }

    /**
     * Testing if the unicode data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataEncryptionAndDataDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof AbstractAsymmetricEncryptionAlgorithm);
        $this->assertTrue($crypter instanceof AbstractRsaEncryption);
        $this->assertTrue($crypter instanceof Rsa3072);

        $unicodeData = "АJx 9,Й$\v@UdrЯЙЮ";
        $testCases = [true, false];

        foreach ($testCases as $chunkProcessingFlag) {
            ($chunkProcessingFlag) ? $crypter->enableChunkProcessing() : $crypter->disableChunkProcessing();

            $crypter->setPaddingStandard($crypter::OAEP_PADDING)->setCipherFormat($crypter::ENCRYPTION_OUTPUT_RAW);

            $this->assertEquals($crypter::OAEP_PADDING, $crypter->getPaddingStandard());
            $this->assertEquals($crypter::ENCRYPTION_OUTPUT_RAW, $crypter->getCipherFormat());

            $encryptedData = $crypter->encryptData($unicodeData);
            $decryptedData = $crypter->decryptData($encryptedData);

            $this->assertEquals($unicodeData, $decryptedData);
        }
    }

    /**
     * Testing if encrypting twice the same input returns different result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptingTheSameDataTwiceReturnsDifferentPaddingData()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $randomData = random_bytes(16);

        $this->assertTrue($crypter->encryptData($randomData) !== $crypter->encryptData($randomData));
    }

    /**
     * Testing if decrypting twice the cipher data from two separate encryption will return the same input result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDecryptingTheCipherDataTwiceReturnsTheCorrectInputData()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $randomData = random_bytes(16);
        $encryptedData = $crypter->encryptData($randomData);
        $encryptedDataSecond = $crypter->encryptData($randomData);

        $this->assertEquals(
            $crypter->decryptData($encryptedData),
            $crypter->decryptData($encryptedData)
        );

        $this->assertEquals(
            $crypter->decryptData($encryptedData),
            $crypter->decryptData($encryptedDataSecond)
        );
    }

    /**
     * Testing if the encrypting of data with importing just the public key works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptingTheDataWithJustThePublicKeyImported()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(false);

        $crypter->setPublicKey($this->readFromFile(self::PUBLIC_KEY_FILENAME_FOR_TESTS));

        $randomData = random_bytes(16);

        $this->assertNotEmpty($crypter->encryptData($randomData));
    }

    /**
     * Testing if the decrypting of cipher data with importing just the private key works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDecryptingTheCipherDataWithJustThePrivateKeyImported()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(false);

        $randomData = random_bytes(16);
        $encryptedData = $crypter->encryptData($randomData);

        $crypter = null;
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(true);

        $crypter->setPrivateKey($this->readFromFile(self::PRIVATE_KEY_FILENAME_FOR_TESTS));

        $this->assertEquals($randomData, $crypter->decryptData($encryptedData));
    }

    /**
     * Testing the algorithm encryption and decryption for all padding standards.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testAsymmetricPaddingStandards()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof AsymmetricPaddingInterface);

        $randomData = random_bytes(16);

        $validPaddingStandards = [
            $crypter::PKCS1_PADDING,
            $crypter::OAEP_PADDING,
        ];

        foreach ($validPaddingStandards as $paddingStandard) {
            $crypter->setPaddingStandard($paddingStandard);
            $this->assertEquals($paddingStandard, $crypter->getPaddingStandard());

            $encryptedData = $crypter->encryptData($randomData);
            $decryptedData = $crypter->decryptData($encryptedData);

            $this->assertEquals($randomData, $decryptedData);
        }
    }

    /**
     * Testing the algorithm encryption and decryption for all output cipher formats.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCipherOutputFormats()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof CipherDataFormatsInterface);

        $randomData = random_bytes(16);

        $hexLowerCasePattern = '/^[a-f0-9]+$/';
        $hexUpperCasePattern = '/^[A-F0-9]+$/';
        $base64Pattern = '%^[a-zA-Z0-9/+]*={0,2}$%';
        $base64UrlFriendlyPattern = '/^[a-zA-Z0-9_-]+$/';

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_RAW);
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_RAW, $crypter->getCipherFormat());

        $encryptedData = $crypter->encryptData($randomData);
        $decryptedData = $crypter->decryptData($encryptedData);

        $this->assertEquals($randomData, $decryptedData);

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_HEX_LOWER);
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_HEX_LOWER, $crypter->getCipherFormat());

        $encryptedData = $crypter->encryptData($randomData);
        $decryptedData = $crypter->decryptData($encryptedData);

        $this->assertEquals(1, preg_match($hexLowerCasePattern, $encryptedData));
        $this->assertEquals($randomData, $decryptedData);

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_HEX_UPPER);
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_HEX_UPPER, $crypter->getCipherFormat());

        $encryptedData = $crypter->encryptData($randomData);
        $decryptedData = $crypter->decryptData($encryptedData);

        $this->assertEquals(1, preg_match($hexUpperCasePattern, $encryptedData));
        $this->assertEquals($randomData, $decryptedData);

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_BASE_64);
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_BASE_64, $crypter->getCipherFormat());

        $encryptedData = $crypter->encryptData($randomData);
        $decryptedData = $crypter->decryptData($encryptedData);

        $this->assertEquals(1, preg_match($base64Pattern, $encryptedData));
        $this->assertEquals($randomData, $decryptedData);

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_BASE_64_URL);
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_BASE_64_URL, $crypter->getCipherFormat());

        $encryptedData = $crypter->encryptData($randomData);
        $decryptedData = $crypter->decryptData($encryptedData);

        $this->assertEquals(1, preg_match($base64UrlFriendlyPattern, $encryptedData));
        $this->assertEquals($randomData, $decryptedData);
    }

    /**
     * Testing simple object encryption and decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testObjectEncryptionFeature()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof ObjectEncryptionInterface);

        $object = new \stdClass();
        $object->test = 'test';

        $encryptedString = $crypter->encryptData(serialize($object));
        $encryptedObject = $crypter->encryptObject($object);

        $this->assertTrue($encryptedString !== $encryptedObject); // padding bytes

        $this->assertEquals(
            unserialize($crypter->decryptData($encryptedString)),
            $crypter->decryptObject($encryptedObject)
        );
    }

    /**
     * Testing simple file encryption and decryption.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testFileEncryptionFeature()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof FileEncryptionInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $encryptedString = $crypter->encryptData($this->readFromFile($fileName));
        $encryptedFileContent = $crypter->encryptFile($fileName);

        $this->assertTrue($encryptedString !== $encryptedFileContent); // padding bytes

        $this->writeToFile($fileName, $encryptedFileContent);

        $this->assertEquals(
            $crypter->decryptData($encryptedString),
            $crypter->decryptFile($fileName)
        );

        $this->deleteTheFile($fileName);
    }

    /**
     * Testing validation case for invalid input plain data used for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidPlainDataUsedForEncryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->encryptData(['none']);
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptData(['none']);
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
     * Testing validation case for invalid input cipher data used for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidCipherDataUsedForDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptData(['none']);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData(['none']);
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
     * Testing validation case for empty string cipher data used for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForEmptyStringCipherDataUsedForDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptData('');
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData('');
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
     * Testing validation case for non-cipher data string used for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonCipherDataStringUsedForDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptData('яз!');
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData('яз!');
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
     * Testing validation case for trying to encrypt an unsupported input data size when not using chunk processing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToEncryptHugeDataWithoutChunkProcessing()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $crypter->disableChunkProcessing();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->encryptData(str_repeat('1', $crypter::KEY_SIZE * 8 + 10));
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptData(str_repeat('1', $crypter::KEY_SIZE * 8 + 10));
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
     * Testing validation case for trying to decrypt an unsupported cipher data size when not using chunk processing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToDecryptHugeCipherDataWithoutChunkProcessing()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $crypter->enableChunkProcessing();

        $cipherData = $crypter->encryptData(str_repeat('1', $crypter::KEY_SIZE * 8 + 10));

        $crypter->disableChunkProcessing();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptData($cipherData);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData($cipherData);
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
     * Testing validation case for trying to encrypt without setting keys first.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToEncryptWithoutAnyKeysSet()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $crypter->encryptData('1234');
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptData('1234');
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
     * Testing validation case for trying to decrypt without setting keys first.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToDecryptWithoutAnyKeysSet()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(false);

        $cipherData = $crypter->encryptData('1234');
        $crypter = null;

        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $crypter->decryptData($cipherData);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData($cipherData);
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
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setPrivateKey(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->setPrivateKey(['wrong']);
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
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setPublicKey(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->setPublicKey(['wrong']);
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
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $keyPair = new KeyPair();

        $keyPair->private = 'яаьц';
        $keyPair->public = $crypter->getPublicKey();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setKeyPair($keyPair);
        } else {
            $hasThrown = null;

            try {
                $crypter->setKeyPair($keyPair);
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
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $keyPair = new KeyPair();

        $keyPair->private = $crypter->getPrivateKey();
        $keyPair->public = 'яаьц';

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setKeyPair($keyPair);
        } else {
            $hasThrown = null;

            try {
                $crypter->setKeyPair($keyPair);
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
     * Testing validation case for setting an invalid final block padding standard.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidPaddingStandard()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setPaddingStandard(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->setPaddingStandard(['wrong']);
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
     * Testing validation case for setting an invalid output cipher format.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidCipherOutputFormat()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setCipherFormat(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->setCipherFormat(['wrong']);
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
     * Testing validation case for trying to encrypt an unsupported or defected plain data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToEncryptDefectedPlainData()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_HEX_UPPER);

        // Simulating defection from library or invalid encoding via changing the padding mode to an invalid one
        $reflectionMbString = new \ReflectionProperty(
            $crypter,
            'padding'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($crypter, -1000);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->encryptData('1234');
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptData('1234');
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
     * Testing validation case for trying to decrypt an unsupported or defected cipher data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForAttemptingToDecryptDefectedCipherData()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $crypter->setCipherFormat($crypter::ENCRYPTION_OUTPUT_HEX_UPPER);
        $cipherData = $crypter->encryptData('');

        $cipherData = strrev($cipherData);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptData($cipherData);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptData($cipherData);
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
     * Testing validation case for invalid type of filename used for file encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForFileEncryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->encryptFile(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptFile(['wrong']);
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
     * Testing validation case for invalid type of filename used for file decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForFileDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptFile(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptFile(['wrong']);
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
     * Testing validation case for invalid type of filename used for file encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonExistingFileNameUsedForFileEncryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $crypter->encryptFile('non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptFile('non-existing.tmp');
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
     * Testing validation case for invalid type of filename used for file decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonExistingFileNameUsedForFileDecryption()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $crypter->decryptFile('non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptFile('non-existing.tmp');
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
     * Testing validation case for invalid type of input used for encrypting objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypePassedForEncryptingObjects()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->encryptObject(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->encryptObject(['wrong']);
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
     * Testing validation case for invalid type of input used for decrypting objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypePassedForDecryptingObjects()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptObject(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptObject(['wrong']);
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
     * Testing validation case for invalid type of serialized input used for decrypting objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSerializedDataPassedForDecryptingObjects()
    {
        $crypter = $this->getAsymmetricEncryptionAlgorithmInstanceForTesting();

        $invalidSerializedData = $crypter->encryptData(serialize(['wrong']));

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->decryptObject($invalidSerializedData);
        } else {
            $hasThrown = null;

            try {
                $crypter->decryptObject($invalidSerializedData);
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
