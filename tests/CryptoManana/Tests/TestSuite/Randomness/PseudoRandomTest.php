<?php

/**
 * Testing the pseudo-randomness realization used for statistically random sequence generation.
 */

namespace CryptoManana\Tests\TestSuite\Randomness;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Randomness\PseudoRandom;

/**
 * Class PseudoRandomTest - Testing the pseudo-randomness class.
 *
 * @package CryptoManana\Tests\TestSuite\Randomness
 */
final class PseudoRandomTest extends AbstractUnitTest
{
    /**
     * How many times to rerun generation tests.
     */
    const REPEATED_TESTS_COUNT = 10;

    /**
     * Creates new instances for testing.
     *
     * @return PseudoRandom Testing instance.
     */
    private function getRandomnessSourceForTesting()
    {
        return new PseudoRandom();
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $tmp = clone $randomness;

        $this->assertEquals($randomness, $tmp);
        $this->assertNotEmpty($tmp->getBytes(8));

        unset($tmp);
        $this->assertNotNull($randomness);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $tmp = serialize($randomness);
        $tmp = unserialize($tmp);

        $this->assertEquals($randomness, $tmp);
        $this->assertNotEmpty($tmp->getBytes(8));

        unset($tmp);
        $this->assertNotNull($randomness);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $this->assertNotEmpty(var_export($randomness, true));

        $reflection = new \ReflectionClass($randomness);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($randomness);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing algorithm initialization and seeding actions.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSeedingAndAutoSeedingActions()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Check if interface is implemented
        $this->assertTrue(
            $randomness instanceof \CryptoManana\Core\Interfaces\Randomness\SeedableGeneratorInterface
        );

        // Auto seed procedure
        $this->assertNull($randomness::setSeed());
        $this->assertNull(PseudoRandom::setSeed(null));

        // Seeding test for producing same values
        $randomness::setSeed(69);
        $sumOne = 0;

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $sumOne += $randomness->getInt(0, 100);

            $sumOne++;
        }

        $randomness::setSeed(69);
        $sumTwo = 0;

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $sumTwo += $randomness->getInt(0, 100);

            $sumTwo++;
        }

        $this->assertEquals($sumOne, $sumTwo);

        // Seeding test for not producing the same values
        $randomness::setSeed(-42);
        $numberOne = $randomness->getInt();

        $randomness::setSeed(+42);
        $numberTwo = $randomness->getInt();

        $this->assertNotEquals($numberOne, $numberTwo);

        // Zero seed and used as main sequence for next tests
        $randomness::setSeed(0);
        $this->assertTrue($randomness->getInt() !== 0);

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness::setSeed('manana');
        } else {
            $hasThrown = null;

            try {
                $randomness::setSeed('manana');
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing the supported range for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSupportedRangeForIntegerGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $this->assertEquals(mt_getrandmax(), $randomness->getMaxNumber());
        $this->assertEquals(-mt_getrandmax() - 1, $randomness->getMinNumber());

        $this->assertTrue(
            is_int(
                $randomness->getInt($randomness->getMinNumber(), $randomness->getMaxNumber())
            )
        );
    }

    /**
     * Testing byte and boolean formats for data generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testByteAndBooleanGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $tenBytes = $randomness->getBytes(10);

            $this->assertTrue(!empty($tenBytes) && is_string($tenBytes));
            $this->assertTrue(strlen($tenBytes) === 10);

            $bool = $randomness->getBool();
            $this->assertTrue(is_bool($bool) && !is_null($bool));

            $ternary = $randomness->getTernary(true);
            $this->assertTrue($ternary >= -1 && $ternary <= 1);

            $ternary = $randomness->getTernary(false);
            $this->assertTrue(in_array($ternary, [true, false, null]));
        }
    }

    /**
     * Testing numerical formats for data generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testNumericalFormatGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $int = $randomness->getInt(-10, 10);
            $this->assertTrue($int >= -10 && $int <= 10);

            $int = $randomness->getInt(0, 1);
            $this->assertTrue($int >= 0 && $int <= 1);

            $float = $randomness->getFloat(-2, 2, 10);
            $this->assertTrue($float >= -2.0 && $float <= 2.0);

            $float = $randomness->getFloat(0.0, $randomness->getMaxNumber());
            $this->assertTrue($float >= 0.0 && $float <= (float)$randomness->getMaxNumber());

            $float = $randomness->getFloat($randomness->getMinNumber() + 1.0, 0.0);
            $this->assertTrue($float >= (float)$randomness->getMinNumber() && $float <= 0.0);

            $probability = $randomness->getProbability(10);
            $this->assertTrue($probability >= 0.0 && $probability <= 1.0);

            $percentCaseOne = $randomness->getPercent(6);
            $this->assertTrue($percentCaseOne >= 0.0 && $percentCaseOne <= 100.0);

            $percentCaseTwo = $randomness->getPercent(4, true);
            $this->assertTrue($percentCaseTwo >= 0.0 && $percentCaseTwo <= 100.0);

            $digitWithNull = $randomness->getDigit();
            $this->assertTrue($digitWithNull >= 0 && $digitWithNull <= 9);

            $digitWithoutNull = $randomness->getDigit(false);
            $this->assertTrue($digitWithoutNull >= 1 && $digitWithoutNull <= 9);
        }
    }

    /**
     * Testing string formats for data generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testStringFormatGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $hexLowerCasePattern = '/^[a-f0-9]+$/';
        $hexUpperCasePattern = '/^[A-F0-9]+$/';

        $base64Pattern = '%^[a-zA-Z0-9/+]*={0,2}$%';
        $base64UrlFriendlyPattern = '/^[a-zA-Z0-9_-]+$/';

        $alphaNumericCaseSensitivePattern = '/^[a-zA-Z0-9]+$/';
        $alphaNumericCaseInsensitivePattern = '/^[a-z0-9]+$/';

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $letterCode = ord($randomness->getLetter(false));
            $this->assertTrue(($letterCode >= 65 && $letterCode <= 90) || ($letterCode >= 97 && $letterCode <= 122));

            $letterCode = ord($randomness->getLetter(false));
            $this->assertTrue($letterCode >= 97 && $letterCode <= 122);

            $hex = $randomness->getHex(10);
            $this->assertTrue(strlen($hex) === 20);
            $this->assertEquals(1, preg_match($hexLowerCasePattern, $hex));

            $hex = $randomness->getHex(10, true);
            $this->assertTrue(strlen($hex) === 20);
            $this->assertEquals(1, preg_match($hexUpperCasePattern, $hex));

            $base64 = $randomness->getBase64(10);
            $this->assertTrue(strlen($base64) === 16);
            $this->assertTrue(strlen($base64) % 4 === 0);
            $this->assertEquals(1, preg_match($base64Pattern, $base64));

            $base64 = $randomness->getBase64(10, true);
            $this->assertTrue(strlen($base64) === 14);
            $this->assertTrue(strpos($base64, '=') === false);
            $this->assertEquals(1, preg_match($base64UrlFriendlyPattern, $base64));

            $alphaNumeric = $randomness->getAlphaNumeric(10);
            $this->assertTrue(strlen($alphaNumeric) === 10);
            $this->assertEquals(1, preg_match($alphaNumericCaseSensitivePattern, $alphaNumeric));

            $alphaNumeric = $randomness->getAlphaNumeric(10, false);
            $this->assertTrue(strlen($alphaNumeric) === 10);
            $this->assertEquals(1, preg_match($alphaNumericCaseInsensitivePattern, $alphaNumeric));

            // Ascii generate cases
            $asciiStrings = [
                $randomness->getAscii(20, false) => false,
                $randomness->getAscii(20, true) => true,
                $randomness->getAlphaNumeric(20) => false,
                $randomness->getString(20) => true,
            ];

            foreach ($asciiStrings as $currentString => $canContainSpace) {
                $this->assertTrue(strlen($currentString) === 20);

                $valid = true;

                for ($i = 0; $i < strlen($currentString); $i++) {
                    $charCode = ord($currentString[$i]);

                    $firstCode = ($canContainSpace == true) ? 32 : 33;

                    if ($charCode < $firstCode || $charCode > 126) {
                        $valid = false;

                        break;
                    }
                }

                $this->assertTrue($valid);
            }

            $customPattern = '/^[aB6!]+$/';
            $customString = $randomness->getString(10, ['a', 'B', '6', '!']);
            $this->assertTrue(strlen($customString) === 10);
            $this->assertEquals(1, preg_match($customPattern, $customString));
        }
    }

    /**
     * Testing unique identifier formats for data generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testUniqueIdentifierGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $guidPattern = '/^\{?[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}\}?$/i';

        $uppercaseWithDashesPattern = '/^[A-Z0-9-]+$/';
        $uppercaseWithoutDashesPattern = '/^[A-Z0-9]+$/';

        $hexUpperCasePattern = '/^[A-F0-9]+$/';
        $alphaNumericPattern = '/^[a-zA-Z0-9]+$/';

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $guid = $randomness->getGloballyUniqueId();
            $this->assertEquals(1, preg_match($guidPattern, $guid));

            $guidWithPrefix = $randomness->getGloballyUniqueId('manana_');
            $this->assertTrue(strpos($guidWithPrefix, 'manana_') === 0);

            $guidWithoutDashes = $randomness->getGloballyUniqueId('', false);
            $this->assertTrue(strpos($guidWithoutDashes, '-') === false);

            $guidUpperWithDashes = $randomness->getGloballyUniqueId('', true, true);
            $this->assertEquals(1, preg_match($uppercaseWithDashesPattern, $guidUpperWithDashes));

            $guidUpperWithoutDashes = $randomness->getGloballyUniqueId('', false, true);
            $this->assertEquals(1, preg_match($uppercaseWithoutDashesPattern, $guidUpperWithoutDashes));

            $uuidFormatOne = $randomness->getStrongUniqueId('', false);
            $this->assertTrue(strlen($uuidFormatOne) === 128);
            $this->assertEquals(1, preg_match($hexUpperCasePattern, $uuidFormatOne));

            $uuidFormatTwo = $randomness->getStrongUniqueId('', true);
            $this->assertTrue(strlen($uuidFormatTwo) === 128);
            $this->assertEquals(1, preg_match($alphaNumericPattern, $uuidFormatTwo));
        }

        // Simple uniqueness test for identifiers
        $identifiersOne = [];
        $identifiersTwo = [];
        $identifiersThree = [];

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $identifiersOne[] = $randomness->getGloballyUniqueId();
            $identifiersTwo[] = $randomness->getStrongUniqueId();
            $identifiersThree[] = $randomness->getStrongUniqueId('', true);
        }

        $this->assertTrue(count($identifiersOne) == count(array_unique($identifiersOne)));
        $this->assertTrue(count($identifiersTwo) == count(array_unique($identifiersTwo)));
        $this->assertTrue(count($identifiersThree) == count(array_unique($identifiersThree)));
    }

    /**
     * Testing RGB formats for data generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testColourPairGeneration()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        $hexFormatPattern = '/#([a-f0-9]{3}){1,2}\b/i';

        for ($i = 1; $i <= self::REPEATED_TESTS_COUNT; $i++) {
            $rgb = $randomness->getRgbColourPair(true);
            $this->assertTrue(count($rgb) === 3);
            $this->assertTrue($rgb[0] >= 0 && $rgb[0] <= 255);
            $this->assertTrue($rgb[1] >= 0 && $rgb[1] <= 255);
            $this->assertTrue($rgb[2] >= 0 && $rgb[2] <= 255);

            $rgb = $randomness->getRgbColourPair(false);
            $this->assertTrue(strlen($rgb) === 7);
            $this->assertEquals(1, preg_match($hexFormatPattern, $rgb));

            $rgb = $randomness->getRgbGreyscalePair(true);
            $this->assertTrue(count($rgb) === 3);
            $this->assertTrue($rgb[0] >= 0 && $rgb[0] <= 255);
            $this->assertTrue($rgb[1] >= 0 && $rgb[1] <= 255);
            $this->assertTrue($rgb[2] >= 0 && $rgb[2] <= 255);

            $rgb = $randomness->getRgbGreyscalePair(false);
            $this->assertTrue(strlen($rgb) === 7);
            $this->assertEquals(1, preg_match($hexFormatPattern, $rgb));

            $rgb = $randomness->getRgbBlackOrWhitePair(true);
            $rgbSum = $rgb[0] + $rgb[1] + $rgb[2];
            $this->assertTrue(count($rgb) === 3);
            $this->assertTrue($rgbSum == 0 || $rgbSum == 765);

            $rgb = $randomness->getRgbBlackOrWhitePair(false);
            $this->assertTrue(strlen($rgb) === 7);
            $this->assertEquals(1, preg_match($hexFormatPattern, $rgb));
        }
    }

    /**
     * Testing validation case for non-positive output length.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForNonPositiveOutputLength()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getBytes(-1);
        } else {
            $hasThrown = null;

            try {
                $randomness->getBytes(-1);
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case for wrong parameters supplied for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForWrongParametersAtInteger()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getInt('test', 'qko');
        } else {
            $hasThrown = null;

            try {
                $randomness->getInt('test', 'qko');
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum is bigger than the maximum for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromIsBiggerThanToAtInteger()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            $randomness->getInt(10, -10);
        } else {
            $hasThrown = null;

            try {
                $randomness->getInt(10, -10);
            } catch (\LogicException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum and the maximum are the same for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromIsTheSameAsToAtInteger()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            $randomness->getInt(69, 69);
        } else {
            $hasThrown = null;

            try {
                $randomness->getInt(69, 69);
            } catch (\LogicException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum overflows for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromOverflowAtInteger()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getInt($randomness->getMinNumber() - 10.0, 0);
        } else {
            $hasThrown = null;

            try {
                $randomness->getInt($randomness->getMinNumber() - 10.0, 0);
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the maximum overflows for integer generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseToOverflowAtInteger()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getInt(0, $randomness->getMaxNumber() + 10.0);
        } else {
            $hasThrown = null;

            try {
                $randomness->getInt(0, $randomness->getMaxNumber() + 10.0);
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case for wrong parameters supplied for float generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForWrongParametersAtFloat()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getFloat('not numeric', 'test me');
        } else {
            $hasThrown = null;

            try {
                $randomness->getFloat('not numeric', 'test me');
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum is bigger than the maximum for float generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromIsBiggerThanToAtFloat()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            $randomness->getFloat(10.0, -10.0);
        } else {
            $hasThrown = null;

            try {
                $randomness->getFloat(10.0, -10.0);
            } catch (\LogicException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum and the maximum are the same for float generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromIsTheSameAsToAtFloat()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LogicException::class);

            $randomness->getFloat(
                42.00000000000001,
                42.00000000000001,
                14
            );
        } else {
            $hasThrown = null;

            try {
                $randomness->getFloat(
                    42.00000000000001,
                    42.00000000000001,
                    14
                );
            } catch (\LogicException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the minimum overflows for float generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseFromOverflowAtFloat()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getFloat(-9.23E+18, 0.0, 14);
        } else {
            $hasThrown = null;

            try {
                $randomness->getFloat(-9.23E+18, 0.0, 14);
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when the maximum overflows for float generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseToOverflowAtFloat()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\DomainException::class);

            $randomness->getFloat(0.0, 9.23E+18, 14);
        } else {
            $hasThrown = null;

            try {
                $randomness->getFloat(0.0, 9.23E+18, 14);
            } catch (\DomainException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when there is an invalid type at character map for string generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseInvalidTypeInCharacterMapAtCustomString()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $randomness->getString(10, ['1', 'S', 234]);
        } else {
            $hasThrown = null;

            try {
                $randomness->getString(10, ['1', 'S', 234]);
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
     * Testing validation case when there is an invalid length at character map for string generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseInvalidLengthInCharacterMapForCustomString()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LengthException::class);

            $randomness->getString(10, ['1', 'S', 'xx']);
        } else {
            $hasThrown = null;

            try {
                $randomness->getString(10, ['1', 'S', 'xx']);
            } catch (\LengthException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }

    /**
     * Testing validation case when there are not enough symbols at character map for string generation.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseNotEnoughSymbolsInCharacterMapForCustomString()
    {
        $randomness = $this->getRandomnessSourceForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\LengthException::class);

            $randomness->getString(10, ['x']);
        } else {
            $hasThrown = null;

            try {
                $randomness->getString(10, ['x']);
            } catch (\LengthException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
