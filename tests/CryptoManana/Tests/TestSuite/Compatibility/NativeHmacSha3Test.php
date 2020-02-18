<?php

/**
 * Testing the NativeHmacSha3 component used for compatibility purposes to generate HMAC-SHA-3 digests.
 */

namespace CryptoManana\Tests\TestSuite\Compatibility;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use CryptoManana\Compatibility\NativeHmacSha3;
use CryptoManana\Compatibility\NativeSha3;

/**
 * Class NativeHmacSha3Test - Tests the pure PHP implementation of the HMAC-SHA-3 algorithm.
 *
 * @package CryptoManana\Tests\TestSuite\Compatibility
 */
final class NativeHmacSha3Test extends AbstractUnitTest
{
    /**
     * The empty string representation for digest generation.
     */
    const EMPTY_STRING = '';

    /**
     * The `NULL` byte string representation for digest generation.
     */
    const THE_NUL_BYTE = "\0";

    /**
     * The default ASCII string for digest generation.
     */
    const ASCII_STRING = 'The quick brown fox jumps over the lazy dog';

    /**
     * The default complex UTF-8 string for digest generation.
     */
    const UTF_8_STRING = 'Яко x test? 2⺓⺔Я .. @ {`} cheti туКа +69!';

    /**
     * Creates pseudo-random data for input testing.
     *
     * @param bool $asHex If `true` using HEX output, else if `false` using RAW bytes output.
     * @param int $bytes The output length for pseudo-random data generation.
     *
     * @return string Returns a string containing the requested number of random bytes.
     * @throws \Exception If the input parameter is less than 1 or no source is available.
     */
    protected function getRandomData($asHex = true, $bytes = 40)
    {
        /**
         * Two test cases for algorithm: 1) Input Size >= 72; 2) Input Size < 72;
         *
         * {@internal To cover them, using 80 HEX characters or 40 RAW byte characters (as default). }}
         */
        return ($asHex) ? bin2hex(random_bytes($bytes)) : random_bytes($bytes);
    }

    /**
     * Switches the component's setting for `mbstring` usage.
     *
     * @param bool|null $use Switch the usage of `mbstring` functions.
     *
     * @throws \ReflectionException If the tested class or property does not exist.
     */
    protected function sha3UsesMbString($use = null)
    {
        $use = (is_bool($use) || is_null($use)) ? $use : null;

        $reflectionMbString = new \ReflectionProperty(
            NativeSha3::class,
            'mbString'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($use);

        $reflectionMbString = new \ReflectionProperty(
            NativeSha3::class,
            'mbString'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($use);
    }

    /**
     * Testing the object dumping for debugging.
     */
    public function testDebugCapabilities()
    {
        // Reset internal settings
        NativeSha3::resetSystemChecks();

        $NativeHmacSha3 = NativeHmacSha3::getInstance();

        $this->assertNotEmpty(var_export($NativeHmacSha3, true));
    }

    /**
     * Testing if all variation of the HMAC-SHA-3 algorithm never return an empty output digest.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $randomData = $this->getRandomData(false, 2);
        $randomKey = $this->getRandomData(false, 2);

        $this->assertNotEmpty(NativeHmacSha3::digest224($randomData, $randomKey));
        $this->assertNotEmpty(NativeHmacSha3::digest256($randomData, $randomKey));
        $this->assertNotEmpty(NativeHmacSha3::digest384($randomData, $randomKey));
        $this->assertNotEmpty(NativeHmacSha3::digest512($randomData, $randomKey));

        $this->assertNotEmpty(NativeHmacSha3::digest224($randomData, $randomKey, true));
        $this->assertNotEmpty(NativeHmacSha3::digest256($randomData, $randomKey, true));
        $this->assertNotEmpty(NativeHmacSha3::digest384($randomData, $randomKey, true));
        $this->assertNotEmpty(NativeHmacSha3::digest512($randomData, $randomKey, true));
    }

    /**
     * Testing if the generation of a digest twice with the same input returns the same result.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testCalculatingTheSameDigestTwice()
    {
        $randomData = $this->getRandomData(false, 2);
        $randomKey = $this->getRandomData(false, 2);

        $this->assertEquals(
            NativeHmacSha3::digest224($randomData, $randomKey),
            NativeHmacSha3::digest224($randomData, $randomKey)
        );

        $this->assertEquals(
            NativeHmacSha3::digest256($randomData, $randomKey),
            NativeHmacSha3::digest256($randomData, $randomKey)
        );
        $this->assertEquals(
            NativeHmacSha3::digest384($randomData, $randomKey),
            NativeHmacSha3::digest384($randomData, $randomKey)
        );
        $this->assertEquals(
            NativeHmacSha3::digest512($randomData, $randomKey),
            NativeHmacSha3::digest512($randomData, $randomKey)
        );

        $this->assertEquals(
            NativeHmacSha3::digest224($randomData, $randomKey, true),
            NativeHmacSha3::digest224($randomData, $randomKey, true)
        );

        $this->assertEquals(
            NativeHmacSha3::digest256($randomData, $randomKey, true),
            NativeHmacSha3::digest256($randomData, $randomKey, true)
        );

        $this->assertEquals(
            NativeHmacSha3::digest384($randomData, $randomKey, true),
            NativeHmacSha3::digest384($randomData, $randomKey, true)
        );

        $this->assertEquals(
            NativeHmacSha3::digest512($randomData, $randomKey, true),
            NativeHmacSha3::digest512($randomData, $randomKey, true)
        );
    }

    /**
     * Testing if the digest generation of an empty string produces the proper output for each variant of HMAC-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testEmptyStringHashing()
    {
        $digestCollection = [
            224 => '1b9044e0d5bb4ef944bc00f1b26c483ac3e222f4640935d089a49083',
            256 => 'e841c164e5b4f10c9f3985587962af72fd607a951196fc92fb3a5251941784ea',
            384 => 'adca89f07bbfbeaf58880c1572379ea2416568fd3b66542bd42599c57c4567e6ae086299ea216c6f3e7aef90b6191d24',
            512 =>
                'cbcf45540782d4bc7387fbbf7d30b3681d6d66cc435cafd82546b0fce96b367ea79662918436fba442e81a01d0f9592dfcd' .
                '30f7a7a8f1475693d30be4150ca84'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHmacSha3::digest224(self::EMPTY_STRING, self::EMPTY_STRING);
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHmacSha3::digest224(self::EMPTY_STRING, self::EMPTY_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[224]), $digest);

            $digest = NativeHmacSha3::digest256(self::EMPTY_STRING, self::EMPTY_STRING);
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHmacSha3::digest256(self::EMPTY_STRING, self::EMPTY_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[256]), $digest);

            $digest = NativeHmacSha3::digest384(self::EMPTY_STRING, self::EMPTY_STRING);
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHmacSha3::digest384(self::EMPTY_STRING, self::EMPTY_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[384]), $digest);

            $digest = NativeHmacSha3::digest512(self::EMPTY_STRING, self::EMPTY_STRING);
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHmacSha3::digest512(self::EMPTY_STRING, self::EMPTY_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[512]), $digest);
        }
    }

    /**
     * Testing if the digest generation of a `NULL` byte produces the proper output for each variant of HMAC-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testNullByteHashing()
    {
        $digestCollection = [
            224 => '716f4510ddc26f639f2dfd2fffbaa7808ec14d51b14b662bf622c11d',
            256 => 'bf34ef593c42c1346dfbffdb24b7b589176d30eadf070a3315c1d7d4d4f267da',
            384 => '206aa4bcb748159f23c2e1c3c8f22a49d0801b55dd41b124df328f08662830585e94d9b8a438306e0e909ab6a479723d',
            512 =>
                '87eaf0e51bd98e783b85a5415176bbc6fbfbff5289d4d21caa7daef7265355434e5f5ffa9c3e9c60be44b7efc291856eb26' .
                'a473756f09335898058d8c201d460'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHmacSha3::digest224(self::THE_NUL_BYTE, self::THE_NUL_BYTE);
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHmacSha3::digest224(self::THE_NUL_BYTE, self::THE_NUL_BYTE, true);
            $this->assertEquals(hex2bin($digestCollection[224]), $digest);

            $digest = NativeHmacSha3::digest256(self::THE_NUL_BYTE, self::THE_NUL_BYTE);
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHmacSha3::digest256(self::THE_NUL_BYTE, self::THE_NUL_BYTE, true);
            $this->assertEquals(hex2bin($digestCollection[256]), $digest);

            $digest = NativeHmacSha3::digest384(self::THE_NUL_BYTE, self::THE_NUL_BYTE);
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHmacSha3::digest384(self::THE_NUL_BYTE, self::THE_NUL_BYTE, true);
            $this->assertEquals(hex2bin($digestCollection[384]), $digest);

            $digest = NativeHmacSha3::digest512(self::THE_NUL_BYTE, self::THE_NUL_BYTE);
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHmacSha3::digest512(self::THE_NUL_BYTE, self::THE_NUL_BYTE, true);
            $this->assertEquals(hex2bin($digestCollection[512]), $digest);
        }
    }

    /**
     * Testing if the digest generation of an ASCII string produces the proper output for each variant of HMAC-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testPlainStringHashing()
    {
        $digestCollection = [
            224 => '4b0d31a4518b4facb611038f383619472f767e1fb56e7a72654f574d',
            256 => 'e2f178144221853d60f7e9ddaf13ea57c6bddd54d9bd18b175fc59278f491a63',
            384 => 'e3a8fad6355c0f11142f670999c2f50b5b8e756a5d10d5a3a6e4b351112224cb1e38c783d9c1eaa54492dc9c2bbe68f9',
            512 =>
                'f0beabb21e4ad509680eb5f040fa1b3d3379efbdf4894d7cab5a95f5c179143765e0dc97ba372939ae0cf7dd8e570f887d0' .
                '81f5feeb164a57c20286d034a2640'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHmacSha3::digest224(self::ASCII_STRING, self::ASCII_STRING);
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHmacSha3::digest224(self::ASCII_STRING, self::ASCII_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[224]), $digest);

            $digest = NativeHmacSha3::digest256(self::ASCII_STRING, self::ASCII_STRING);
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHmacSha3::digest256(self::ASCII_STRING, self::ASCII_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[256]), $digest);

            $digest = NativeHmacSha3::digest384(self::ASCII_STRING, self::ASCII_STRING);
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHmacSha3::digest384(self::ASCII_STRING, self::ASCII_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[384]), $digest);

            $digest = NativeHmacSha3::digest512(self::ASCII_STRING, self::ASCII_STRING);
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHmacSha3::digest512(self::ASCII_STRING, self::ASCII_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[512]), $digest);
        }
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output for each variant of HMAC-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $digestCollection = [
            224 => 'b9d50acf7991b837947d169a7b555a77441678d59df745bb0952fa7b',
            256 => 'd29100208021d06d4306ae3a430a35cd888c5bccd159344ae8f67bf3db991de3',
            384 => '60032e4457d3312db983fe08be14bd17b512cdfdf90ad88a6f548a74f89201a0d9f63ae194eec6ba8d6f1845a385dc24',
            512 =>
                '386aaf551d3f3e838dbd55460fd8b481c950c6ba046f0fdda815b1c795cb5f286c470236443210c6a8c2da1a3cfe04f5b2b' .
                'c2767bda2952c6d53decb6621ba09'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHmacSha3::digest224(self::UTF_8_STRING, self::UTF_8_STRING);
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHmacSha3::digest224(self::UTF_8_STRING, self::UTF_8_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[224]), $digest);

            $digest = NativeHmacSha3::digest256(self::UTF_8_STRING, self::UTF_8_STRING);
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHmacSha3::digest256(self::UTF_8_STRING, self::UTF_8_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[256]), $digest);

            $digest = NativeHmacSha3::digest384(self::UTF_8_STRING, self::UTF_8_STRING);
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHmacSha3::digest384(self::UTF_8_STRING, self::UTF_8_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[384]), $digest);

            $digest = NativeHmacSha3::digest512(self::UTF_8_STRING, self::UTF_8_STRING);
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHmacSha3::digest512(self::UTF_8_STRING, self::UTF_8_STRING, true);
            $this->assertEquals(hex2bin($digestCollection[512]), $digest);
        }
    }

    /**
     * Testing if the digest generation produces the same output as the `ext-hash` HMAC-SHA-3 implementation.
     *
     * @throws \Exception If the tested class does not exist or the randomness source is not available.
     */
    public function testRandomStringHashing()
    {
        $supportedAlgorithms = hash_hmac_algos();

        $nativeSupport = (
            in_array('sha3-224', $supportedAlgorithms, true) &&
            in_array('sha3-256', $supportedAlgorithms, true) &&
            in_array('sha3-384', $supportedAlgorithms, true) &&
            in_array('sha3-512', $supportedAlgorithms, true)
        );

        $randomData = $this->getRandomData();
        $randomKey = $this->getRandomData();

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            if ($nativeSupport) {
                $this->assertEquals(
                    hash_hmac('sha3-224', $randomData, $randomKey),
                    NativeHmacSha3::digest224($randomData, $randomKey)
                );

                $this->assertEquals(
                    hash_hmac('sha3-224', $randomData, $randomKey, true),
                    NativeHmacSha3::digest224($randomData, $randomKey, true)
                );

                $this->assertEquals(
                    hash_hmac('sha3-256', $randomData, $randomKey),
                    NativeHmacSha3::digest256($randomData, $randomKey)
                );

                $this->assertEquals(
                    hash_hmac('sha3-256', $randomData, $randomKey, true),
                    NativeHmacSha3::digest256($randomData, $randomKey, true)
                );

                $this->assertEquals(
                    hash_hmac('sha3-384', $randomData, $randomKey),
                    NativeHmacSha3::digest384($randomData, $randomKey)
                );

                $this->assertEquals(
                    hash_hmac('sha3-384', $randomData, $randomKey, true),
                    NativeHmacSha3::digest384($randomData, $randomKey, true)
                );

                $this->assertEquals(
                    hash_hmac('sha3-512', $randomData, $randomKey),
                    NativeHmacSha3::digest512($randomData, $randomKey)
                );

                $this->assertEquals(
                    hash_hmac('sha3-512', $randomData, $randomKey, true),
                    NativeHmacSha3::digest512($randomData, $randomKey, true)
                );
            } else {
                $this->assertEquals(
                    bin2hex(NativeHmacSha3::digest224($randomData, $randomKey, true)),
                    NativeHmacSha3::digest224($randomData, $randomKey)
                );

                $this->assertEquals(
                    NativeHmacSha3::digest224($randomData, $randomKey, true),
                    hex2bin(NativeHmacSha3::digest224($randomData, $randomKey))
                );

                $this->assertEquals(
                    bin2hex(NativeHmacSha3::digest256($randomData, $randomKey, true)),
                    NativeHmacSha3::digest256($randomData, $randomKey)
                );

                $this->assertEquals(
                    NativeHmacSha3::digest256($randomData, $randomKey, true),
                    hex2bin(NativeHmacSha3::digest256($randomData, $randomKey))
                );

                $this->assertEquals(
                    bin2hex(NativeHmacSha3::digest384($randomData, $randomKey, true)),
                    NativeHmacSha3::digest384($randomData, $randomKey)
                );

                $this->assertEquals(
                    NativeHmacSha3::digest384($randomData, $randomKey, true),
                    hex2bin(NativeHmacSha3::digest384($randomData, $randomKey))
                );

                $this->assertEquals(
                    bin2hex(NativeHmacSha3::digest512($randomData, $randomKey, true)),
                    NativeHmacSha3::digest512($randomData, $randomKey)
                );

                $this->assertEquals(
                    NativeHmacSha3::digest512($randomData, $randomKey, true),
                    hex2bin(NativeHmacSha3::digest512($randomData, $randomKey))
                );
            }
        }
    }

    /**
     * Testing validation case for wrong algorithm choosing on internal calls.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForWrongInternalUsage()
    {
        $reflection = new \ReflectionClass(NativeHmacSha3::class);
        $method = $reflection->getMethod('customHmac');

        $method->setAccessible(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $method->invoke(NativeHmacSha3::getInstance(), 'wrong one', '', '', false);
        } else {
            $hasThrown = null;

            try {
                $method->invoke(NativeHmacSha3::getInstance(), 'wrong one', '', '', false);
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
     * Testing validation case for non string type input data.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInputData()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHmacSha3::digest256(['wrong'], '');
        } else {
            $hasThrown = null;

            try {
                NativeHmacSha3::digest256(['wrong'], '');
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
     * Testing validation case for non string type input HMAC key.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringHashingKey()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHmacSha3::digest256('', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                NativeHmacSha3::digest256('', ['wrong']);
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
        $tmp = NativeHmacSha3::getInstance();

        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof NativeHmacSha3);

        $this->assertEquals(NativeHmacSha3::class, (string)$tmp);
        $reflection = new \ReflectionClass(NativeHmacSha3::class);

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
