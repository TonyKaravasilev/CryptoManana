<?php

/**
 * Testing the NativePbkdf2Sha3 component used for compatibility purposes to generate PBKDF2-SHA-3 digests.
 */

namespace CryptoManana\Tests\TestSuite\Compatibility;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use \CryptoManana\Compatibility\NativePbkdf2Sha3;
use \CryptoManana\Compatibility\NativeSha3;

/**
 * Class NativePbkdf2Sha3Test - Tests the pure PHP implementation of the PBKDF2-SHA-3 algorithm.
 *
 * @package CryptoManana\Tests\TestSuite\Compatibility
 */
final class NativePbkdf2Sha3Test extends AbstractUnitTest
{
    /**
     * Default output length for tests.
     */
    const OUTPUT_LENGTH = 40;

    /**
     * Default iteration count for tests.
     */
    const ITERATION_COUNT = 2;

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
            NativePbkdf2Sha3::class,
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

        $NativePbkdf2Sha3 = NativePbkdf2Sha3::getInstance();

        $this->assertNotEmpty(var_export($NativePbkdf2Sha3, true));
    }

    /**
     * Testing if all variation of the PBKDF2-SHA-3 algorithm never return an empty output digest.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $randomData = $this->getRandomData(false, 2);
        $randomSalt = $this->getRandomData(false, 2);

        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );

        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );
        $this->assertNotEmpty(
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );
    }

    /**
     * Testing if the generation of a digest twice with the same input returns the same result.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testCalculatingTheSameDigestTwice()
    {
        $randomData = $this->getRandomData(false, 2);
        $randomSalt = $this->getRandomData(false, 2);

        $this->assertEquals(
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10
            ),
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10
            ),
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10
            ),
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10
            ),
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            ),
            NativePbkdf2Sha3::digest224(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            ),
            NativePbkdf2Sha3::digest256(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            ),
            NativePbkdf2Sha3::digest384(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );

        $this->assertEquals(
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            ),
            NativePbkdf2Sha3::digest512(
                $randomData,
                $randomSalt,
                1,
                10,
                true
            )
        );
    }

    /**
     * Testing if the digest generation of an empty string produces the proper output for each variant of PBKDF2-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testEmptyStringHashing()
    {
        $digestCollection = [
            224 => '42ec0e2b3f2f8e0745b2030d049802aa509f6273',
            256 => '06a6d2e902405496e33efb7d6487701c00a53172',
            384 => '3088e2f4f7cc3398950fe5679c4da9c81452f38f',
            512 => 'e0300b096b9eecc5ad2bd231a5df623c6be485f6'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativePbkdf2Sha3::digest224(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativePbkdf2Sha3::digest224(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest256(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativePbkdf2Sha3::digest256(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest384(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativePbkdf2Sha3::digest384(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest512(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativePbkdf2Sha3::digest512(
                self::EMPTY_STRING,
                self::EMPTY_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of a `NULL` byte produces the proper output for each variant of PBKDF2-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testNullByteHashing()
    {
        $digestCollection = [
            224 => 'fa1dcf3f54442322757699a63a13e0d6f3832496',
            256 => '9882cb47b5fe35f256be84af9c9237fcd4e278f4',
            384 => '816a6d5d416fe98f138fab7e84db3448cd7f2e54',
            512 => '982d0a1efd37fb7017e18007e5e1dd5fb162bbcd'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativePbkdf2Sha3::digest224(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativePbkdf2Sha3::digest224(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest256(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativePbkdf2Sha3::digest256(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest384(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativePbkdf2Sha3::digest384(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest512(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativePbkdf2Sha3::digest512(
                self::THE_NUL_BYTE,
                self::THE_NUL_BYTE,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of an ASCII string produces the proper output for each variant of PBKDF2-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testPlainStringHashing()
    {
        $digestCollection = [
            224 => '6ac2f73a2cc6eb722f69751eafebbc7c3058b4b3',
            256 => '53d32c3ab9b9669b6f1d59c14b78c7986f705af9',
            384 => '2da2deda7733b440c3e1ddd0c0a8ea0154bdbfff',
            512 => '25beaf15590af519386b779bcb9fef2ee1e1682e'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativePbkdf2Sha3::digest224(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativePbkdf2Sha3::digest224(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest256(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativePbkdf2Sha3::digest256(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest384(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativePbkdf2Sha3::digest384(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest512(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativePbkdf2Sha3::digest512(
                self::ASCII_STRING,
                self::ASCII_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output for each variant of PBKDF2-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $digestCollection = [
            224 => 'ce271348549d3aee5256f50d86e9443b3fee8ab0',
            256 => '59a9a3252a0b39729f7e1a761a5b82abe41baa94',
            384 => '1ac7a68ec9de87c2e62a56ff07b8fee947154c85',
            512 => '76a66af19e39a8c28a7039706760ba906764e961'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativePbkdf2Sha3::digest224(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativePbkdf2Sha3::digest224(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest256(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativePbkdf2Sha3::digest256(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest384(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativePbkdf2Sha3::digest384(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativePbkdf2Sha3::digest512(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativePbkdf2Sha3::digest512(
                self::UTF_8_STRING,
                self::UTF_8_STRING,
                self::ITERATION_COUNT,
                self::OUTPUT_LENGTH,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation produces the same output as the `ext-hash` PBKDF2-SHA-3 implementation.
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
        $randomSalt = $this->getRandomData();

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            if ($nativeSupport) {
                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-224',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest224(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-224',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    ),
                    NativePbkdf2Sha3::digest224(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-256',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest256(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-256',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    ),
                    NativePbkdf2Sha3::digest256(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-384',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest384(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-384',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    ),
                    NativePbkdf2Sha3::digest384(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-512',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest512(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hash_pbkdf2(
                        'sha3-512',
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    ),
                    NativePbkdf2Sha3::digest512(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH,
                        true
                    )
                );
            } else {
                $this->assertEquals(
                    substr(
                        bin2hex(
                            NativePbkdf2Sha3::digest224(
                                $randomData,
                                $randomSalt,
                                self::ITERATION_COUNT,
                                self::OUTPUT_LENGTH,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest224(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativePbkdf2Sha3::digest224(
                            $randomData,
                            $randomSalt,
                            self::ITERATION_COUNT,
                            self::OUTPUT_LENGTH
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativePbkdf2Sha3::digest224(
                                    $randomData,
                                    $randomSalt,
                                    self::ITERATION_COUNT,
                                    self::OUTPUT_LENGTH,
                                    true
                                )
                            ),
                            0,
                            self::OUTPUT_LENGTH
                        )
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            NativePbkdf2Sha3::digest256(
                                $randomData,
                                $randomSalt,
                                self::ITERATION_COUNT,
                                self::OUTPUT_LENGTH,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest256(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativePbkdf2Sha3::digest256(
                            $randomData,
                            $randomSalt,
                            self::ITERATION_COUNT,
                            self::OUTPUT_LENGTH
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativePbkdf2Sha3::digest256(
                                    $randomData,
                                    $randomSalt,
                                    self::ITERATION_COUNT,
                                    self::OUTPUT_LENGTH,
                                    true
                                )
                            ),
                            0,
                            self::OUTPUT_LENGTH
                        )
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            NativePbkdf2Sha3::digest384(
                                $randomData,
                                $randomSalt,
                                self::ITERATION_COUNT,
                                self::OUTPUT_LENGTH,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest384(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativePbkdf2Sha3::digest384(
                            $randomData,
                            $randomSalt,
                            self::ITERATION_COUNT,
                            self::OUTPUT_LENGTH
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativePbkdf2Sha3::digest384(
                                    $randomData,
                                    $randomSalt,
                                    self::ITERATION_COUNT,
                                    self::OUTPUT_LENGTH,
                                    true
                                )
                            ),
                            0,
                            self::OUTPUT_LENGTH
                        )
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            NativePbkdf2Sha3::digest512(
                                $randomData,
                                $randomSalt,
                                self::ITERATION_COUNT,
                                self::OUTPUT_LENGTH,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativePbkdf2Sha3::digest512(
                        $randomData,
                        $randomSalt,
                        self::ITERATION_COUNT,
                        self::OUTPUT_LENGTH
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativePbkdf2Sha3::digest512(
                            $randomData,
                            $randomSalt,
                            self::ITERATION_COUNT,
                            self::OUTPUT_LENGTH
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativePbkdf2Sha3::digest512(
                                    $randomData,
                                    $randomSalt,
                                    self::ITERATION_COUNT,
                                    self::OUTPUT_LENGTH,
                                    true
                                )
                            ),
                            0,
                            self::OUTPUT_LENGTH
                        )
                    )
                );
            }
        }
    }

    /**
     * Testing with different output lengths per algorithm.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testCustomOutputLengthsAreWorkingAsExpected()
    {
        $algorithms = [
            'digest224' => 28,
            'digest256' => 32,
            'digest384' => 48,
            'digest512' => 64,
        ];

        foreach ($algorithms as $method => $size) {
            $iterations = random_int(1, self::ITERATION_COUNT);

            /**
             * {@internal Passing zero as a value will use the default output size. }}
             */
            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, 0, true)) === $size
            );

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, 0, false)) === $size
            );

            /**
             * {@internal Use the same output size as the internal hash function. }}
             */
            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $size, true)) === $size
            );

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $size, false)) === $size
            );

            /**
             * {@internal The minimum output size must be tested also. }}
             */
            $min = 1;

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $min, true)) === $min
            );

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $min, false)) === $min
            );

            /**
             * {@internal The supports `PHP_INT_MAX` makes the test too slow so using `$size * 2` instead. }}
             */
            $doubled = $size * 2;

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $doubled, true)) === $doubled
            );

            $this->assertTrue(
                strlen(NativePbkdf2Sha3::{$method}('1', '2', $iterations, $doubled, false)) === $doubled
            );
        }
    }

    /**
     * Testing validation case for wrong algorithm choosing on internal calls.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForWrongInternalUsage()
    {
        $reflection = new \ReflectionClass(NativePbkdf2Sha3::class);
        $method = $reflection->getMethod('customPbkdf2');

        $method->setAccessible(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $method->invoke(NativePbkdf2Sha3::getInstance(), 'wrong one', '1', '2', 3, 4, false);
        } else {
            $hasThrown = null;

            try {
                $method->invoke(NativePbkdf2Sha3::getInstance(), 'wrong one', '1', '2', 3, 4, false);
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
     * Testing validation case for non string type salt string.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringSalt()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativePbkdf2Sha3::digest256('1', ['wrong'], 1, 4);
        } else {
            $hasThrown = null;

            try {
                NativePbkdf2Sha3::digest256('1', ['wrong'], 1, 4);
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
     * Testing validation case for non string type input password.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInputPassword()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativePbkdf2Sha3::digest256(['wrong'], '1', 1, 4);
        } else {
            $hasThrown = null;

            try {
                NativePbkdf2Sha3::digest256(['wrong'], '1', 1, 4);
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
     * Testing validation case for negative or zero iteration count.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNegativeOrZeroIterations()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativePbkdf2Sha3::digest256('1', '2', -1000 /* wrong */, 4);
        } else {
            $hasThrown = null;

            try {
                NativePbkdf2Sha3::digest256('1', '2', -1000 /* wrong */, 4);
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
     * Testing validation case for negative output key length.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNegativeOutputKeyLength()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativePbkdf2Sha3::digest256('1', '2', 1, -1000 /* wrong */);
        } else {
            $hasThrown = null;

            try {
                NativePbkdf2Sha3::digest256('1', '2', 1, -1000 /* wrong */);
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
        $tmp = NativePbkdf2Sha3::getInstance();

        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof NativePbkdf2Sha3);

        $this->assertEquals(NativePbkdf2Sha3::class, (string)$tmp);
        $reflection = new \ReflectionClass(NativePbkdf2Sha3::class);

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
