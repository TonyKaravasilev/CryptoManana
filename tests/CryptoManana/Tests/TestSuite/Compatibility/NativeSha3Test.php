<?php

/**
 * Testing the NativeSha3 component used for compatibility purposes to generate SHA-3 digests.
 */

namespace CryptoManana\Tests\TestSuite\Compatibility;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use CryptoManana\Compatibility\NativeSha3;

/**
 * Class NativeSha3Test - Tests the pure PHP implementation of the SHA-3 algorithm.
 *
 * @package CryptoManana\Tests\TestSuite\Compatibility
 */
final class NativeSha3Test extends AbstractUnitTest
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
    }

    /**
     * Switches the component's setting for system word size.
     *
     * @param bool|null $use Switch the usage of x64/x86 algorithm.
     *
     * @throws \ReflectionException If the tested class or property does not exist.
     */
    protected function sha3UsesX64Algorithm($use = null)
    {
        $use = (is_bool($use) || is_null($use)) ? $use : null;

        $reflectionMbString = new \ReflectionProperty(
            NativeSha3::class,
            'isX64'
        );

        $reflectionMbString->setAccessible(true);
        $reflectionMbString->setValue($use);
    }

    /**
     * Testing the object dumping for debugging.
     */
    public function testDebugCapabilities()
    {
        $NativeSha3 = NativeSha3::getInstance();

        $this->assertNotEmpty(var_export($NativeSha3, true));
    }

    /**
     * Testing the object's ability to identify the current processor word size automatically.
     *
     * @throws \Exception If the tested class does not exist or the randomness source is not available.
     */
    public function testIdentifyingTheProcessorWordSize()
    {
        NativeSha3::resetSystemChecks();

        $reflection = new \ReflectionProperty(NativeSha3::class, 'isX64');
        $reflection->setAccessible(true);

        $this->assertNull($reflection->getValue());

        // Triggering the algorithm check and selection
        NativeSha3::digest256($this->getRandomData(true, 2));

        $this->assertEquals((PHP_INT_SIZE === 8), $reflection->getValue());
    }

    /**
     * Testing if all variation of the SHA-3 algorithm never return an empty output digest.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $randomData = $this->getRandomData(false, 2);

        $this->assertNotEmpty(NativeSha3::digest224($randomData));
        $this->assertNotEmpty(NativeSha3::digest256($randomData));
        $this->assertNotEmpty(NativeSha3::digest384($randomData));
        $this->assertNotEmpty(NativeSha3::digest512($randomData));

        $this->assertNotEmpty(NativeSha3::digest224($randomData, true));
        $this->assertNotEmpty(NativeSha3::digest256($randomData, true));
        $this->assertNotEmpty(NativeSha3::digest384($randomData, true));
        $this->assertNotEmpty(NativeSha3::digest512($randomData, true));
    }

    /**
     * Testing if the generation of a digest twice with the same input returns the same result.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testCalculatingTheSameDigestTwice()
    {
        $randomData = $this->getRandomData(false, 2);

        $this->assertEquals(
            NativeSha3::digest224($randomData),
            NativeSha3::digest224($randomData)
        );

        $this->assertEquals(
            NativeSha3::digest256($randomData),
            NativeSha3::digest256($randomData)
        );
        $this->assertEquals(
            NativeSha3::digest384($randomData),
            NativeSha3::digest384($randomData)
        );
        $this->assertEquals(
            NativeSha3::digest512($randomData),
            NativeSha3::digest512($randomData)
        );

        $this->assertEquals(
            NativeSha3::digest224($randomData, true),
            NativeSha3::digest224($randomData, true)
        );

        $this->assertEquals(
            NativeSha3::digest256($randomData, true),
            NativeSha3::digest256($randomData, true)
        );

        $this->assertEquals(
            NativeSha3::digest384($randomData, true),
            NativeSha3::digest384($randomData, true)
        );

        $this->assertEquals(
            NativeSha3::digest512($randomData, true),
            NativeSha3::digest512($randomData, true)
        );
    }

    /**
     * Testing if the digest generation of an empty string produces the proper output for each variant of SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testEmptyStringHashing()
    {
        $digestCollection = [
            224 => '6b4e03423667dbb73b6e15454f0eb1abd4597f9a1b078e3f5b5a6bc7',
            256 => 'a7ffc6f8bf1ed76651c14756a061d662f580ff4de43b49fa82d80a4b80f8434a',
            384 => '0c63a75b845e4f7d01107d852e4c2485c51a50aaaa94fc61995e71bbee983a2ac3713831264adb47fb6bd1e058d5f004',
            512 =>
                'a69f73cca23a9ac5c8b567dc185a756e97c982164fe25859e0d1dcc1475c80a615b2123af1f5f94c11e3e9402c3ac558' .
                'f500199d95b6d3e301758586281dcd26'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $testingValues = (PHP_INT_SIZE === 8) ? [true, false] : [false];

            foreach ($testingValues as $isX64) {
                $this->sha3UsesX64Algorithm($isX64);

                $digest = NativeSha3::digest224(self::EMPTY_STRING);
                $this->assertEquals($digestCollection[224], $digest);

                $digest = NativeSha3::digest224(self::EMPTY_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[224]), $digest);

                $digest = NativeSha3::digest256(self::EMPTY_STRING);
                $this->assertEquals($digestCollection[256], $digest);

                $digest = NativeSha3::digest256(self::EMPTY_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[256]), $digest);

                $digest = NativeSha3::digest384(self::EMPTY_STRING);
                $this->assertEquals($digestCollection[384], $digest);

                $digest = NativeSha3::digest384(self::EMPTY_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[384]), $digest);

                $digest = NativeSha3::digest512(self::EMPTY_STRING);
                $this->assertEquals($digestCollection[512], $digest);

                $digest = NativeSha3::digest512(self::EMPTY_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[512]), $digest);
            }

            /**
             * Do code coverage of the 64-bit algorithm for basic errors only (on a 32-bit PHP).
             *
             * {@internal On a 32-bit system, the 64-bit version is not available and will not work correctly. }}
             */
            if (PHP_INT_SIZE === 4) {
                $this->sha3UsesX64Algorithm(true); // switch to x64

                $this->assertNotEmpty(NativeSha3::digest224(self::EMPTY_STRING));
                $this->assertNotEmpty(NativeSha3::digest256(self::EMPTY_STRING));
                $this->assertNotEmpty(NativeSha3::digest384(self::EMPTY_STRING));
                $this->assertNotEmpty(NativeSha3::digest512(self::EMPTY_STRING));

                $this->assertNotEmpty(NativeSha3::digest224(self::EMPTY_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest256(self::EMPTY_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest384(self::EMPTY_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest512(self::EMPTY_STRING, true));
            }
        }

        // Reset to auto-choosing
        $this->sha3UsesMbString(null);
        $this->sha3UsesX64Algorithm(null);
    }

    /**
     * Testing if the digest generation of a `NULL` byte produces the proper output for each variant of SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testNullByteHashing()
    {
        $digestCollection = [
            224 => 'bdd5167212d2dc69665f5a8875ab87f23d5ce7849132f56371a19096',
            256 => '5d53469f20fef4f8eab52b88044ede69c77a6a68a60728609fc4a65ff531e7d0',
            384 => '127677f8b66725bbcb7c3eae9698351ca41e0eb6d66c784bd28dcdb3b5fb12d0c8e840342db03ad1ae180b92e3504933',
            512 =>
                '7127aab211f82a18d06cf7578ff49d5089017944139aa60d8bee057811a15fb55a53887600a3eceba004de51105139f3250' .
                '6fe5b53e1913bfa6b32e716fe97da'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $testingValues = (PHP_INT_SIZE === 8) ? [true, false] : [false];

            foreach ($testingValues as $isX64) {
                $this->sha3UsesX64Algorithm($isX64);

                $digest = NativeSha3::digest224(self::THE_NUL_BYTE);
                $this->assertEquals($digestCollection[224], $digest);

                $digest = NativeSha3::digest224(self::THE_NUL_BYTE, true);
                $this->assertEquals(hex2bin($digestCollection[224]), $digest);

                $digest = NativeSha3::digest256(self::THE_NUL_BYTE);
                $this->assertEquals($digestCollection[256], $digest);

                $digest = NativeSha3::digest256(self::THE_NUL_BYTE, true);
                $this->assertEquals(hex2bin($digestCollection[256]), $digest);

                $digest = NativeSha3::digest384(self::THE_NUL_BYTE);
                $this->assertEquals($digestCollection[384], $digest);

                $digest = NativeSha3::digest384(self::THE_NUL_BYTE, true);
                $this->assertEquals(hex2bin($digestCollection[384]), $digest);

                $digest = NativeSha3::digest512(self::THE_NUL_BYTE);
                $this->assertEquals($digestCollection[512], $digest);

                $digest = NativeSha3::digest512(self::THE_NUL_BYTE, true);
                $this->assertEquals(hex2bin($digestCollection[512]), $digest);
            }

            /**
             * Do code coverage of the 64-bit algorithm for basic errors only (on a 32-bit PHP).
             *
             * {@internal On a 32-bit system, the 64-bit version is not available and will not work correctly. }}
             */
            if (PHP_INT_SIZE === 4) {
                $this->sha3UsesX64Algorithm(true); // switch to x64

                $this->assertNotEmpty(NativeSha3::digest224(self::THE_NUL_BYTE));
                $this->assertNotEmpty(NativeSha3::digest256(self::THE_NUL_BYTE));
                $this->assertNotEmpty(NativeSha3::digest384(self::THE_NUL_BYTE));
                $this->assertNotEmpty(NativeSha3::digest512(self::THE_NUL_BYTE));

                $this->assertNotEmpty(NativeSha3::digest224(self::THE_NUL_BYTE, true));
                $this->assertNotEmpty(NativeSha3::digest256(self::THE_NUL_BYTE, true));
                $this->assertNotEmpty(NativeSha3::digest384(self::THE_NUL_BYTE, true));
                $this->assertNotEmpty(NativeSha3::digest512(self::THE_NUL_BYTE, true));
            }
        }

        // Reset to auto-choosing
        $this->sha3UsesMbString(null);
        $this->sha3UsesX64Algorithm(null);
    }

    /**
     * Testing if the digest generation of an ASCII string produces the proper output for each variant of SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testPlainStringHashing()
    {
        $digestCollection = [
            224 => 'd15dadceaa4d5d7bb3b48f446421d542e08ad8887305e28d58335795',
            256 => '69070dda01975c8c120c3aada1b282394e7f032fa9cf32f4cb2259a0897dfc04',
            384 => '7063465e08a93bce31cd89d2e3ca8f602498696e253592ed26f07bf7e703cf328581e1471a7ba7ab119b1a9ebdf8be41',
            512 =>
                '01dedd5de4ef14642445ba5f5b97c15e47b9ad931326e4b0727cd94cefc44fff23f07bf543139939b49128caf436dc1bdee' .
                '54fcb24023a08d9403f9b4bf0d450'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $testingValues = (PHP_INT_SIZE === 8) ? [true, false] : [false];

            foreach ($testingValues as $isX64) {
                $this->sha3UsesX64Algorithm($isX64);

                $digest = NativeSha3::digest224(self::ASCII_STRING);
                $this->assertEquals($digestCollection[224], $digest);

                $digest = NativeSha3::digest224(self::ASCII_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[224]), $digest);

                $digest = NativeSha3::digest256(self::ASCII_STRING);
                $this->assertEquals($digestCollection[256], $digest);

                $digest = NativeSha3::digest256(self::ASCII_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[256]), $digest);

                $digest = NativeSha3::digest384(self::ASCII_STRING);
                $this->assertEquals($digestCollection[384], $digest);

                $digest = NativeSha3::digest384(self::ASCII_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[384]), $digest);

                $digest = NativeSha3::digest512(self::ASCII_STRING);
                $this->assertEquals($digestCollection[512], $digest);

                $digest = NativeSha3::digest512(self::ASCII_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[512]), $digest);
            }

            /**
             * Do code coverage of the 64-bit algorithm for basic errors only (on a 32-bit PHP).
             *
             * {@internal On a 32-bit system, the 64-bit version is not available and will not work correctly. }}
             */
            if (PHP_INT_SIZE === 4) {
                $this->sha3UsesX64Algorithm(true); // switch to x64

                $this->assertNotEmpty(NativeSha3::digest224(self::ASCII_STRING));
                $this->assertNotEmpty(NativeSha3::digest256(self::ASCII_STRING));
                $this->assertNotEmpty(NativeSha3::digest384(self::ASCII_STRING));
                $this->assertNotEmpty(NativeSha3::digest512(self::ASCII_STRING));

                $this->assertNotEmpty(NativeSha3::digest224(self::ASCII_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest256(self::ASCII_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest384(self::ASCII_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest512(self::ASCII_STRING, true));
            }
        }

        // Reset to auto-choosing
        $this->sha3UsesMbString(null);
        $this->sha3UsesX64Algorithm(null);
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output for each variant of SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $digestCollection = [
            224 => '87d6448d1b989b5eddf0be6625a9ba6dd79e0913afaa0385b2c08a88',
            256 => '4528c3e61be52dced06b2856c4859a171e9be1a6046a9cde9017d865cc6dfc83',
            384 => '140b7d9a861e929d99d011dbdabde1bb195d05203cc48326875aa9a74ae31f87571d397ef48dfc8e9dd1eedfdc893ee7',
            512 =>
                'ae25e5cd31ab981bbc7f341b4935018a668d40f0ad6b886e3acf3018ab683b22a8519f6be00ce8b6b05170108f87bb48f4c' .
                '299b32f2ecf1e954fcf4758660431'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $testingValues = (PHP_INT_SIZE === 8) ? [true, false] : [false];

            foreach ($testingValues as $isX64) {
                $this->sha3UsesX64Algorithm($isX64);

                $digest = NativeSha3::digest224(self::UTF_8_STRING);
                $this->assertEquals($digestCollection[224], $digest);

                $digest = NativeSha3::digest224(self::UTF_8_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[224]), $digest);

                $digest = NativeSha3::digest256(self::UTF_8_STRING);
                $this->assertEquals($digestCollection[256], $digest);

                $digest = NativeSha3::digest256(self::UTF_8_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[256]), $digest);

                $digest = NativeSha3::digest384(self::UTF_8_STRING);
                $this->assertEquals($digestCollection[384], $digest);

                $digest = NativeSha3::digest384(self::UTF_8_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[384]), $digest);

                $digest = NativeSha3::digest512(self::UTF_8_STRING);
                $this->assertEquals($digestCollection[512], $digest);

                $digest = NativeSha3::digest512(self::UTF_8_STRING, true);
                $this->assertEquals(hex2bin($digestCollection[512]), $digest);
            }

            /**
             * Do code coverage of the 64-bit algorithm for basic errors only (on a 32-bit PHP).
             *
             * {@internal On a 32-bit system, the 64-bit version is not available and will not work correctly. }}
             */
            if (PHP_INT_SIZE === 4) {
                $this->sha3UsesX64Algorithm(true); // switch to x64

                $this->assertNotEmpty(NativeSha3::digest224(self::UTF_8_STRING));
                $this->assertNotEmpty(NativeSha3::digest256(self::UTF_8_STRING));
                $this->assertNotEmpty(NativeSha3::digest384(self::UTF_8_STRING));
                $this->assertNotEmpty(NativeSha3::digest512(self::UTF_8_STRING));

                $this->assertNotEmpty(NativeSha3::digest224(self::UTF_8_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest256(self::UTF_8_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest384(self::UTF_8_STRING, true));
                $this->assertNotEmpty(NativeSha3::digest512(self::UTF_8_STRING, true));
            }
        }

        // Reset to auto-choosing
        $this->sha3UsesMbString(null);
        $this->sha3UsesX64Algorithm(null);
    }

    /**
     * Testing if the digest generation produces the same output as the `ext-hash` SHA-3 implementation.
     *
     * @throws \Exception If the tested class does not exist or the randomness source is not available.
     */
    public function testRandomStringHashing()
    {
        $supportedAlgorithms = hash_algos();

        $nativeSupport = (
            in_array('sha3-224', $supportedAlgorithms, true) &&
            in_array('sha3-256', $supportedAlgorithms, true) &&
            in_array('sha3-384', $supportedAlgorithms, true) &&
            in_array('sha3-512', $supportedAlgorithms, true)
        );

        $randomData = $this->getRandomData();

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $reflectionWord = new \ReflectionProperty(NativeSha3::class, 'isX64');
            $reflectionWord->setAccessible(true);

            $testingValues = (PHP_INT_SIZE === 8) ? [true, false] : [false];

            foreach ($testingValues as $isX64) {
                $this->sha3UsesX64Algorithm($isX64);

                if ($nativeSupport) {
                    $this->assertEquals(
                        hash('sha3-224', $randomData),
                        NativeSha3::digest224($randomData)
                    );

                    $this->assertEquals(
                        hash('sha3-224', $randomData, true),
                        NativeSha3::digest224($randomData, true)
                    );

                    $this->assertEquals(
                        hash('sha3-256', $randomData),
                        NativeSha3::digest256($randomData)
                    );

                    $this->assertEquals(
                        hash('sha3-256', $randomData, true),
                        NativeSha3::digest256($randomData, true)
                    );

                    $this->assertEquals(
                        hash('sha3-384', $randomData),
                        NativeSha3::digest384($randomData)
                    );

                    $this->assertEquals(
                        hash('sha3-384', $randomData, true),
                        NativeSha3::digest384($randomData, true)
                    );

                    $this->assertEquals(
                        hash('sha3-512', $randomData),
                        NativeSha3::digest512($randomData)
                    );

                    $this->assertEquals(
                        hash('sha3-512', $randomData, true),
                        NativeSha3::digest512($randomData, true)
                    );
                } else {
                    $this->assertEquals(
                        bin2hex(NativeSha3::digest224($randomData, true)),
                        NativeSha3::digest224($randomData)
                    );

                    $this->assertEquals(
                        NativeSha3::digest224($randomData, true),
                        hex2bin(NativeSha3::digest224($randomData))
                    );

                    $this->assertEquals(
                        bin2hex(NativeSha3::digest256($randomData, true)),
                        NativeSha3::digest256($randomData)
                    );

                    $this->assertEquals(
                        NativeSha3::digest256($randomData, true),
                        hex2bin(NativeSha3::digest256($randomData))
                    );

                    $this->assertEquals(
                        bin2hex(NativeSha3::digest384($randomData, true)),
                        NativeSha3::digest384($randomData)
                    );

                    $this->assertEquals(
                        NativeSha3::digest384($randomData, true),
                        hex2bin(NativeSha3::digest384($randomData))
                    );

                    $this->assertEquals(
                        bin2hex(NativeSha3::digest512($randomData, true)),
                        NativeSha3::digest512($randomData)
                    );

                    $this->assertEquals(
                        NativeSha3::digest512($randomData, true),
                        hex2bin(NativeSha3::digest512($randomData))
                    );
                }

                /**
                 * Do code coverage of the 64-bit algorithm for basic errors only (on a 32-bit PHP).
                 *
                 * {@internal On a 32-bit system, the 64-bit version is not available and will not work correctly. }}
                 */
                if (PHP_INT_SIZE === 4) {
                    $this->sha3UsesX64Algorithm(true); // switch to x64

                    $this->assertNotEmpty(NativeSha3::digest224($randomData));
                    $this->assertNotEmpty(NativeSha3::digest256($randomData));
                    $this->assertNotEmpty(NativeSha3::digest384($randomData));
                    $this->assertNotEmpty(NativeSha3::digest512($randomData));

                    $this->assertNotEmpty(NativeSha3::digest224($randomData, true));
                    $this->assertNotEmpty(NativeSha3::digest256($randomData, true));
                    $this->assertNotEmpty(NativeSha3::digest384($randomData, true));
                    $this->assertNotEmpty(NativeSha3::digest512($randomData, true));
                }
            }

            // Reset to auto-choosing
            $this->sha3UsesMbString(null);
            $this->sha3UsesX64Algorithm(null);
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

            NativeSha3::digest256(['wrong']);
        } else {
            $hasThrown = null;

            try {
                NativeSha3::digest256(['wrong']);
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
        $tmp = NativeSha3::getInstance();

        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof NativeSha3);

        $this->assertEquals(NativeSha3::class, (string)$tmp);
        $reflection = new \ReflectionClass(NativeSha3::class);

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
