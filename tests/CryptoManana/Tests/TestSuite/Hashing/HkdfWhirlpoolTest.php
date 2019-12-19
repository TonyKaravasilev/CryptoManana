<?php

/**
 * Testing the HKDF-Whirlpool realization used for digest generation.
 */

namespace CryptoManana\Tests\TestSuite\Hashing;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface;
use \CryptoManana\Hashing\HkdfWhirlpool;

/**
 * Class HkdfWhirlpoolTest - Testing the HKDF-Whirlpool class.
 *
 * @package CryptoManana\Tests\TestSuite\Hashing
 */
final class HkdfWhirlpoolTest extends AbstractUnitTest
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
     * @return HkdfWhirlpool Testing instance.
     * @throws \Exception If the system does not support the algorithm.
     */
    private function getHashAlgorithmInstanceForTesting()
    {
        return new HkdfWhirlpool();
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
        $this->assertTrue($hasher instanceof HkdfWhirlpool);

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
                HkdfWhirlpool::class,
                'useNative'
            );

            $reflectionUseProperty->setAccessible(true);
            $reflectionUseProperty->setValue($hasher, $toUse);

            $this->assertEquals(
                '6aac29fe24e2821f320badddde4e085c7ec6de06e4b549725165a21f7614d03b07ffdc0f' .
                'ee231052e6de26a6bfb30f6af233e2efb351955d67ec6f9e0741ad34',
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
                '46e262fef3d69516a54b6b3f34b27fa5c952ff66db8c826b0d3ed486b952e2f729646fd43' .
                '8620be898d1c8a659b8440b889a33b81b0864fb57f547618f662a66'
            ),
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_UPPER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_UPPER, $hasher->getDigestFormat());

        $this->assertEquals(
            '46E262FEF3D69516A54B6B3F34B27FA5C952FF66DB8C826B0D3ED486B952E2F729646FD43' .
            '8620BE898D1C8A659B8440B889A33B81B0864FB57F547618F662A66',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_HEX_LOWER);
        $this->assertEquals($hasher::DIGEST_OUTPUT_HEX_LOWER, $hasher->getDigestFormat());

        $this->assertEquals(
            '46e262fef3d69516a54b6b3f34b27fa5c952ff66db8c826b0d3ed486b952e2f729646fd43' .
            '8620be898d1c8a659b8440b889a33b81b0864fb57f547618f662a66',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64, $hasher->getDigestFormat());

        $this->assertEquals(
            'RuJi/vPWlRalS2s/NLJ/pclS/2bbjIJrDT7UhrlS4vcpZG/UOGIL6JjRyKZZuEQLiJozuBsIZPtX9Udhj2YqZg==',
            $hasher->hashData('')
        );

        $hasher->setDigestFormat($hasher::DIGEST_OUTPUT_BASE_64_URL);
        $this->assertEquals($hasher::DIGEST_OUTPUT_BASE_64_URL, $hasher->getDigestFormat());

        $this->assertEquals(
            'RuJi_vPWlRalS2s_NLJ_pclS_2bbjIJrDT7UhrlS4vcpZG_UOGIL6JjRyKZZuEQLiJozuBsIZPtX9Udhj2YqZg',
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
            'A2A13644AB1C22BB1C6DFC4E98D2A74E1D9F15F0820D6E7D2C749BF1583549E52D74AAFDB' .
            '5E617C3846AA7682ED96CC798DDEA888571618AF6D35D2E04A43159',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_APPEND); // 'test1234'
        $this->assertEquals($hasher::SALTING_MODE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            'B3C158BA3CD28D7F5D4C2D2FD8BBF62E4EE11B1F8CA0EBB28EB38AF8DB271CFB48DC19BE8' .
            'E58A7884612E1FD2A80337F2F4B9F54A28FB62AB3A18EB285B908E8',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PREPEND); // '1234test'
        $this->assertEquals($hasher::SALTING_MODE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '20493DC95CE0C6A169E99D6FD3D4A77F814552E019FB0C7A05AEDAC95F715BBBFF7631B90' .
            '16EB6B7B82AEDEF0A76BCA51CA6ED28814B35A22C47C54987780802',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_INPUT); // '1234test4321'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_INPUT, $hasher->getSaltingMode());

        $this->assertEquals(
            '3AAECADEA2DC521B24B8BAB0852B5C7196995AE73E0EC85561250997D262458DE7931F4CB' .
            'F800FC768EF1836A3BDCD084A41F05F12526E5BE27CF2374405652D',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_INFIX_SALT); // 'test1234tset'
        $this->assertEquals($hasher::SALTING_MODE_INFIX_SALT, $hasher->getSaltingMode());

        $this->assertEquals(
            '9700385D93CCA3D1FECFED26EE8DC0FB1CE0DED843D18CC0F07053A6CF098A07675AA8EBF' .
            '047D5D1E42E34BCCD6D30526FDE3D501BB7D3F15A7FEF2B7D03BA72',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_APPEND); // 'test4321'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_APPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '66A33956720AF45C73EB6F1EB44B9303A08F7DA493F84DFFD051B2C45DB6F4372ABDDC10F' .
            '30F85B1AC8B78206BD277FFD64881FF7C82D42257D0D18523D8831A',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_REVERSE_PREPEND); // '4321test'
        $this->assertEquals($hasher::SALTING_MODE_REVERSE_PREPEND, $hasher->getSaltingMode());

        $this->assertEquals(
            '37AC777708AFAB9DA8A86E3A34AA4B30CC0F19155BE1E43BD1FE04CF5A3A41DB7A4925D128' .
            '2865250D51F00B10D53BB948346EE5D9243C2477A526A35A774E2C',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_SUFFIX); // 'test12344321'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_SUFFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            'E9507D9D564A44B3BD3B78057194A8F63D920986254C2E832A95293FF7ABA641EEF34D9ED' .
            'DBB60329821CBD343402849FFDBB7DA785347DF6863BECD7E857675',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_DUPLICATE_PREFIX); // '12344321test'
        $this->assertEquals($hasher::SALTING_MODE_DUPLICATE_PREFIX, $hasher->getSaltingMode());

        $this->assertEquals(
            '2D70C4F84C8FB35D9988DCD6C8F0EF687E3BE32C1708F71451FE929E0A04596D95AB1B4CB' .
            'DFDF893539E1AD5C4E53DECB6B178680114594FC24DC69A53E34FC8',
            $hasher->hashData($data)
        );

        $hasher->setSaltingMode($hasher::SALTING_MODE_PALINDROME_MIRRORING); // '1234testtset4321'
        $this->assertEquals($hasher::SALTING_MODE_PALINDROME_MIRRORING, $hasher->getSaltingMode());

        $this->assertEquals(
            '1FCC283CC51CF3A4121B89C9A273D707BE090577456C6CA41A09C8B3F53B5610C530DF0E3' .
            '8ACC634DD5C561DA16B6CD23CABFA6566FFF4C653D8FB2FD5F38DD9',
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
