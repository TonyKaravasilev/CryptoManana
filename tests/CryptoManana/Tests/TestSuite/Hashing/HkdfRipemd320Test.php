<?php

/**
 * Testing the HKDF-RIPEMD-320 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface;
use \CryptoManana\Hashing\HkdfRipemd320;

/**
 * Class HkdfRipemd320Test - Testing the HKDF-RIPEMD-320 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HkdfRipemd320Test extends AbstractUnitTest
{
    /**
     * Default output length for tests.
     */
    const OUTPUT_LENGTH = 40;

    /**
     * Default context/application string for tests.
     */
    const CONTEXT_STRING = 'cryptomanana';

    /**
     * Default derivation salt string for tests.
     */
    const DERIVATION_SALT = 'x$A4Я;';

    /**
     * Creates new instances for testing.
     *
     * @return HkdfRipemd320 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HkdfRipemd320();
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
        $this->assertTrue($hasher instanceof AbstractKeyMaterialDerivationFunction);
        $this->assertTrue($hasher instanceof HkdfRipemd320);

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
                HkdfRipemd320::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                '0322fb7bf79c78e5ddc369d1b5d34b26c6347bd11684c2963f97ae70b675200f0b374aecb9f64037',
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
            hex2bin('2c68daa4cc4af6944b6039ab6cccb2169ed4a5ae1af88a18080cc724ebad58e3dd90502117e1384f'),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '2C68DAA4CC4AF6944B6039AB6CCCB2169ED4A5AE1AF88A18080CC724EBAD58E3DD90502117E1384F',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '2c68daa4cc4af6944b6039ab6cccb2169ed4a5ae1af88a18080cc724ebad58e3dd90502117e1384f',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'LGjapMxK9pRLYDmrbMyyFp7Upa4a+IoYCAzHJOutWOPdkFAhF+E4Tw==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'LGjapMxK9pRLYDmrbMyyFp7Upa4a-IoYCAzHJOutWOPdkFAhF-E4Tw',
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
            ->setContextualString(self::CONTEXT_STRING)
            ->setDerivationSalt(self::DERIVATION_SALT);

        $this->assertEquals('1234', $hasher->getSalt());
        $this->assertEquals(self::OUTPUT_LENGTH, $hasher->getOutputLength());
        $this->assertEquals(self::CONTEXT_STRING, $hasher->getContextualString());
        $this->assertEquals(self::DERIVATION_SALT, $hasher->getDerivationSalt());

        $hasher->setSaltingMode($hasher::SALTING_MODE_NONE); // 'test'
        $this->assertEquals($hasher::SALTING_MODE_NONE, $hasher->getSaltingMode());

        $this->assertEquals(
            '4DC2B27860587F9D53BDB3A6509B9A47FC4BE3A9B7E3E81A8DF167583E97FFB214FF2141DE72D7A2',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'BC9D68FE559FE7B023CA0723AEA6EA4AB02A33591A611C4057C46215416B6A14C292EFAF2CA8C89B',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '5046A10328C1A180DBEF21A1BCD5A9BEACD494EFF4CAE171B95C0AF485C93E2F9610A3D878B6796B',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '3B9E40A796117778BEFB493D6A356E987EF95E32629AAA7C9A5ECE043AAA11581BAA0875A0572524',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '899A80E38A2E7F9F5166256FDC5C406317C6F1FBD54F2EBC6C03D5947B16CB263484C04DECD040C3',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '94B791AE22ACC64C05F6194D4D2365894BB9B3DD33A3B2C4EC84ED6673FA86B9E5CF94B1FAC6A217',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '7728776978CBCC85ADAAEFEEB831CDF9D3844091D17D1A39F0CAEA8330B97892D7A823899B3AD2BD',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'AAEF8BD955789D8D002E031F90B786307A685A94F9125DED447FF116095607C17902F1B79FC71D0E',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '741F5FACC217D26A1AF55F5764FBB8A9B541B454A5B03FE450F7EB49B30DAC93E59D6E045F6153EC',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            'C81F6519060DA45EEA42726DE2C7AA2A7540EFB8BA70BDAF6AA787DBFE0DA9A355D6400A89B26722',
            $hasher->hashData($data)
        );
    }

    /**
     * Testing repetitive hashing.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testRepetitiveHashingFeature()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $this->assertTrue($hasher instanceof RepetitiveHashingInterface);

        $digest = $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW)->hashData('');
        $digest = $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64)->hashData($digest);

        $this->assertEquals($digest, $hasher->repetitiveHashData(''));
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
     * Testing validation case for invalid type of context/application information string used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidDerivationContextInformationUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setContextualString(['none']);
        } else {
            $hasThrown = null;

            try {
                $hasher->setContextualString(['none']);
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
     * Testing validation case for invalid input data used for repetitive hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidInputDataUsedForRepetitiveHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->repetitiveHashData(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $hasher->repetitiveHashData(['wrong']);
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
     * Testing validation case for invalid type or value of the iteration count used for repetitive hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidIterationCountForRepetitiveHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->repetitiveHashData('', -1);
        } else {
            $hasThrown = null;

            try {
                $hasher->repetitiveHashData('', -1);
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
