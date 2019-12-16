<?php

/**
 * Testing the SHA-2 family HMAC-SHA-224 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface;
use \CryptoManana\Hashing\HmacShaTwo224;

/**
 * Class HmacShaTwo224Test - Testing the SHA-2 family HMAC-SHA-224 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HmacShaTwo224Test extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return HmacShaTwo224 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HmacShaTwo224();
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
        $this->assertTrue($hasher instanceof HmacShaTwo224);

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
            'bff30efde99491a63cfc4d04d15fc1ca04d85eed3cf3f2d718736267',
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
        $this->assertEquals($hasher::DIGEST_OUTPUT_RAW, $hasher->getDigestFormat());

        $this->assertEquals(
            hex2bin('2d015ac5c9d652e088a81131172d982ba765ce38d2486899dceedb7c'),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '2D015AC5C9D652E088A81131172D982BA765CE38D2486899DCEEDB7C',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '2d015ac5c9d652e088a81131172d982ba765ce38d2486899dceedb7c',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'LQFaxcnWUuCIqBExFy2YK6dlzjjSSGiZ3O7bfA==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'LQFaxcnWUuCIqBExFy2YK6dlzjjSSGiZ3O7bfA',
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
            'CECB6811E38599DC6795CA7F15E30959B1D1253538A9381093E82B08',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '17F3C6DD13435D16446590D4BE336596D9246904AFF84C3DF76376B1',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '8B723336D88A5AAEF95188332E375EE0DE569A5B9559C4B9045D898F',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '2B62C40195C526F1029D21665DE0103F260F4CB8580644CEAE649CCC',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '304F24782478FC66F45C7502B944B3CE3639B42BFA85BE832E8585F3',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'F66BBC5EFA725CC4C375C484F6F99B575B66F76896D8E9F936878A9D',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'FE0072F2E015610367D218003183B00ABA2F809F8941A82D84603692',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '60FEB9DB275251E13139AD7944C78770D4D14FCACEDFB714B5332B7B',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'A4E01813D829BDBDCAAAD8DCDB0250F10A219A4D7ABB728DA8C96B3F',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '39A22CCFAF92E51E0F7296C8C2810FA6659E1BD76872E75DE290C5A0',
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

        $testCases = [true, false];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                HmacShaTwo224::class,
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
