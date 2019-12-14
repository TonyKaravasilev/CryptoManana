<?php

/**
 * Testing the internal StringBuilder component used for unicode string manipulations.
 */

namespace CryptoManana\Tests\TestSuite\Core;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Interfaces\DesignPatterns\CoreStringBuilderInterface;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton;
use \CryptoManana\Core\StringBuilder;

/**
 * Class StringBuilderTest - Tests the string helper class.
 *
 * @package CryptoManana\Tests\TestSuite\Core
 */
final class StringBuilderTest extends AbstractUnitTest
{
    /**
     * Testing the object dumping for debugging.
     */
    public function testDebugCapabilities()
    {
        $factory = StringBuilder::getInstance();

        $this->assertNotEmpty(var_export($factory, true));
    }

    /**
     * Testing method for switching between `mbstring` and core functions.
     */
    public function testChangingStringManipulationMode()
    {
        StringBuilder::useMbString();
        $this->assertTrue(StringBuilder::isUsingMbString());

        StringBuilder::useMbString(0);
        $this->assertFalse(StringBuilder::isUsingMbString());

        StringBuilder::useMbString(1);
        $this->assertTrue(StringBuilder::isUsingMbString());

        StringBuilder::useMbString(false);
        $this->assertFalse(StringBuilder::isUsingMbString());

        StringBuilder::useMbString(true);
        $this->assertTrue(StringBuilder::isUsingMbString());
    }

    /**
     * Testing the getting of correct length.
     */
    public function testStringLengthCheck()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals(4, StringBuilder::stringLength('YDR1'));
        $this->assertEquals(4, StringBuilder::stringLength('1Ab#'));
        $this->assertEquals(4, StringBuilder::stringLength('2⺓⺔я'));
        $this->assertFalse(StringBuilder::stringLength(new \stdClass()));

        StringBuilder::useMbString(false);
        $this->assertEquals(4, StringBuilder::stringLength('YDR1'));
        $this->assertEquals(4, StringBuilder::stringLength('1Ab#'));
        $this->assertNotEquals(4, StringBuilder::stringLength('2⺓⺔я'));
        $this->assertFalse(StringBuilder::stringLength(new \stdClass()));
    }

    /**
     * Testing the converting to upper case.
     */
    public function testStringToUpper()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals('YDR1', StringBuilder::stringToUpper('YDR1'));
        $this->assertEquals('1AB#', StringBuilder::stringToUpper('1Ab#'));
        $this->assertEquals('2⺓⺔Я', StringBuilder::stringToUpper('2⺓⺔я'));
        $this->assertFalse(StringBuilder::stringToUpper(new \stdClass()));

        StringBuilder::useMbString(false);
        $this->assertEquals('YDR1', StringBuilder::stringToUpper('YDR1'));
        $this->assertEquals('1AB#', StringBuilder::stringToUpper('1Ab#'));
        $this->assertNotEquals('2⺓⺔Я', StringBuilder::stringToUpper('2⺓⺔я'));
        $this->assertFalse(StringBuilder::stringToUpper(new \stdClass()));
    }

    /**
     * Testing the converting to lower case.
     */
    public function testStringToLower()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals('ydr1', StringBuilder::stringToLower('YDR1'));
        $this->assertEquals('1ab#', StringBuilder::stringToLower('1Ab#'));
        $this->assertEquals('2⺓⺔я', StringBuilder::stringToLower('2⺓⺔Я'));
        $this->assertFalse(StringBuilder::stringToLower(new \stdClass()));

        StringBuilder::useMbString(false);
        $this->assertEquals('ydr1', StringBuilder::stringToLower('YDR1'));
        $this->assertEquals('1ab#', StringBuilder::stringToLower('1Ab#'));
        $this->assertNotEquals('2⺓⺔я', StringBuilder::stringToLower('2⺓⺔Я'));
        $this->assertFalse(StringBuilder::stringToLower(new \stdClass()));
    }

    /**
     * Testing the converting of an integer to a character.
     */
    public function testGettingOfCharacterSymbol()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals('T', StringBuilder::getChr(84));

        // Available in PHP >= 7.2.0
        if (function_exists('mb_chr')) {
            $this->assertEquals('Ā', StringBuilder::getChr(256));
        } else {
            $this->assertEquals("\0", StringBuilder::getChr(256));
        }

        $this->assertFalse(StringBuilder::getChr(-1024));

        StringBuilder::useMbString(false);
        $this->assertEquals('T', StringBuilder::getChr(84));
        $this->assertEquals("\0", StringBuilder::getChr(256));
        $this->assertFalse(StringBuilder::getChr(-1024));
    }

    /**
     * Testing the converting a character to an integer.
     */
    public function testGettingOfCharacterCode()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals(84, StringBuilder::getOrd('T'));

        // Available in PHP >= 7.2.0
        if (function_exists('mb_ord')) {
            $this->assertEquals(256, StringBuilder::getOrd('Ā'));
        } else {
            $this->assertEquals(0, StringBuilder::getOrd("\0"));
        }

        $this->assertFalse(StringBuilder::getOrd([]));

        StringBuilder::useMbString(false);
        $this->assertEquals(84, StringBuilder::getOrd('T'));
        $this->assertEquals(0, StringBuilder::getOrd("\0"));
        $this->assertFalse(StringBuilder::getOrd([]));
    }

    /**
     * Testing the reversing of a string.
     */
    public function testStringReverse()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals('YDR1', StringBuilder::stringReverse('1RDY'));
        $this->assertEquals('1Ab#', StringBuilder::stringReverse('#bA1'));
        $this->assertEquals('2⺓⺔Я', StringBuilder::stringReverse('Я⺔⺓2'));
        $this->assertFalse(StringBuilder::stringReverse(new \stdClass()));

        StringBuilder::useMbString(false);
        $this->assertEquals('YDR1', StringBuilder::stringReverse('1RDY'));
        $this->assertEquals('1Ab#', StringBuilder::stringReverse('#bA1'));
        $this->assertNotEquals('2⺓⺔Я', StringBuilder::stringReverse('Я⺔⺓2'));
        $this->assertFalse(StringBuilder::stringReverse(new \stdClass()));
    }

    /**
     * Testing the converting of a string to an array.
     */
    public function testStringSplit()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals(['YD', 'R1'], StringBuilder::stringSplit('YDR1', 2));
        $this->assertEquals(['#b', 'A1'], StringBuilder::stringSplit('#bA1', 2));
        $this->assertEquals(['2⺓', '⺔Я'], StringBuilder::stringSplit('2⺓⺔Я', 2));
        $this->assertEquals(['2⺓⺔Я'], StringBuilder::stringSplit('2⺓⺔Я', 10));
        $this->assertEquals(['12', '34', '5Я', '78'], StringBuilder::stringSplit('12345Я78', 2));
        $this->assertEquals(['2', '⺓', '⺔', 'Я'], StringBuilder::stringSplit('2⺓⺔Я', 1));
        $this->assertFalse(StringBuilder::stringSplit('', -1024));
        $this->assertFalse(StringBuilder::stringSplit(new \stdClass()));

        StringBuilder::useMbString(false);
        $this->assertEquals(['YD', 'R1'], StringBuilder::stringSplit('YDR1', 2));
        $this->assertEquals(['#b', 'A1'], StringBuilder::stringSplit('#bA1', 2));
        $this->assertNotEquals(['2⺓', '⺔Я'], StringBuilder::stringSplit('2⺓⺔Я', 2));
        $this->assertEquals(['2⺓⺔Я'], StringBuilder::stringSplit('2⺓⺔Я', 10));
        $this->assertEquals(['12', '34', '56', '78'], StringBuilder::stringSplit('12345678', 2));
        $this->assertEquals(['1', '2', '3', '4'], StringBuilder::stringSplit('1234', 1));
        $this->assertFalse(StringBuilder::stringSplit('', -1024));
        $this->assertFalse(StringBuilder::stringSplit(new \stdClass()));
    }

    /**
     * Testing the replacing of values in a string or in an array.
     */
    public function testStringAndArrayNeedleReplacing()
    {
        StringBuilder::useMbString(true);
        $this->assertEquals('ZYX', StringBuilder::stringReplace('A', 'Z', 'AYX'));
        $this->assertEquals('⺔⺔Я', StringBuilder::stringReplace('⺓', '⺔', '⺓⺔Я'));

        $this->assertEquals('X', StringBuilder::stringReplace(['A', 'Y'], '', 'AYX'));
        $this->assertEquals('Я', StringBuilder::stringReplace(['⺓', '⺔'], '', '⺓⺔Я'));

        $this->assertEquals('YZ1ZY', StringBuilder::stringReplace(['A', 'B'], ['Z', 'Y'], 'BA1AB'));
        $this->assertEquals(
            ['Y', 'Z', 'Z', 'Я'],
            StringBuilder::stringReplace(['A', 'B'], ['Z', 'Y'], ['B', 'A', 'A', 'Я'])
        );
        $this->assertEquals(
            ['', '', '', 'Я'],
            StringBuilder::stringReplace(['A', 'B'], '', ['B', 'A', 'A', 'Я'])
        );

        $tmpCount = 0;
        $this->assertEquals(
            'ZZZ',
            StringBuilder::stringReplace('A', 'Z', 'AAA', $tmpCount)
        );
        $this->assertEquals(3, $tmpCount);
        unset($tmpCount);

        StringBuilder::useMbString(false);
        $this->assertEquals('ZYX', StringBuilder::stringReplace('A', 'Z', 'AYX'));
        $this->assertEquals('⺔⺔Я', StringBuilder::stringReplace('⺓', '⺔', '⺓⺔Я'));

        $this->assertEquals('X', StringBuilder::stringReplace(['A', 'Y'], '', 'AYX'));
        $this->assertEquals('Я', StringBuilder::stringReplace(['⺓', '⺔'], '', '⺓⺔Я'));

        $this->assertEquals('YZ1ZY', StringBuilder::stringReplace(['A', 'B'], ['Z', 'Y'], 'BA1AB'));
        $this->assertEquals(
            ['Y', 'Z', 'Z', 'Я'],
            StringBuilder::stringReplace(['A', 'B'], ['Z', 'Y'], ['B', 'A', 'A', 'Я'])
        );
        $this->assertEquals(
            ['', '', '', 'Я'],
            StringBuilder::stringReplace(['A', 'B'], '', ['B', 'A', 'A', 'Я'])
        );

        $tmpCount = 0;
        $this->assertEquals(
            'ZZZ',
            StringBuilder::stringReplace('A', 'Z', 'AAA', $tmpCount)
        );
        $this->assertEquals(3, $tmpCount);
        unset($tmpCount);
    }

    /**
     * Testing the string trimming.
     */
    public function testStringFullTrimming()
    {
        StringBuilder::useMbString(true);

        $this->assertEquals(
            '1a#%afzZ!',
            StringBuilder::stringFullTrimming("  \x0B\0 \t\n\r   1a \t #%afzZ ! \0 \t\n\r\x0B   ")
        );

        $this->assertEquals(
            '1a#%⺓⺔ЯF!',
            StringBuilder::stringFullTrimming("  \x0B\0 \t\n\r   1a \t #%⺓⺔ЯF ! \0 \t\n\r\x0B   ")
        );

        $this->assertEmpty(StringBuilder::stringFullTrimming(" \x0B\0 \t \n\r  \0  \t\n \r\x0B "));
        $this->assertFalse(StringBuilder::stringFullTrimming(new \stdClass()));

        StringBuilder::useMbString(false);

        $this->assertEquals(
            '1a#%afzZ!',
            StringBuilder::stringFullTrimming("  \x0B\0 \t\n\r   1a \t #%afzZ ! \0 \t\n\r\x0B   ")
        );

        $this->assertEquals(
            '1a#%⺓⺔ЯF!',
            StringBuilder::stringFullTrimming("  \x0B\0 \t\n\r   1a \t #%⺓⺔ЯF ! \0 \t\n\r\x0B   ")
        );

        $this->assertEmpty(StringBuilder::stringFullTrimming(" \x0B\0 \t \n\r  \0  \t\n \r\x0B "));
        $this->assertFalse(StringBuilder::stringFullTrimming(new \stdClass()));
    }

    /**
     * Testing the extended singleton functionality.
     *
     * @throws \ReflectionException If the tested class or method does not exist.
     */
    public function testSingletonInstancing()
    {
        $tmp = StringBuilder::getInstance();

        $this->assertTrue($tmp instanceof CoreStringBuilderInterface);
        $this->assertTrue($tmp instanceof AbstractSingleton);
        $this->assertTrue($tmp instanceof StringBuilder);

        $this->assertEquals(StringBuilder::class, (string)$tmp);
        $reflection = new \ReflectionClass(StringBuilder::class);

        $this->assertTrue($reflection->getConstructor()->isProtected());

        $internalMethods = [
            '__clone' => 'isPrivate',
            '__sleep' => 'isPrivate',
            '__wakeup' => 'isPrivate',
        ];

        foreach ($internalMethods as $method => $visibility) {
            $method = $reflection->getMethod($method);
            $this->assertTrue($method->{$visibility}());

            $method->setAccessible(true);

            $this->assertNull($method->invoke($tmp));
        }
    }
}
