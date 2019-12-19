<?php

/**
 * Testing the HMAC-Whirlpool realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface;
use \CryptoManana\Core\Interfaces\MessageDigestion\FileHashingInterface;
use \CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface;
use \CryptoManana\Hashing\HmacWhirlpool;

/**
 * Class HmacWhirlpoolTest - Testing the HMAC-Whirlpool class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HmacWhirlpoolTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return HmacWhirlpool Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HmacWhirlpool();
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
        $this->assertTrue($hasher instanceof HmacWhirlpool);

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
                HmacWhirlpool::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                'cb3e278b7e433563376bac8260af3da5a18a2336ac21dce18d04a220142' .
                '255e0b9592d044b57b0c522b531a2455e5e29c71d275ac8a4f85785b26b9ec758f2ac',
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
            hex2bin(
                '47c229f7b8ebdd4d1547aef763cd6194a126263af1283af5a083c0ac' .
                '72532cee3397a87fd2309875b0b4077c1b89bf1fd93334428536871d7c141329916f44fc'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '47C229F7B8EBDD4D1547AEF763CD6194A126263AF1283AF5A083C0AC72' .
            '532CEE3397A87FD2309875B0B4077C1B89BF1FD93334428536871D7C141329916F44FC',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '47c229f7b8ebdd4d1547aef763cd6194a126263af1283af5a083c0ac' .
            '72532cee3397a87fd2309875b0b4077c1b89bf1fd93334428536871d7c141329916f44fc',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'R8Ip97jr3U0VR673Y81hlKEmJjrxKDr1oIPArHJTLO4zl6h/0jCYdbC0B3wbib' .
            '8f2TM0QoU2hx18FBMpkW9E/A==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'R8Ip97jr3U0VR673Y81hlKEmJjrxKDr1oIPArHJTLO4zl6h_0jCYdbC0B3wbib8f' .
            '2TM0QoU2hx18FBMpkW9E_A',
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
            '492993092715E388548705E205FB310C66E6DC5E8472E012A2905936485' .
            'CA6716ED095CA4612FA814844A966DB91D2EC0BBC7358871366AF177D56BFC9C5A38C',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'E0DFB677B099C615DF860F2388B44B03040E84D3A80F479E6BE07EA141CD' .
            'CE9C47526550E5C921CDFCC044C2DFE4EA2475B31B3F4A855D2ED31452CCACBEC1A5',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '06D8D9C129CF1CD3622402985E7E3D2E48922680420546D23F87B41341C9' .
            '64321544F6F27DECF1AB48F7B089347911458F61DA7FD0BAF05C6392E43BE0CAC816',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            'F41C0F9A8BA64F6EA4C7570048764A4DCABD01EB906FA0AEA9868A9B0F3FF' .
            '8A61B1D716AEE0B8DB4B22B319546F69DD3484E60DD1C59FE51E1CDFE4F1A8D820E',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '5F1475E8E8AD110F5B6FC6FD2FC8D0C13C87D8A8334456BCE4CF87EB8F3A71' .
            '716561542CF028CA112D22FC8211DFB1D21D9D2EA52A3343DF65F35E1BA661E1F8',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '248CEFC2626FB3BA22BEE9062E7F88D132484D0A5107FE590D3EAFE7F30651' .
            '27E789ED508C950748AEE49B38DDE15819A6B74A06923671E42FDBBE1CEDD3796C',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'F220A8D886B7BBCEB82DF8B576655B87B894DD8B8FB6ACAB0986648114B95' .
            '39980954E22BB559E99AAC2CBE93F201530CE84CDCFA4AF4E87C7FE5FB4A1145699',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '081C011829D97A0AA6CEE92E6ADBDB03FEC8CC835A2C81417500578F5FAF24D0' .
            'D7DEB3C6B7AA346168E5580E679B6BA1802D50BBF91C2C5665CC03CA74E66481',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '0268B2CFC8EB99EA4786BF40817F05AA2E503331BFDCF83FDD7D4EF3C7AD90F05' .
            'D07B2AE4BA365F18AC023638BA84D38D09342DC7D30D579CFCB6E10AEE80FD4',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '9CE7401A9F7796928390CECA4967A7B1FFBF715480E30419D5DEA43D1687BAF00' .
            '38C2B93CDABB0CDB3302775E772198BE8F67173B9EC962DF6C5F4873F6FF2F3',
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
                HmacWhirlpool::class,
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
}
