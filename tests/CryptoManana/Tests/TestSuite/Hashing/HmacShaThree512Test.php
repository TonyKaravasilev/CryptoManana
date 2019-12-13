<?php

/**
 * Testing the SHA-3 family HMAC-SHA-512 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface;
use \CryptoManana\Hashing\HmacShaThree512;

/**
 * Class HmacShaThree512Test - Testing the SHA-3 family HMAC-SHA-512 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HmacShaThree512Test extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return HmacShaThree512 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HmacShaThree512();
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
        $this->assertTrue($hasher instanceof HmacShaThree512);

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

        $this->assertEquals(
            '64593ea5488eb7a8581f8570678f6088ab9bf8ab220bc19c6da780e3ab6cb477ce0' .
            '8276235836185d7d86ffdde9b0f760af709c99a7e4274d35c594ece639eb0',
            $hasher->hashData('я1Й\`.a$#!x')
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
        $hasher->setKey('test');

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_RAW);

        $this->assertEquals(
            hex2bin(
                'e8b82f5f0f82c8af5c79c81c9b7b5a702c687f826b420cfecc43bf2' .
                'ea41e7b763f7c21a55da89e21e4234b819eb01e844a7b9e9e329e31b1cf457d5b415ca688'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);

        $this->assertEquals(
            'E8B82F5F0F82C8AF5C79C81C9B7B5A702C687F826B420CFECC43BF2EA41E7B763F7C21' .
            'A55DA89E21E4234B819EB01E844A7B9E9E329E31B1CF457D5B415CA688',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);

        $this->assertEquals(
            'e8b82f5f0f82c8af5c79c81c9b7b5a702c687f826b420cfecc43bf2ea41e7b763f7c21' .
            'a55da89e21e4234b819eb01e844a7b9e9e329e31b1cf457d5b415ca688',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);

        $this->assertEquals(
            '6LgvXw+CyK9cecgcm3tacCxof4JrQgz+zEO/LqQee3Y/fCGlXaieIeQjS4GesB6ESnuenjKeMbHPRX1bQVymiA==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);

        $this->assertEquals(
            '6LgvXw-CyK9cecgcm3tacCxof4JrQgz-zEO_LqQee3Y_fCGlXaieIeQjS4GesB6ESnuenjKeMbHPRX1bQVymiA',
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
        $hasher->setKey('xxx');

        $data = 'test';
        $hasher->setSalt('1234');

        $this->assertEquals('1234', $hasher->getSalt());

        $hasher->setSaltingMode($hasher::SALTING_MODE_NONE); // 'test'
        $this->assertEquals($hasher::SALTING_MODE_NONE, $hasher->getSaltingMode());

        $this->assertEquals(
            '2B085133E9639A3B58CD0AD01C89C676C88B9A940E62B0A5F456DF' .
            'A5CC7CBDEED1FE67C7FF96FCF2F7F707E633D62E89A4D600B4950C563E7E0DF54EC91D565B',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'E120C400E2B69041AD63B6680CBC14F0BEE4525771B5D0725BF94B8' .
            'F8C73649D158E3C9EEBF5604F715E53D9841577E6FA913F8E5C0AE7AF264240EF697A79CB',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '45A5C9DBA20ED6C1DB7DD34B6D9A844D86FDEA43F7041609D6ED85C66' .
            'F478CEC507746A1877C367CEC9C2E01369152D56831C6C73AD718BB68ECC33E42EE2E22',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '2AA5A44A9ED51AF0445EC5974FB68E0A1FB62359BF385DC8274D99897' .
            '6F46BAC0AC76D0EAF11004861E4DFB03597C99A69EC1D9FE45734988725F7719D32C931',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '519B5927524ADB4371C6C58E39FCFB510D2EC870CFE5E4C667012A2EE6' .
            '2C5466E536FC2783F4B5A1B4AC1036CD41827A5BE7374765A4356A0E8E8AB3BB1606AE',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'D13F9B34CAFF2F5D7342D6833B7675D83F4A963CD3C19275FE32352B8C' .
            '18464FC9E1B1BA92B8085FB6C287A3FC63FED23044B55B28C0B7B29CC885042BE935E4',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '2326D747DC26AA5F263FA033B25339868E741BC01BC71300C8921D7B6D' .
            '5569E95E9C577D469CC2E2D7306925B499A4E05652BD5384F86EE0EA9BD70D21C6BE13',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '9F862E8931F595F52C0BF651221D32112BEB7F0E88407DE1E6D31BE1C092' .
            'D5748273DC9C1E59F7982211E07A4D72A6971FA9C179C83A556F263025FB34EDC9C1',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'ECEF02212B98B3D4CCD6670CDBFD02B96F31ADCA8C65570A4D4A97F019' .
            '37AD6F79CCBD099BA1AE82C657E4AC28EBDC234B5F99D0A704A24FCEF296C544F8CF98',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            'BE87FF4555A221F61C58E07B676A5CDABF1ABFD89197C25C76DDC9189' .
            '8411F66DA018613F255E0D488CFF1198AE35DD9A5256EF6DAC95095A5581E186395ACE3',
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
    public function testFileHashingFeatureActions()
    {
        $hasher = $this->getHashAlgorithmInstanceForTesting();
        $hasher->setKey('xxx');

        $this->assertTrue($hasher instanceof FileHashingInterface);

        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $testCases = in_array('sha3-512', hash_algos(), true) ? [true, false] : [true];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                HmacShaThree512::class,
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
