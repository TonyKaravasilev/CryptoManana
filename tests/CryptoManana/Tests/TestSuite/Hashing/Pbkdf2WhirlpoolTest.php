<?php

/**
 * Testing the PBKDF2-Whirlpool realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractIterativeSlowDerivation;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface;
use CryptoManana\Hashing\Pbkdf2Whirlpool;

/**
 * Class Pbkdf2WhirlpoolTest - Testing the PBKDF2-Whirlpool class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class Pbkdf2WhirlpoolTest extends AbstractUnitTest
{
    /**
     * Default output length for tests.
     */
    const OUTPUT_LENGTH = 64;

    /**
     * Default derivation salt string for tests.
     */
    const DERIVATION_SALT = 'x$A4Я;';

    /**
     * Default derivation iterations to apply for tests.
     */
    const DERIVATION_ITERATION_COUNT = 2;

    /**
     * Creates new instances for testing.
     *
     * @return Pbkdf2Whirlpool Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new Pbkdf2Whirlpool();
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

        $this->assertTrue($hasher instanceof AbstractHashAlgorithm);
        $this->assertTrue($hasher instanceof AbstractKeyStretchingFunction);
        $this->assertTrue($hasher instanceof AbstractPasswordBasedDerivationFunction);
        $this->assertTrue($hasher instanceof AbstractIterativeSlowDerivation);
        $this->assertTrue($hasher instanceof Pbkdf2Whirlpool);

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
        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);

        $randomData = random_bytes(32);

        $this->assertEquals(
            $hasher->hashData($randomData),
            $hasher->hashData($randomData)
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
        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                Pbkdf2Whirlpool::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                'afb9443c8b3e94bc009c170598f2bebbfa41a5a0f5d2963d1804b' .
                '29b98c68910e028022cf742e17fa77c4d769acf5a64b6213f2aee64f3674e8e5ea05239bcaf',
                $hasher->hashData('я1Й\`.a$#!x')
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

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

        $this->assertEquals(
            hex2bin(
                '58eff98cc0d4905103d571cbde20a8e4f408f6f4be7999e3a2479df3cf649fafa0a231c33d4900586' .
                'ede53f33d0c8320c03d5d3d421cf9a4d0a98e03c1dd6d95'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '58EFF98CC0D4905103D571CBDE20A8E4F408F6F4BE7999E3A2479DF3CF649FAFA0A231C33D4900586' .
            'EDE53F33D0C8320C03D5D3D421CF9A4D0A98E03C1DD6D95',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '58eff98cc0d4905103d571cbde20a8e4f408f6f4be7999e3a2479df3cf649fafa0a231c33d4900586' .
            'ede53f33d0c8320c03d5d3d421cf9a4d0a98e03c1dd6d95',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'WO/5jMDUkFED1XHL3iCo5PQI9vS+eZnjoked889kn6+gojHDPUkAWG7eU/M9DIMgwD1dPUIc+aTQqY4Dwd1tlQ==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'WO_5jMDUkFED1XHL3iCo5PQI9vS-eZnjoked889kn6-gojHDPUkAWG7eU_M9DIMgwD1dPUIc-aTQqY4Dwd1tlQ',
            $hasher->hashData('')
        );
    }

    /**
     * Testing with different output lengths per algorithm.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testChangingTheDigestOutputLength()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        /**
         * {@internal The default output size is the same as the internal hash function. }}
         */
        $this->assertEquals(self::OUTPUT_LENGTH, $hasher->getOutputLength());

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertTrue(
            strlen($hasher->hashData('')) === self::OUTPUT_LENGTH
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertTrue(
            strlen($hasher->hashData('')) === self::OUTPUT_LENGTH * 2
        );

        /**
         * {@internal The minimum output size must be tested also. }}
         */
        $minimum = 1;
        $hasher->setOutputLength($minimum);

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertTrue(
            strlen($hasher->hashData('')) === $minimum
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertTrue(
            strlen($hasher->hashData('')) === $minimum * 2
        );

        /**
         * {@internal The supported `$size * 255` makes the test too slow so using `$size * 2` instead. }}
         */
        $doubled = self::OUTPUT_LENGTH * 2;
        $hasher->setOutputLength($doubled);

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertTrue(
            strlen($hasher->hashData('')) === $doubled
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertTrue(
            strlen($hasher->hashData('')) === $doubled * 2
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

        $data = 'test';
        $hasher->setSalt('1234')
            ->setOutputLength(self::OUTPUT_LENGTH)
            ->setDerivationSalt(self::DERIVATION_SALT)
            ->setDerivationIterations(self::DERIVATION_ITERATION_COUNT);

        $this->assertEquals('1234', $hasher->getSalt());
        $this->assertEquals(self::OUTPUT_LENGTH, $hasher->getOutputLength());
        $this->assertEquals(self::DERIVATION_SALT, $hasher->getDerivationSalt());
        $this->assertEquals(self::DERIVATION_ITERATION_COUNT, $hasher->getDerivationIterations());

        $hasher->setSaltingMode($hasher::SALTING_MODE_NONE); // 'test'
        $this->assertEquals($hasher::SALTING_MODE_NONE, $hasher->getSaltingMode());

        $this->assertEquals(
            '993FF7969404B35DFD94B760A9A65AE691B8F1618F4914D69F7C9624AE17919130AE563DF297A6C1F' .
            '6F9747B0A4F38435DAC054A590AEDBB1B70B2F668AEF731',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '90D9FE3C4CA14FCAC5C8E825CC388CE36D11F4BAB2A5B4BB576F05E56C7740E12F5CB493243ADC85A' .
            '443706B59F11D5F9C71855759D63BD8C1A2B9D2E2D5C167',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '743D80D5531CB87BB3CB026980FE7C23D406D4FA0D70E8CB121FB0A6BCF59D87A0782007995C79CC47' .
            '469C57A0423A5DB31F206D68FF96AD94999E0E98D4FFD5',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '3FB0449BE78FC4189D2C16CD9D2E73F110FD6CA7250EB5558F969F24059B6489BE63F9D2F23849DF8' .
            'E6854908611E1EC462330CA1694AB216DD75CCDA52BA9F6',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '8A8CAE0F917194471F13249B8F0C6745FE5998F0CA60823728FF7BACEF3B8E7C7EEE7F2A6A24A35C9' .
            'F5FBF299AA2C32DFF5DD3AC8AEB1D680F40AB8EDE0D97A7',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '9DBE1FEC5DF203321325648C200B7BEAFC81DA177967EF26857C85749D01FA31C62A23FE983C24F10' .
            '8023223373DEF4EB80A3AB17218EB1505529C930A04E1EA',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '7BB2F6F2E5FA23FB1367CCE041A9723E7D43FD24DA4BECBA6740FB13844312DE8C4CEECACC5860BE' .
            '5D567485D40030EA7EC6B7D8FACC7534D8A2D3333391CA35',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'AE191362488C14BFCB11A91DC4882AFB4974CC36710ACF5433F3C3BC59206F0FCCC20B4C1AF63A12' .
            'F809148CBC5EEDCD32DE592518D44E6A1ED75E87D5E6E0E9',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '0D7D0E9EB8E5948EBA0EB08FDCC7F5EBDDB24122C601585F93A342D3B8FF2C90C51F4C7CEA9C2158D' .
            '38AD6C8E365690882DF20E7747CF0D5CABDC3EDBF0C644B',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '9FABD22458CC69CD1A3395B911C4DB4C550C6001C6C930F11A8F2D41AD97E48458E678947C9D5E5FC' .
            '5B471B0C34B5280491AB4A68EE0A2B6FE2CF62E61BC003B',
            $hasher->hashData($data)
        );
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
     * Testing validation case for invalid type of derivation salt string used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDerivationSaltUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setDerivationSalt(['none']);
        } else {
            $hasThrown = null;

            try {
                $hasher->setDerivationSalt(['none']);
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
     * Testing validation case for invalid type of derivation output size used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDerivationOutputDigestSizeUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setOutputLength(-2000);
        } else {
            $hasThrown = null;

            try {
                $hasher->setOutputLength(-2000);
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
     * Testing validation case for invalid type or value of derivation iterations count used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDerivationIterationCountUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setDerivationIterations(['none']);
        } else {
            $hasThrown = null;

            try {
                $hasher->setDerivationIterations(['none']);
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
