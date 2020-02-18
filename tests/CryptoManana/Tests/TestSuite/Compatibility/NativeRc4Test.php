<?php

/**
 * Testing the NativeRc4 component used for compatibility purposes to use RC4 encryption/decryption.
 */

namespace CryptoManana\Tests\TestSuite\Compatibility;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use CryptoManana\Compatibility\NativeRc4;

/**
 * Class NativeRc4Test - Tests the pure PHP implementation of the RC4 algorithm.
 *
 * @package CryptoManana\Tests\TestSuite\Compatibility
 */
final class NativeRc4Test extends AbstractUnitTest
{
    /**
     * Testing the object dumping for debugging.
     */
    public function testDebugCapabilities()
    {
        $NativeRc4 = NativeRc4::getInstance();

        $this->assertNotEmpty(var_export($NativeRc4, true));
    }

    /**
     * Testing the RC4 algorithm never return an empty output.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testTheOutputIsNeverEmpty()
    {
        $randomData = random_bytes(NativeRc4::KEY_SIZE);
        $randomKey = random_bytes(NativeRc4::KEY_SIZE);

        $encrypted = NativeRc4::encryptData($randomKey, $randomData);
        $decrypted = NativeRc4::decryptData($randomKey, $encrypted);

        $this->assertNotEmpty($encrypted);
        $this->assertNotEmpty($decrypted);
    }

    /**
     * Testing if the basic data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testBasicDataEncryptionAndDataDecryption()
    {
        $randomData = random_bytes(8);
        $randomKey = random_bytes(NativeRc4::KEY_SIZE);

        $encryptedData = NativeRc4::encryptData($randomKey, $randomData);
        $decryptedData = NativeRc4::decryptData($randomKey, $encryptedData);

        $this->assertEquals($randomData, $decryptedData);

        if (in_array('RC4', openssl_get_cipher_methods(), true)) {
            $this->assertEquals(
                $encryptedData,
                openssl_encrypt($randomData, 'RC4', $randomKey, OPENSSL_RAW_DATA, '')
            );
        }
    }

    /**
     * Testing if the unicode data encryption and decryption process works.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testUnicodeDataEncryptionAndDataDecryption()
    {
        $unicodeData = "яJx 3!Й$\v@UdrЯЗЮ"; // length => NativeRc4::KEY_SIZE
        $randomKey = random_bytes(NativeRc4::KEY_SIZE);

        $encryptedData = NativeRc4::encryptData($randomKey, $unicodeData);
        $decryptedData = NativeRc4::decryptData($randomKey, $encryptedData);

        $this->assertEquals($unicodeData, $decryptedData);

        if (in_array('RC4', openssl_get_cipher_methods(), true)) {
            $this->assertEquals(
                $unicodeData,
                openssl_decrypt($encryptedData, 'RC4', $randomKey, OPENSSL_RAW_DATA, '')
            );
        }
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testEncryptingTheSameDataTwice()
    {
        $randomData = random_bytes(32);
        $randomKey = random_bytes(2);

        $this->assertEquals(
            NativeRc4::encryptData($randomKey, $randomData),
            NativeRc4::encryptData($randomKey, $randomData)
        );
    }

    /**
     * Testing if encrypting twice the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testDecryptingTheSameDataTwice()
    {
        $randomData = random_bytes(8);
        $randomKey = random_bytes(32);

        $encryptedData = NativeRc4::encryptData($randomKey, $randomData);

        $this->assertEquals(
            NativeRc4::decryptData($randomKey, $encryptedData),
            NativeRc4::decryptData($randomKey, $encryptedData)
        );
    }

    /**
     * Testing validation case for non string type secret key passed for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringSecretKeyPassedForEncryption()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeRc4::encryptData(['wrong'], '');
        } else {
            $hasThrown = null;

            try {
                NativeRc4::encryptData(['wrong'], '');
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
     * Testing validation case for non string type secret key passed for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringSecretKeyPassedForDecryption()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeRc4::decryptData(['wrong'], '');
        } else {
            $hasThrown = null;

            try {
                NativeRc4::decryptData(['wrong'], '');
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
     * Testing validation case for non string type input data passed for encryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInputDataPassedForEncryption()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeRc4::encryptData('', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                NativeRc4::encryptData('', ['wrong']);
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
     * Testing validation case for non string type input data passed for decryption.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInputDataPassedForDecryption()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeRc4::decryptData('', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                NativeRc4::decryptData('', ['wrong']);
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
     * Testing the extended singleton functionality.
     *
     * @throws \ReflectionException If the tested class or method does not exist.
     */
    public function testSingletonInstancing()
    {
        $tmp = NativeRc4::getInstance();

        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof NativeRc4);

        $this->assertEquals(NativeRc4::class, (string)$tmp);
        $reflection = new \ReflectionClass(NativeRc4::class);

        $this->assertTrue($reflection->getConstructor()->isProtected());

        $internalMethods = [
            '__clone' => 'isPrivate',
            '__sleep' => 'isPrivate',
            '__wakeup' => 'isPrivate',
        ];

        foreach ($internalMethods as $method => $visibility) {
            $method = $reflection->getMethod($method);
            $this->assertTrue($method->{$visibility}());

            $method->setAccessible(true);

            $this->assertNull($method->invoke($tmp));
        }
    }
}
