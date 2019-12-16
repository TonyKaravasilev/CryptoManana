<?php

/**
 * Testing the SHA-2 family SHA-224 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface;
use \CryptoManana\Hashing\ShaTwo224;

/**
 * Class ShaTwo224Test - Testing the SHA-2 family SHA-224 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class ShaTwo224Test extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return ShaTwo224 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new ShaTwo224();
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

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER)
            ->setSaltingMode($hasher::SALTING_MODE_PREPEND)
            ->setSalt('');

        $this->assertTrue($hasher instanceof AbstractHashAlgorithm);
        $this->assertTrue($hasher instanceof AbstractUnkeyedHashFunction);
        $this->assertTrue($hasher instanceof ShaTwo224);

        $this->assertNotEmpty($hasher->hashData(''));
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

        $this->assertEquals(
            '116e9830fa46bf3661977a83738bb4260f8fb5fd0a1fee644e268b69',
            $hasher->hashData('я1Й\`.a$#!x')
        );
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
     * Testing algorithm digest generation and output formats.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDigestGenerationAndOutputFormatsActions()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);
        $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

        $this->assertEquals(
            hex2bin('d14a028c2a3a2bc9476102bb288234c415a2b01f828ea62ac5b3e42f'),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            'D14A028C2A3A2BC9476102BB288234C415A2B01F828EA62AC5B3E42F',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            'd14a028c2a3a2bc9476102bb288234c415a2b01f828ea62ac5b3e42f',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            '0UoCjCo6K8lHYQK7KII0xBWisB+CjqYqxbPkLw==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            '0UoCjCo6K8lHYQK7KII0xBWisB-CjqYqxbPkLw',
            $hasher->hashData('')
        );
    }

    /**
     * Testing algorithm salting capabilities for digest generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSaltingCapabilitiesForHashingActions()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $data = 'test';
        $hasher->setSalt('1234');

        $this->assertEquals('1234', $hasher->getSalt());

        $hasher->setSaltingMode($hasher::SALTING_MODE_NONE); // 'test'
        $this->assertEquals($hasher::SALTING_MODE_NONE, $hasher->getSaltingMode());

        $this->assertEquals(
            '90A3ED9E32B2AAF4C61C410EB925426119E1A9DC53D4286ADE99A809',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'FE53BE35763BCCD7295B7225ED41F5C508440E8C4CC2A6B52725105A',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '0B03DD6B0FC60D5B2DD5E87B5D33BB41BA4B495DA0384C97A0EBEA68',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '4869515BE5706A0A3403043CBDB55882E76E9BFDAD710E1853CFB7AD',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '57E527BA4FC29A07D14A62E48FBB26DBB61C7CDD8A5D18DC101FF6C2',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '7A9F203CB2A52E62406B605D0B1053D28AB60F1FF3B1D53EA417418C',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '4C3BD699D1F5016AFB27032CFE16184F7FF0942B3ED2F5A1903FBA7D',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '542256236C7212251E6F941D564E292A8A53C5C1444EFBF739CC734F',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'AC9CB645CBA85F61495054D0A64F208F4F10E74EBE49445A4D0751D9',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            'D02742E32D4A271288020B6E7BA737492387B89CBC2708B956F9FA22',
            $hasher->hashData($data)
        );
    }

    /**
     * Testing simple object hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testObjectHashingFeatureActions()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

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
    public function testFileHashingFeatureActions()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();

        $this->assertTrue($hasher instanceof FileHashingInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                ShaTwo224::class,
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
    public function testValidationCaseForInvalidSaltingModeForHashing()
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
    public function testValidationCaseForInvalidOutputFormatForDigests()
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
    public function testValidationCaseForInvalidInputDataForHashing()
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
     * Testing validation case for invalid type of filename used for hashing.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameForHashing()
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
    public function testValidationCaseForNonExistingFileNameForHashing()
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
    public function testValidationCaseForInvalidTypeForHashingObjects()
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
}
