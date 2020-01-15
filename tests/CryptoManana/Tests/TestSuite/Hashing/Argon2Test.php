<?php

/**
 * Testing the Argon2 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHardwareResistantDerivation;
use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface;
use \CryptoManana\Hashing\Argon2;

/**
 * Class Argon2Test - Testing the Argon2 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class Argon2Test extends AbstractUnitTest
{
    /**
     * Internal flag to enable or disable the testing of the Argon2 algorithm.
     *
     * Note: `null` => auto-check on next call, `true` => available, `false` => not available.
     *
     * @var null|bool Is the Argon2 algorithm supported.
     */
    protected static $isArgon2Supported = null;

    /**
     * Creates new instances for testing.
     *
     * @return Argon2|null Testing instance or null if not available.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        if (self::$isArgon2Supported === null) {
            self::$isArgon2Supported = in_array(PASSWORD_ARGON2I, password_algos(), true);
        }

        $instance = null;

        if ((self::$isArgon2Supported)) {
            $instance = (new Argon2())
                ->setTimeCost(PASSWORD_ARGON2_DEFAULT_TIME_COST)
                ->setMemoryCost(PASSWORD_ARGON2_DEFAULT_MEMORY_COST)
                ->setThreadsCost(PASSWORD_ARGON2_DEFAULT_THREADS);
        }

        return $instance;
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $tmp = clone $hasher;

            $this->assertEquals($hasher, $tmp);
            $this->assertNotEmpty($tmp->hashData(''));

            unset($tmp);
            $this->assertNotNull($hasher);
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $tmp = serialize($hasher);
            $tmp = unserialize($tmp);

            $this->assertEquals($hasher, $tmp);
            $this->assertNotEmpty($tmp->hashData(''));

            unset($tmp);
            $this->assertNotNull($hasher);
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $this->assertNotEmpty(var_export($hasher, true));

            $reflection = new \ReflectionClass($hasher);
            $method = $reflection->getMethod('__debugInfo');

            $result = $method->invoke($hasher);
            $this->assertNotEmpty($result);
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing if the algorithm never returns an empty output digest.
     *
     * @throws \Exception If the system does not support the algorithm.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER)->setSaltingMode($hasher::SALTING_MODE_PREPEND);

            $hasher->setTimeCost(PASSWORD_ARGON2_DEFAULT_TIME_COST);
            $hasher->setMemoryCost(PASSWORD_ARGON2_DEFAULT_MEMORY_COST);
            $hasher->setThreadsCost(PASSWORD_ARGON2_DEFAULT_THREADS);
            $this->assertEquals(PASSWORD_ARGON2_DEFAULT_TIME_COST, $hasher->getTimeCost());
            $this->assertEquals(PASSWORD_ARGON2_DEFAULT_MEMORY_COST, $hasher->getMemoryCost());
            $this->assertEquals(PASSWORD_ARGON2_DEFAULT_THREADS, $hasher->getThreadsCost());

            $hasher->setAlgorithmVariation($hasher::VERSION_I);
            $this->assertEquals($hasher::VERSION_I, $hasher->getAlgorithmVariation());

            $this->assertTrue($hasher instanceof AbstractHashAlgorithm);
            $this->assertTrue($hasher instanceof AbstractKeyStretchingFunction);
            $this->assertTrue($hasher instanceof AbstractPasswordBasedDerivationFunction);
            $this->assertTrue($hasher instanceof AbstractHardwareResistantDerivation);
            $this->assertTrue($hasher instanceof Argon2);

            $this->assertNotEmpty($hasher->hashData(''));
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing if the generation of a digest twice with the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testCalculatingTheSameDigestTwice()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);

            $randomData = random_bytes(32);

            $this->assertEquals(
                password_verify(
                    $randomData,
                    $hasher->hashData($randomData)
                ),
                password_verify(
                    $randomData,
                    $hasher->hashData($randomData)
                )
            );
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
            $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

            $testData = 'я1Й\`.a$#!x';
            $testCases = [true, false];
            $testSecondVersion = in_array(PASSWORD_ARGON2ID, password_algos(), true);

            foreach ($testCases as $toUse) {
                $reflectionUseProperty = new \ReflectionProperty(
                    Argon2::class,
                    'useNative'
                );

                $reflectionUseProperty->setAccessible(true);
                $reflectionUseProperty->setValue($hasher, $toUse);

                $hasher->setAlgorithmVariation($hasher::VERSION_I);

                $this->assertTrue(
                    password_verify(
                        $testData,
                        $hasher->hashData($testData)
                    )
                );

                $this->assertTrue(
                    password_verify(
                        $testData,
                        '$argon2i$v=19$m=65536,t=4,p=1$VUY5TXJ3dVJNdERsVlhueQ$Ww8lLx' .
                        'cxVXbSzwb17gotLUqZtFOzpjrMv5Uqp9FFBl4'
                    )
                );

                if ($testSecondVersion) {
                    $hasher->setAlgorithmVariation($hasher::VERSION_ID);

                    $this->assertTrue(
                        password_verify(
                            $testData,
                            $hasher->hashData($testData)
                        )
                    );

                    $this->assertTrue(
                        password_verify(
                            $testData,
                            '$argon2id$v=19$m=65536,t=4,p=1$enRNQjFIZTdNeGVZbDZwUg$j' .
                            'tKEi5O3cUKFzi51qt7Oq6Xku0xiC2T58FczT0+fkow'
                        )
                    );
                }
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing algorithm digest generation and output formats.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDigestOutputFormatsForHashingData()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $testCases = [
                $hasher::DIGEST_OUTPUT_RAW,
                $hasher::DIGEST_OUTPUT_HEX_UPPER,
                $hasher::DIGEST_OUTPUT_HEX_LOWER,
                $hasher::DIGEST_OUTPUT_BASE_64,
                $hasher::DIGEST_OUTPUT_BASE_64_URL
            ];

            $data = '12345';

            foreach ($testCases as $outputFormat) {
                $hasher->setDigestFormat($outputFormat);
                $this->assertEquals($outputFormat, $hasher->getDigestFormat());

                $this->assertTrue(
                    $hasher->verifyHash(
                        $data,
                        $hasher->hashData($data)
                    )
                );
            }

            // Test the extra conversion for the password digestion format
            $reflection = new \ReflectionClass($hasher);
            $method = $reflection->getMethod('convertFormattedDigest');
            $method->setAccessible(true);

            $result = $method->invoke($hasher, 'aD-7x_');

            $this->assertEquals(
                base64_decode('aD+7x/=='),
                $result
            );
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing algorithm salting capabilities for digest generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSaltingCapabilitiesForHashingData()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);

            $data = 'test';
            $hasher->setSalt('1234');

            $this->assertEquals('1234', $hasher->getSalt());

            $testCases = [
                $hasher::SALTING_MODE_NONE,  // 'test'
                $hasher::SALTING_MODE_APPEND, // 'test1234'
                $hasher::SALTING_MODE_PREPEND, // '1234test'
                $hasher::SALTING_MODE_INFIX_INPUT, // '1234test4321'
                $hasher::SALTING_MODE_INFIX_SALT, // 'test1234tset'
                $hasher::SALTING_MODE_REVERSE_APPEND, // 'test4321'
                $hasher::SALTING_MODE_REVERSE_PREPEND, // '4321test'
                $hasher::SALTING_MODE_DUPLICATE_SUFFIX, // 'test12344321'
                $hasher::SALTING_MODE_DUPLICATE_PREFIX, // '1234testtset4321'
                $hasher::SALTING_MODE_PALINDROME_MIRRORING // '1234testtset4321'
            ];

            foreach ($testCases as $saltingMode) {
                $hasher->setSaltingMode($saltingMode);
                $this->assertEquals($saltingMode, $hasher->getSaltingMode());

                $this->assertTrue(
                    $hasher->verifyHash(
                        $data,
                        $hasher->hashData($data)
                    )
                );
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing the secure digest verification feature.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSecureDigestVerificationFeature()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            $this->assertTrue($hasher instanceof SecureVerificationInterface);

            $digest = $hasher->hashData('1234');

            $this->assertTrue($hasher->verifyHash('1234', $digest));
            $this->assertFalse($hasher->verifyHash('1235', $digest));
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid type of salt string used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSaltUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setSalt(['none']);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setSalt(['none']);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid salting mode used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSaltingModeUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setSaltingMode(100000);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setSaltingMode(100000);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid output format chosen for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidOutputFormatUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setDigestFormat(100000);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setDigestFormat(100000);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid input data used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInputDataUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->hashData(['wrong']);
            } else {
                $hasThrown = null;

                try {
                    $hasher->hashData(['wrong']);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid time cost used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTimeCostUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setTimeCost(-1);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setTimeCost(-1);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid memory cost used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidMemoryCostUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setMemoryCost(-1);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setMemoryCost(-1);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid threads cost used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidThreadsCostUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setThreadsCost(-1);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setThreadsCost(-1);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid algorithm variation used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAlgorithmVariationUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->setAlgorithmVariation(-1);
            } else {
                $hasThrown = null;

                try {
                    $hasher->setAlgorithmVariation(-1);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid input data for digest verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInputDataForDigestVerification()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->verifyHash(['wrong'], '1234abcd');
            } else {
                $hasThrown = null;

                try {
                    $hasher->verifyHash(['wrong'], '1234abcd');
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }

    /**
     * Testing validation case for invalid digestion string for digest verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDigestionStringForDigestVerification()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        if ($hasher !== null) {
            // Backward compatible for different versions of PHPUnit
            if (method_exists($this, 'expectException')) {
                $this->expectException(\InvalidArgumentException::class);

                $hasher->verifyHash('', ['wrong']);
            } else {
                $hasThrown = null;

                try {
                    $hasher->verifyHash('', ['wrong']);
                } catch (\InvalidArgumentException $exception) {
                    $hasThrown = !empty($exception->getMessage());
                } catch (\Exception $exception) {
                    $hasThrown = $exception->getMessage();
                }

                $this->assertTrue($hasThrown);

                return;
            }
        } else {
            $this->assertNull($hasher);
        }
    }
}
