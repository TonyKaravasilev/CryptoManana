<?php

/**
 * Testing the NativeHkdfSha3 component used for compatibility purposes to generate HKDF-SHA-3 digests.
 */

namespace CryptoManana\Tests\TestSuite\Compatibility;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use \CryptoManana\Compatibility\NativeHkdfSha3;
use \CryptoManana\Compatibility\NativeSha3;

/**
 * Class NativeHkdfSha3Test - Tests the pure PHP implementation of the HKDF-SHA-3 algorithm.
 *
 * @package CryptoManana\Tests\TestSuite\Compatibility
 */
final class NativeHkdfSha3Test extends AbstractUnitTest
{
    /**
     * Default output length for tests.
     */
    const OUTPUT_LENGTH = 40;

    /**
     * Default `information` string for tests.
     */
    const APPLICATION_NAME = 'cryptomanana';

    /**
     * The empty string representation for digest generation.
     *
     * @internal This type of algorithm does not except an empty string as input.
     */
    const SPACE_STRING = ' ';

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
            NativeHkdfSha3::class,
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

        $NativeHkdfSha3 = NativeHkdfSha3::getInstance();

        $this->assertNotEmpty(var_export($NativeHkdfSha3, true));
    }

    /**
     * Testing if all variation of the HKDF-SHA-3 algorithm never return an empty output digest.
     *
     * @throws \Exception If the randomness source is not available.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $randomData = $this->getRandomData(false, 2);
        $randomSalt = $this->getRandomData(false, 2);

        $this->assertNotEmpty(
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );

        $this->assertNotEmpty(
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );
        $this->assertNotEmpty(
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
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
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            ),
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            ),
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            ),
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            ),
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            ),
            NativeHkdfSha3::digest224(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            ),
            NativeHkdfSha3::digest256(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            ),
            NativeHkdfSha3::digest384(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );

        $this->assertEquals(
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            ),
            NativeHkdfSha3::digest512(
                $randomData,
                10,
                self::APPLICATION_NAME,
                $randomSalt,
                true
            )
        );
    }

    /**
     * Testing if the digest generation of a space string produces the proper output for each variant of HKDF-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testSpaceStringHashing()
    {
        $digestCollection = [
            224 => 'a4ef0e7f62a68f618d201350eb9ac8367ba57080',
            256 => 'a6a865f98bd0786aee9ed8d1da310800fe44f506',
            384 => 'ffc7a2d0bcadcdc53391c6426f4c990c147990e5',
            512 => '4771fc504ff5d38e7c32c724de96ef00576a0f71'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHkdfSha3::digest224(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHkdfSha3::digest224(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest256(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHkdfSha3::digest256(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest384(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHkdfSha3::digest384(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest512(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHkdfSha3::digest512(
                self::SPACE_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::SPACE_STRING,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of a `NULL` byte produces the proper output for each variant of HKDF-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testNullByteHashing()
    {
        $digestCollection = [
            224 => '849c06494db1012926f6f70c82d667ac1702ff9f',
            256 => '749b09cf1804d6425cc7c9361df78ffd2c9058b8',
            384 => '18936d3317d6ab6e5391868333fcc0b78432b68d',
            512 => '4696604916dd9cb65066f0d5c24bbb29c1d0df98'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHkdfSha3::digest224(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHkdfSha3::digest224(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest256(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHkdfSha3::digest256(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest384(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHkdfSha3::digest384(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest512(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHkdfSha3::digest512(
                self::THE_NUL_BYTE,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::THE_NUL_BYTE,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of an ASCII string produces the proper output for each variant of HKDF-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testPlainStringHashing()
    {
        $digestCollection = [
            224 => 'a5c489267ac0826aca54a780ef8ee273f2e901fd',
            256 => 'cb32962ab8267b3af55a317e3e2787271ab99933',
            384 => '4c585008cd7e390e81349b841e0861cff1f1fa25',
            512 => '21cd1e9dd25c566f25a66170efccca15c5273445'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHkdfSha3::digest224(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHkdfSha3::digest224(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest256(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHkdfSha3::digest256(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest384(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHkdfSha3::digest384(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest512(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHkdfSha3::digest512(
                self::ASCII_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::ASCII_STRING,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output for each variant of HKDF-SHA-3.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $digestCollection = [
            224 => '0acb24d6a0f84bc866b1fd67f5ece2c47998fd5a',
            256 => 'a427165b10f396771ff0bd4c7d735fe488f30037',
            384 => 'b41a18b98312e28a89a6d074a213d3f7cafa52fd',
            512 => 'cb3a9a485b9af36468ae3f1027bccdd21c970d39'
        ];

        foreach ([true, false] as $useMbString) {
            $this->sha3UsesMbString($useMbString);

            $digest = NativeHkdfSha3::digest224(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING
            );
            $this->assertEquals($digestCollection[224], $digest);

            $digest = NativeHkdfSha3::digest224(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING,
                true
            );
            $this->assertEquals($digestCollection[224], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest256(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING
            );
            $this->assertEquals($digestCollection[256], $digest);

            $digest = NativeHkdfSha3::digest256(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING,
                true
            );
            $this->assertEquals($digestCollection[256], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest384(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING
            );
            $this->assertEquals($digestCollection[384], $digest);

            $digest = NativeHkdfSha3::digest384(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING,
                true
            );
            $this->assertEquals($digestCollection[384], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));

            $digest = NativeHkdfSha3::digest512(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING
            );
            $this->assertEquals($digestCollection[512], $digest);

            $digest = NativeHkdfSha3::digest512(
                self::UTF_8_STRING,
                self::OUTPUT_LENGTH,
                self::APPLICATION_NAME,
                self::UTF_8_STRING,
                true
            );
            $this->assertEquals($digestCollection[512], substr(bin2hex($digest), 0, self::OUTPUT_LENGTH));
        }
    }

    /**
     * Testing if the digest generation produces the same output as the `ext-hash` HKDF-SHA-3 implementation.
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
                    substr(
                        bin2hex(
                            hash_hkdf(
                                'sha3-224',
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest224(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hash_hkdf('sha3-224', $randomData, self::OUTPUT_LENGTH, self::APPLICATION_NAME, $randomSalt),
                    NativeHkdfSha3::digest224(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt,
                        true
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            hash_hkdf(
                                'sha3-256',
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest256(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hash_hkdf('sha3-256', $randomData, self::OUTPUT_LENGTH, self::APPLICATION_NAME, $randomSalt),
                    NativeHkdfSha3::digest256(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt,
                        true
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            hash_hkdf(
                                'sha3-384',
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest384(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hash_hkdf('sha3-384', $randomData, self::OUTPUT_LENGTH, self::APPLICATION_NAME, $randomSalt),
                    NativeHkdfSha3::digest384(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt,
                        true
                    )
                );

                $this->assertEquals(
                    substr(
                        bin2hex(
                            hash_hkdf(
                                'sha3-512',
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest512(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hash_hkdf('sha3-512', $randomData, self::OUTPUT_LENGTH, self::APPLICATION_NAME, $randomSalt),
                    NativeHkdfSha3::digest512(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt,
                        true
                    )
                );
            } else {
                $this->assertEquals(
                    substr(
                        bin2hex(
                            NativeHkdfSha3::digest224(
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest224(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativeHkdfSha3::digest224(
                            $randomData,
                            self::OUTPUT_LENGTH,
                            self::APPLICATION_NAME,
                            $randomSalt
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativeHkdfSha3::digest224(
                                    $randomData,
                                    self::OUTPUT_LENGTH,
                                    self::APPLICATION_NAME,
                                    $randomSalt,
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
                            NativeHkdfSha3::digest256(
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest256(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativeHkdfSha3::digest256(
                            $randomData,
                            self::OUTPUT_LENGTH,
                            self::APPLICATION_NAME,
                            $randomSalt
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativeHkdfSha3::digest256(
                                    $randomData,
                                    self::OUTPUT_LENGTH,
                                    self::APPLICATION_NAME,
                                    $randomSalt,
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
                            NativeHkdfSha3::digest384(
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest384(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativeHkdfSha3::digest384(
                            $randomData,
                            self::OUTPUT_LENGTH,
                            self::APPLICATION_NAME,
                            $randomSalt
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativeHkdfSha3::digest384(
                                    $randomData,
                                    self::OUTPUT_LENGTH,
                                    self::APPLICATION_NAME,
                                    $randomSalt,
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
                            NativeHkdfSha3::digest512(
                                $randomData,
                                self::OUTPUT_LENGTH,
                                self::APPLICATION_NAME,
                                $randomSalt,
                                true
                            )
                        ),
                        0,
                        self::OUTPUT_LENGTH
                    ),
                    NativeHkdfSha3::digest512(
                        $randomData,
                        self::OUTPUT_LENGTH,
                        self::APPLICATION_NAME,
                        $randomSalt
                    )
                );

                $this->assertEquals(
                    hex2bin(
                        NativeHkdfSha3::digest512(
                            $randomData,
                            self::OUTPUT_LENGTH,
                            self::APPLICATION_NAME,
                            $randomSalt
                        )
                    ),
                    hex2bin(
                        substr(
                            bin2hex(
                                NativeHkdfSha3::digest512(
                                    $randomData,
                                    self::OUTPUT_LENGTH,
                                    self::APPLICATION_NAME,
                                    $randomSalt,
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
            /**
             * {@internal Passing zero as a value will use the default output size. }}
             */
            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', 0, '', '', true)) === $size
            );

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', 0, '', '', false)) === $size
            );

            /**
             * {@internal Use the same output size as the internal hash function. }}
             */
            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $size, '', '', true)) === $size
            );

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $size, '', '', false)) === $size
            );

            /**
             * {@internal The minimum output size must be tested also. }}
             */
            $min = 1;

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $min, '', '', true)) === $min
            );

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $min, '', '', false)) === $min
            );

            /**
             * {@internal The supports `$size * 255` makes the test too slow so using `$size * 2` instead. }}
             */
            $doubled = $size * 2;

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $doubled, '', '', true)) === $doubled
            );

            $this->assertTrue(
                strlen(NativeHkdfSha3::{$method}('1', $doubled, '', '', false)) === $doubled
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
        $reflection = new \ReflectionClass(NativeHkdfSha3::class);
        $method = $reflection->getMethod('customHkdf');

        $method->setAccessible(true);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $method->invoke(NativeHkdfSha3::getInstance(), 'wrong one', '1', 0, '', '', false);
        } else {
            $hasThrown = null;

            try {
                $method->invoke(NativeHkdfSha3::getInstance(), 'wrong one', '1', 0, '', '', false);
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

            NativeHkdfSha3::digest256('test', 0, '', ['wrong']);
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('test', 0, '', ['wrong']);
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
     * Testing validation case for non string type application information.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInformation()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256('test', 0, ['wrong'], '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('test', 0, ['wrong'], '');
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
     * Testing validation case for non string type input key material (IKM).
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonStringInputKeyMaterial()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256(['wrong'], 0, '', '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256(['wrong'], 0, '', '');
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
     * Testing validation case for empty string as input key material (IKM).
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForEmptyInputKeyMaterial()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256('' /* wrong */, 0, '', '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('' /* wrong */, 0, '', '');
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
     * Testing validation case for non integer type output length.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonIntegerOutputLength()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256('test', ['wrong'], '', '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('test', ['wrong'], '', '');
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
     * Testing validation case for negative output length.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNegativeOutputLength()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256('test', -1 /* wrong */, '', '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('test', -1 /* wrong */, '', '');
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
     * Testing validation case for huge output length.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForHugeOutputLength()
    {
        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            NativeHkdfSha3::digest256('test', 10000 /* wrong */, '', '');
        } else {
            $hasThrown = null;

            try {
                NativeHkdfSha3::digest256('test', 10000 /* wrong */, '', '');
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
        $tmp = NativeHkdfSha3::getInstance();

        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof NativeHkdfSha3);

        $this->assertEquals(NativeHkdfSha3::class, (string)$tmp);
        $reflection = new \ReflectionClass(NativeHkdfSha3::class);

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
