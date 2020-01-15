<?php

/**
 * Testing the Bcrypt realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHardwareResistantDerivation;
use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface;
use \CryptoManana\Hashing\Bcrypt;

/**
 * Class BcryptTest - Testing the Bcrypt class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class BcryptTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return Bcrypt Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return (new Bcrypt())->setAlgorithmicCost(Bcrypt::MINIMUM_ALGORITHMIC_COST);
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $tmp = clone $hasher;

        $this->assertEquals($hasher, $tmp);
        $this->assertNotEmpty($tmp->hashData(''));

        unset($tmp);
        $this->assertNotNull($hasher);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $tmp = serialize($hasher);
        $tmp = unserialize($tmp);

        $this->assertEquals($hasher, $tmp);
        $this->assertNotEmpty($tmp->hashData(''));

        unset($tmp);
        $this->assertNotNull($hasher);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $this->assertNotEmpty(var_export($hasher, true));

        $reflection = new \ReflectionClass($hasher);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($hasher);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing if the algorithm never returns an empty output digest.
     *
     * @throws \Exception If the system does not support the algorithm.
     */
    public function testTheOutputDigestIsNeverEmpty()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER)->setSaltingMode($hasher::SALTING_MODE_PREPEND);

        $hasher->setAlgorithmicCost(PASSWORD_BCRYPT_DEFAULT_COST);
        $this->assertEquals(PASSWORD_BCRYPT_DEFAULT_COST, $hasher->getAlgorithmicCost());

        $this->assertTrue($hasher instanceof AbstractHashAlgorithm);
        $this->assertTrue($hasher instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($hasher instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($hasher instanceof AbstractHardwareResistantDerivation);
        $this->assertTrue($hasher instanceof Bcrypt);

        $this->assertNotEmpty($hasher->hashData(''));
    }

    /**
     * Testing if the generation of a digest twice with the same input returns the same result.
     *
     * @throws \Exception If system does not support the algorithm or the randomness source is not available.
     */
    public function testCalculatingTheSameDigestTwice()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();
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
    }

    /**
     * Testing if the digest generation of an UTF-8 string produces the proper output.
     *
     * @throws \Exception If the tested class or property does not exist.
     */
    public function testUnicodeStringHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();
        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

        $testData = 'я1Й\`.a$#!x';
        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                Bcrypt::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertTrue(
                password_verify(
                    $testData,
                    $hasher->hashData($testData)
                )
            );

            $this->assertTrue(
                password_verify(
                    $testData,
                    '$2y$04$o4PnMjJP7VWFngOME2R31OLDZDc0n5a/DoPR/iPDhuGHpQ/UpZt7i'
                )
            );
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
    }

    /**
     * Testing algorithm salting capabilities for digest generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSaltingCapabilitiesForHashingData()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing the secure digest verification feature.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSecureDigestVerificationFeature()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $this->assertTrue($hasher instanceof SecureVerificationInterface);

        $digest = $hasher->hashData('1234');

        $this->assertTrue($hasher->verifyHash('1234', $digest));
        $this->assertFalse($hasher->verifyHash('1235', $digest));
    }

    /**
     * Testing validation case for invalid type of salt string used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSaltUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing validation case for invalid salting mode used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidSaltingModeUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing validation case for invalid output format chosen for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidOutputFormatUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing validation case for invalid input data used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInputDataUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing validation case for invalid algorithmic cost used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidAlgorithmicCostUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setAlgorithmicCost(-1);
        } else {
            $hasThrown = null;

            try {
                $hasher->setAlgorithmicCost(-1);
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
     * Testing validation case for invalid input data for digest verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInputDataForDigestVerification()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }

    /**
     * Testing validation case for invalid digestion string for digest verification.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDigestionStringForDigestVerification()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    }
}
