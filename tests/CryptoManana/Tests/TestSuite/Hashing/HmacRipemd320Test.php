<?php

/**
 * Testing the HMAC-RIPEMD-320 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction;
use CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface;
use CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface;
use CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface;
use CryptoManana\Hashing\HmacRipemd320;

/**
 * Class HmacRipemd320Test - Testing the HMAC-RIPEMD-320 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HmacRipemd320Test extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return HmacRipemd320 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HmacRipemd320();
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

        $hasher->setKey('manana');
        $this->assertEquals('manana', $hasher->getKey());

        $hasher->setKey('')
            ->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER)
            ->setSaltingMode($hasher::SALTING_MODE_PREPEND);

        $this->assertTrue($hasher instanceof AbstractHashAlgorithm);
        $this->assertTrue($hasher instanceof AbstractKeyedHashFunction);
        $this->assertTrue($hasher instanceof HmacRipemd320);

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
        $randomKey = random_bytes(32);

        $this->assertEquals(
            $hasher->setKey($randomKey)->hashData($randomData),
            $hasher->setKey($randomKey)->hashData($randomData)
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
        $hasher->setKey('test')->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);

        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                HmacRipemd320::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                '034d357c0829c3d13b9c5d9f15dd125003fed04419e3e55963f76a2074a9efaa129be4607cb172a7',
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
        $hasher->setKey('test');

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

        $this->assertEquals(
            hex2bin('0a51df0d9f4542082617133e1a92f4ec0e61d3fbb5c02c94e025b9797613642b071ed3c6fee03a16'),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '0A51DF0D9F4542082617133E1A92F4EC0E61D3FBB5C02C94E025B9797613642B071ED3C6FEE03A16',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '0a51df0d9f4542082617133e1a92f4ec0e61d3fbb5c02c94e025b9797613642b071ed3c6fee03a16',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'ClHfDZ9FQggmFxM+GpL07A5h0/u1wCyU4CW5eXYTZCsHHtPG/uA6Fg==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'ClHfDZ9FQggmFxM-GpL07A5h0_u1wCyU4CW5eXYTZCsHHtPG_uA6Fg',
            $hasher->hashData('')
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
        $hasher->setKey('xxx');

        $data = 'test';
        $hasher->setSalt('1234');

        $this->assertEquals('1234', $hasher->getSalt());

        $hasher->setSaltingMode($hasher::SALTING_MODE_NONE); // 'test'
        $this->assertEquals($hasher::SALTING_MODE_NONE, $hasher->getSaltingMode());

        $this->assertEquals(
            'BB0D0706AE32A6051031C41C44D2424F01B13BFCC1E36FABED6A3FAF423C5E52FB622E679B910A0A',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '812BBFA81CF72F7244DBF1E712999EFE2C99E3E219C6D80F9CD4CB1E3F9A5B7FC89B142276D2DDF7',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '505938DF02E570641F5EA361C3D5FEB19F84ED16670E5DD238C930DE1177676D90D120458CFF9019',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '3D021301A76ABA08943672C31E247037A789A66C16B791143DA5835D05D39C001CDA86CCF6E1D986',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '54C326FAB1D1CA221B8DD0C9A621E6E36481C701A70F131A0CDB610ABC7A90360A732E7B0B2A2928',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'B0FE7C430B7F70E2AD710A7326BADD14D4EE3DEF3024029DFC4F1324A8D029DED286CDAD2D058129',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '7561E64E67636C6C4917360E5CE037568DE7241061281840C4EEBCB6F91FC53AE608AD5EA08D3164',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'F42DF62F4F46CBB6F9424C6EF027D07F5CF6368AEC76239300EC2C9DA227A0714D174F5935402BD2',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '670FF6B6C68D231D84DB7A7C0AC672578A007587935BCEAD750A6DF536CD583105A96D04AC597449',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            'D1F86F5B708A69C48B7D9BCDAE7DCA830C326A7DDD3DE9F76D26C40D6E28071B9F4FCCE50181E70A',
            $hasher->hashData($data)
        );
    }

    /**
     * Testing simple object hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testObjectHashingFeature()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();
        $hasher->setKey('xxx');

        $this->assertTrue($hasher instanceof ObjectHashingInterface);

        $object = new \stdClass();
        $object->test = 'test';

        $this->assertEquals(
            $hasher->hashData(serialize($object)),
            $hasher->hashObject($object)
        );
    }

    /**
     * Testing simple file hashing.
     *
     * @throws \Exception|\ReflectionException If the tested class or method does not exist.
     */
    public function testFileHashingFeature()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();
        $hasher->setKey('xxx');

        $this->assertTrue($hasher instanceof FileHashingInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                HmacRipemd320::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $saltingCases = [
                ['', $hasher::SALTING_MODE_NONE], // No salting, use ext-hash
                ['69', $hasher::SALTING_MODE_NONE], // No salting, use ext-hash
                ['', $hasher::SALTING_MODE_REVERSE_PREPEND], // With salting, use ext-hash
                ['123', $hasher::SALTING_MODE_INFIX_SALT], // With salting, use native
                ['zzя', $hasher::SALTING_MODE_APPEND] // With salting, use native
            ];

            foreach ($saltingCases as $saltingCase) {
                list($salt, $saltingMode) = $saltingCase;
                $hasher->setSalt($salt)->setSaltingMode($saltingMode);

                foreach ([$hasher::DIGEST_OUTPUT_RAW, $hasher::DIGEST_OUTPUT_HEX_UPPER] as $formatCode) {
                    $hasher->setDigestFormat($formatCode);

                    $this->assertEquals(
                        $hasher->hashData($this->readFromFile($fileName)),
                        $hasher->hashFile($fileName)
                    );
                }
            }
        }

        $this->deleteTheFile($fileName);
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
     * Testing validation case for invalid key used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidKeyForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->setKey(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $hasher->setKey(['wrong']);
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
     * Testing validation case for invalid type of filename used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->hashFile(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $hasher->hashFile(['wrong']);
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
     * Testing validation case for non-existing filename used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonExistingFileNameUsedForHashing()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $hasher->hashFile('non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $hasher->hashFile('non-existing.tmp');
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
     * Testing validation case for invalid type of input used for hashing objects.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidTypePassedForHashingObjects()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $hasher->hashObject(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $hasher->hashObject(['wrong']);
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
