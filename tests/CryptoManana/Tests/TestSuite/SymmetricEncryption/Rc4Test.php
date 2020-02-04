<?php

/**
 * Testing the RC4-128 realization used for data encryption/decryption.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractStreamCipherAlgorithm;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractSymmetricEncryptionAlgorithm;
use \CryptoManana\Core\Interfaces\MessageEncryption\SecretKeyInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\CipherDataFormatsInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface;
use \CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface;
use \CryptoManana\SymmetricEncryption\Rc4;

/**
 * Class Rc4Test - Testing the RC4-128 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class Rc4Test extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return Rc4 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getSymmetricEncryptionAlgorithmInstanceForTesting()
    {
        return new Rc4();
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertNotEmpty(var_export($crypter, true));

        $reflection = new \ReflectionClass($crypter);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($crypter);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing if the basic data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataEncryptionAndDataDecryption()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof AbstractSymmetricEncryptionAlgorithm);
        $this->assertTrue($crypter instanceof AbstractStreamCipherAlgorithm);
        $this->assertTrue($crypter instanceof DataEncryptionInterface);
        $this->assertTrue($crypter instanceof Rc4);

        $randomKey = random_bytes($crypter::KEY_SIZE);
        $randomData = random_bytes($crypter::KEY_SIZE * 2);

        $crypter->setSecretKey($randomKey)->setCipherFormat($crypter::ENCRYPTION_OUTPUT_RAW);

        $this->assertEquals($randomKey, $crypter->getSecretKey());
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_RAW, $crypter->getCipherFormat());

        $testCases = in_array('RC4', openssl_get_cipher_methods(), true) ? [true, false] : [true];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                Rc4::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($crypter, $toUse);

            $encryptedData = $crypter->encryptData($randomData);
            $decryptedData = $crypter->decryptData($encryptedData);

            $this->assertEquals($randomData, $decryptedData);
        }
    }

    /**
     * Testing if the unicode data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataEncryptionAndDataDecryption()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $randomKey = random_bytes($crypter::KEY_SIZE);

        $unicodeData = "яJx 3!Й$\v@UdrЯЗЮ"; // length => $crypter::KEY_SIZE

        $crypter->setSecretKey($randomKey)->setCipherFormat($crypter::ENCRYPTION_OUTPUT_RAW);

        $this->assertEquals($randomKey, $crypter->getSecretKey());
        $this->assertEquals($crypter::ENCRYPTION_OUTPUT_RAW, $crypter->getCipherFormat());

        $testCases = in_array('RC4', openssl_get_cipher_methods(), true) ? [true, false] : [true];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                Rc4::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($crypter, $toUse);

            $encryptedData = $crypter->encryptData($unicodeData);
            $decryptedData = $crypter->decryptData($encryptedData);

            $this->assertEquals($unicodeData, $decryptedData);
        }
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptingTheSameDataTwice()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $randomData = random_bytes($crypter::KEY_SIZE);

        $this->assertEquals(
            $crypter->encryptData($randomData),
            $crypter->encryptData($randomData)
        );
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDecryptingTheSameDataTwice()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $randomData = random_bytes($crypter::KEY_SIZE);
        $encryptedData = $crypter->encryptData($randomData);

        $this->assertEquals(
            $crypter->decryptData($encryptedData),
            $crypter->decryptData($encryptedData)
        );
    }

    /**
     * Testing the internal transformations for the used key size when a non-exact values was passed.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testKeySizeAndInternalTransformations()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof SecretKeyInterface);

        $emptyKey = '';
        $crypter->setSecretKey($emptyKey);

        $this->assertTrue(strlen($crypter->getSecretKey()) === $crypter::KEY_SIZE);
        $this->assertEquals(str_repeat("\0", $crypter::KEY_SIZE), $crypter->getSecretKey());

        $shortKey = '1234';
        $crypter->setSecretKey($shortKey);

        $this->assertTrue(strlen($crypter->getSecretKey()) === $crypter::KEY_SIZE);
        $this->assertEquals(
            '1234' . str_repeat("\0", $crypter::KEY_SIZE - 4),
            $crypter->getSecretKey()
        );

        $exactKey = str_repeat('1', $crypter::KEY_SIZE);
        $crypter->setSecretKey($exactKey);

        $this->assertTrue(strlen($crypter->getSecretKey()) === $crypter::KEY_SIZE);
        $this->assertEquals($exactKey, $crypter->getSecretKey());

        $longKey = str_repeat('1', $crypter::KEY_SIZE * 2);
        $crypter->setSecretKey($longKey);

        $this->assertTrue(strlen($crypter->getSecretKey()) === $crypter::KEY_SIZE);
        $this->assertEquals(
            hash_hkdf('sha256', $longKey, $crypter::KEY_SIZE, 'CryptoMañana', ''),
            $crypter->getSecretKey()
        );
    }

    /**
     * Testing the algorithm encryption and decryption for all output cipher formats.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCipherOutputFormats()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof CipherDataFormatsInterface);

        $randomData = random_bytes($crypter::KEY_SIZE);

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof ObjectEncryptionInterface);

        $object = new \stdClass();
        $object->test = 'test';

        $encryptedString = $crypter->encryptData(serialize($object));
        $encryptedObject = $crypter->encryptObject($object);

        $this->assertEquals($encryptedString, $encryptedObject);

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        $this->assertTrue($crypter instanceof FileEncryptionInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $encryptedString = $crypter->encryptData($this->readFromFile($fileName));
        $encryptedFileContent = $crypter->encryptFile($fileName);

        $this->assertEquals($encryptedString, $encryptedFileContent);

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
     * Testing validation case for setting an invalid secret key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForSettingAnInvalidSecretKey()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $crypter->setSecretKey(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $crypter->setSecretKey(['wrong']);
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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
     * Testing validation case for invalid type of filename used for file encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForFileEncryption()
    {
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
        $crypter = $this->getSymmetricEncryptionAlgorithmInstanceForTesting();

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
}
