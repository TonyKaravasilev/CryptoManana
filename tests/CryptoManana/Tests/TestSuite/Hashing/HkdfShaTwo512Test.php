<?php

/**
 * Testing the SHA-2 family HKDF-SHA-512 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use \CryptoManana\Hashing\HkdfShaTwo512;

/**
 * Class HkdfShaTwo512Test - Testing the SHA-2 family HKDF-SHA-512 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HkdfShaTwo512Test extends AbstractUnitTest
{
    /**
     * Default output length for tests.
     */
    const OUTPUT_LENGTH = 64;

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
     * @return HkdfShaTwo512 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HkdfShaTwo512();
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
        $this->assertTrue($hasher instanceof HkdfShaTwo512);

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
                HkdfShaTwo512::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                '310939e1da8df2ae5e44eadcc55bbd4da40f42a9946aaeb719fea1f2d4eff8344f23ca369' .
                'c5accbb3f2a944c34d0cfa7496c099beb7441f1cbbbbd36d0581adc',
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
                '3f4ece5985d1207999c7a6c0511e25c20d4a9549588fe0f41ab720de1fdb02adecd02099' .
                'fd307f896a08cb7c5c536d193d8ea8972c389830190e7e128fb42839'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '3F4ECE5985D1207999C7A6C0511E25C20D4A9549588FE0F41AB720DE1FDB02ADECD02099' .
            'FD307F896A08CB7C5C536D193D8EA8972C389830190E7E128FB42839',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '3f4ece5985d1207999c7a6c0511e25c20d4a9549588fe0f41ab720de1fdb02adecd02099' .
            'fd307f896a08cb7c5c536d193d8ea8972c389830190e7e128fb42839',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'P07OWYXRIHmZx6bAUR4lwg1KlUlYj+D0Grcg3h/bAq3s0CCZ/TB/iWoIy3xcU20ZPY6olyw4mDAZDn4Sj7QoOQ==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'P07OWYXRIHmZx6bAUR4lwg1KlUlYj-D0Grcg3h_bAq3s0CCZ_TB_iWoIy3xcU20ZPY6olyw4mDAZDn4Sj7QoOQ',
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
            '4EC8D6C27FB711D4EDB1B2C78462AB3A5570FF0BF1EFF08513470525A3C5FFE464D5DE2' .
            '2F32C781AD7F9ADBE141C4609A40CDEAD4E1033E0927CFA6A77496A97',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '0023F0692B0D792B7837A65C3B40BC83A1F5279887B7CB898E7C1B2195C105CA85D3C1' .
            '06D1DE18C56468049DBF973139DDEDA6E470BD93977B59810DF9E2F3E1',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '1A4CBCCE15D07E568C1CE044E83377BF34F44826CFFEEDA62F754088BA93AB5B06557A' .
            'B3DC4E06CB3776472335645873E75C693E49A889323454A7D7DE12FD7C',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '1FB1CCA3AFDEC68F253EE4C94249DFE1377959BC189AFE0947E147C45FCF655A964775A' .
            'B351D5A167885A52E570B27CC895933657E481787E20C0EBBC6C32A59',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '3FE9850FF6802A6BF6D7365B5F8324F2F42643B2FF2DB858B5C1D6D21DB47853CD7AE71' .
            'D2F3A86094A30B685FE26DC89EEBBDF799200CEE55676844F82D5E37F',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '8A6064C900EFF9A85D54E83CA8BC4461C8A0785D982E440AACC71F0EA0E924AA1AAA6E' .
            '6E3F5279354CB6E80C2FE08B86A95B000F1B9D3BC42E49D5B01BE46519',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'C0195252A411AB283F6640EA68C512318F34B20876A20DE2438CD644ADC24733E440262' .
            '0865C462A69C80684D142CDAFEDCA9AE47A07ED0E5B56BC5D58CF1F3E',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'B8B3038D820B2C54CDE486B5DDA716E2EA3964D44ED70690BCF84DDF9B88156CC02BBB1' .
            '1B029B7FF25DC5E8521B25480D3C6405AB07F02D14EBF8C5A0B0DF852',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'C86D85A13AC9BBF7FD9461A6FCA322817A1B2151FE91DFEBE410E31660C1FCEF092427' .
            '457E366EBE5FE088F6D004B1F786AA67DAAA8E222A06DE8AE3B8FE6ACD',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '962CC538D36D5717A3EA62DF7B9C01429DCB357105BCD7280003D493ABD6B64BB25C3F0' .
            '315CD33DE47AB5C1710EC056377952634BC77ABD3838F3CC1A0360C06',
            $hasher->hashData($data)
        );
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
}
