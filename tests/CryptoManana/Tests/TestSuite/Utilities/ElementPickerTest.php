<?php

/**
 * Testing the ElementPicker component used for random element picking from a string and an array.
 */

namespace CryptoManana\Tests\TestSuite\Utilities;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable;
use CryptoManana\Core\Abstractions\Randomness\AbstractRandomness;
use CryptoManana\Randomness\CryptoRandom;
use CryptoManana\Randomness\PseudoRandom;
use CryptoManana\Randomness\QuasiRandom;
use CryptoManana\Utilities\ElementPicker;

/**
 * Class ElementPickerTest - Tests the element picker class.
 *
 * @package CryptoManana\Tests\TestSuite\Utilities
 */
final class ElementPickerTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @param AbstractRandomness|CryptoRandom|PseudoRandom|QuasiRandom|null $generator Randomness source.
     *
     * @return ElementPicker Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getElementPickerForTesting($generator = null)
    {
        return new ElementPicker($generator);
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $picker = $this->getElementPickerForTesting();

        $tmp = clone $picker;

        $this->assertEquals($picker, $tmp);
        $this->assertNotEmpty($tmp->pickCharacterElement('test'));

        unset($tmp);
        $this->assertNotNull($picker);
    }

    /**
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $picker = $this->getElementPickerForTesting();

        $tmp = serialize($picker);
        $tmp = unserialize($tmp);

        $this->assertEquals($picker, $tmp);
        $this->assertNotEmpty($tmp->pickCharacterElement('test'));

        unset($tmp);
        $this->assertNotNull($picker);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDebugCapabilities()
    {
        $picker = $this->getElementPickerForTesting();

        $this->assertNotEmpty(var_export($picker, true));
    }

    /**
     * Testing the dependency injection principle realization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDependencyInjection()
    {
        $picker = $this->getElementPickerForTesting();

        $this->assertTrue($picker instanceof AbstractRandomnessInjectable);
        $this->assertTrue($picker->getRandomGenerator() instanceof CryptoRandom);

        $picker->setRandomGenerator(new QuasiRandom());
        $this->assertTrue($picker->getRandomGenerator() instanceof QuasiRandom);

        $picker->setRandomGenerator(new PseudoRandom());
        $this->assertTrue($picker->getRandomGenerator() instanceof PseudoRandom);

        $picker = $picker->setRandomGenerator(new CryptoRandom())->seedRandomGenerator();
        $this->assertTrue($picker->getRandomGenerator() instanceof CryptoRandom);
    }

    /**
     * Testing the random picking of a character from a string.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testPickingRandomCharacterFromString()
    {
        $picker = $this->getElementPickerForTesting(new PseudoRandom());
        $testString = 'Long string for testing in here!   #ThisIsNaughty';

        $resultOne = $picker->pickCharacterElement($testString);
        $resultTwo = $picker->pickCharacterElement($testString);

        $this->assertNotEquals($resultOne, $resultTwo);

        $picker->seedRandomGenerator(42);
        $resultOne = $picker->pickCharacterElement($testString);

        $picker->seedRandomGenerator(42);
        $resultTwo = $picker->pickCharacterElement($testString);

        $this->assertEquals($resultOne, $resultTwo);
    }

    /**
     * Testing the random picking of an element from an array.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testPickingRandomElementFromArray()
    {
        $picker = $this->getElementPickerForTesting(new PseudoRandom());
        $testArray = ['1', [3, 2], new \stdClass(), 33, 'test', [], '1', 69];

        $resultOne = $picker->pickArrayElement($testArray);
        $resultTwo = $picker->pickArrayElement($testArray);

        $this->assertNotEquals($resultOne, $resultTwo);

        $picker->seedRandomGenerator(42);
        $resultOne = $picker->pickArrayElement($testArray);

        $picker->seedRandomGenerator(42);
        $resultTwo = $picker->pickArrayElement($testArray);

        $this->assertEquals($resultOne, $resultTwo);
    }

    /**
     * Testing the element picking in supported types with empty values.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testPickingFromEmptyInput()
    {
        $picker = $this->getElementPickerForTesting();

        $this->assertEmpty($picker->pickCharacterElement(''));
        $this->assertEmpty($picker->pickArrayElement([]));
    }

    /**
     * Testing validation case when a string is not given for character picking.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseStringNotGivenForCharacterPicking()
    {
        $generator = $this->getElementPickerForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $generator->pickCharacterElement(['array']);
        } else {
            $hasThrown = null;

            try {
                $generator->pickCharacterElement(['array']);
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
