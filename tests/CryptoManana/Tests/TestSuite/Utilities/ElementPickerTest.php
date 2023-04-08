<?php

/**
 * Testing the ElementPicker component used for random element picking from a string and an array.
 */

namespace CryptoManana\Tests\TestSuite\Utilities;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Randomness\PseudoRandom;
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
     * @return ElementPicker Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getElementPickerForTesting()
    {
        $generator = $this->getMockBuilder(PseudoRandom::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $generator->expects($this->atLeast(0))
            ->method('getInt')
            ->willReturn(1);

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
     * Testing the random picking of a character from a string.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testPickingRandomCharacterFromString()
    {
        $picker = $this->getElementPickerForTesting();
        $testString = 'Long string for testing in here!   #ThisIsNaughty';

        $resultOne = $picker->pickCharacterElement($testString);
        $resultTwo = $picker->pickCharacterElement($testString);

        $this->assertEquals($resultOne, $resultTwo);

        $this->assertEmpty($picker->pickCharacterElement(''));
    }

    /**
     * Testing the random picking of an element from an array.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testPickingRandomElementFromArray()
    {
        $picker = $this->getElementPickerForTesting();
        $testArray = ['1', [3, 2], new \stdClass(), 33, 'test', [], '1', 69];

        $resultOne = $picker->pickArrayElement($testArray);
        $resultTwo = $picker->pickArrayElement($testArray);

        $this->assertEquals($resultOne, $resultTwo);

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
