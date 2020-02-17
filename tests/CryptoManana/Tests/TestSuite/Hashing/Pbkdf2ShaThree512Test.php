<?php

/**
 * Testing the SHA-3 family PBKDF2-SHA-512 realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractIterativeSlowDerivation;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface;
use CryptoManana\Hashing\Pbkdf2ShaThree512;

/**
 * Class Pbkdf2ShaThree512Test - Testing the SHA-3 family PBKDF2-SHA-512 class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class Pbkdf2ShaThree512Test extends AbstractUnitTest
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
     * @return Pbkdf2ShaThree512 Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new Pbkdf2ShaThree512();
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
        $this->assertTrue($hasher instanceof Pbkdf2ShaThree512);

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

        $testCases = in_array('sha3-256', hash_algos(), true) ? [true, false] : [true];

        foreach ($testCases as $toUse) {
            $reflectionUseProperty = new \ReflectionProperty(
                Pbkdf2ShaThree512::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                '23d1114498a6fbaac510e3fa06f30d6b710662d6f0eea69afbc860faca1ef6f39a8' .
                'd8d6706d00ffe59653c37fb91ef42d7d0d7d00608543625e3f4c76d3086fb',
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
                '665409f49b9b46e3f4cb15476ed41d562f4ba27068be4c324d95f25755c37edf23d64a' .
                '31e4a35a344326da324ccee72cf45e5896f9bd261ba622e43b7a1a5204'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '665409F49B9B46E3F4CB15476ED41D562F4BA27068BE4C324D95F25755C37EDF23D64A31' .
            'E4A35A344326DA324CCEE72CF45E5896F9BD261BA622E43B7A1A5204',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '665409f49b9b46e3f4cb15476ed41d562f4ba27068be4c324d95f25755c37edf23d64a' .
            '31e4a35a344326da324ccee72cf45e5896f9bd261ba622e43b7a1a5204',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'ZlQJ9JubRuP0yxVHbtQdVi9LonBovkwyTZXyV1XDft8j1kox5KNaNEMm2jJMzucs9F5Ylvm9JhumIuQ7ehpSBA==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'ZlQJ9JubRuP0yxVHbtQdVi9LonBovkwyTZXyV1XDft8j1kox5KNaNEMm2jJMzucs9F5Ylvm9JhumIuQ7ehpSBA',
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
            'B0768BD3B5C3C8C69F20DC486142F24E8BFA7FE715687E521C1D62295B98EDBD44' .
            '23F71980FD31FA6536856D3943AD48DF7FB4917C7DB19FF1F7ECD10C2753F3',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '41D9245EAE67225A19486ADE4DD46BA481AA997668A2B00EAD3E9D6969AC5B85FC11' .
            '15DDC70E6C650DF38C2C058810AB770948D7D09FCF17D54AE1DB088447F0',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'D88FC76A94D3613D67DB081B8EC989EFBD8162A672BD0A65979611254F5210F0C502' .
            '1427B7C1E80BE4DC83880E64D99651D66A7DC20882B9ED38D15552B75E9E',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '7607D331392FF0D2660C0D734FCF715AF8D7F88D505651A99CADEFFFA834741E69DFC' .
            'E7FE1789292EFB26D17C4DFA6990F82E2F391D95C01E22FCBF9FC83E3DE',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            'D65977AD7F4AC18E0B88F2B3B64440CA9F967D0B94B1FC1B7ED8251664A34C8A98A208' .
            'FDC105F0B8BC93E19092030B7CB7A5243A1796C1DB29BE336BE9517B77',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'B7731DE8EE9495318BFA3F5AE00FC8F7B965A9584908C53A86199349C1E9D4DFA5AFD35' .
            '8B25BAD3B8BAE5CEAFF49E94D934C7FB3C0B12BFFD928A3C8D6AEE828',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '1850C6BBC7DF5A57F7C3E77622F51020A5AECE98EA1132B5ECBF2AA5CE145A78B60C3B8C' .
            '02E63BAFCDD672BAC8FE66CA6CB7F0FFE4F470E12A7FD0332E82C840',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '70F15E95F5BD81B4CCE5741EFCF697611430A39AEC7B15BDCDBA42DA874873E92ACF21667' .
            '144539EF23EDC07875138F9800E5D5D0FAB952C718BFF0B4FC367FD',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'E0501F9B8391886E6FD2DC573E1C68705401BF9B4D053F54E0BF121E57D70475BED3A5EE' .
            'AF1F1447BD161409BDEC20E9F97812F947C4C70734448B674BC179E2',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '91BB22B1A0A0638BD9CF8525F5CBC8C4BC44E110B48D716C2BB71315975AD525BC1CD9732' .
            'A2F62F8BA706134BE1BBEC8520E2A46034277D5186BD8513F232A94',
            $hasher->hashData($data)
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
