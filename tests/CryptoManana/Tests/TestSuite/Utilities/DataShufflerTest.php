<?php

/**
 * Testing the DataShuffler component used for string and array shuffling.
 */

namespace CryptoManana\Tests\TestSuite\Utilities;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Randomness\PseudoRandom;
use CryptoManana\Utilities\DataShuffler;

/**
 * Class DataShufflerTest - Tests the data shuffler class.
 *
 * @package CryptoManana\Tests\TestSuite\Utilities
 */
final class DataShufflerTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return DataShuffler Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataShufflerForTesting()
    {
        $generator = $this->getMockBuilder(PseudoRandom::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $generator->expects($this->atLeast(0))
            ->method('getInt')
            ->willReturn(0);

        return new DataShuffler($generator);
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $shuffler = $this->getDataShufflerForTesting();

        $tmp = clone $shuffler;

        $this->assertEquals($shuffler, $tmp);
        $this->assertNotEmpty($tmp->shuffleString('test'));

        unset($tmp);
        $this->assertNotNull($shuffler);
    }

    /**
     * Testing the string shuffling.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testStringShuffling()
    {
        $shuffler = $this->getDataShufflerForTesting();
        $testArray = '123456789';

        $resultOne = $shuffler->shuffleString($testArray);
        $resultTwo = $shuffler->shuffleString($testArray);

        $this->assertEquals($resultOne, $resultTwo);

        $this->assertEmpty($shuffler->shuffleString(''));
    }

    /**
     * Testing the array shuffling.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testArrayShuffling()
    {
        $shuffler = $this->getDataShufflerForTesting();
        $testArray = ['1', [3, 2], new \stdClass(), 33, 'test', [], '1', 69];

        $resultOne = $shuffler->shuffleArray($testArray);
        $resultTwo = $shuffler->shuffleArray($testArray);

        $this->assertEquals($resultOne, $resultTwo);

        $this->assertEmpty($shuffler->shuffleArray([]));
    }

    /**
     * Testing validation case when a string is not given for string shuffling.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseStringNotGivenForStringShuffling()
    {
        $generator = $this->getDataShufflerForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $generator->shuffleString(['array']);
        } else {
            $hasThrown = null;

            try {
                $generator->shuffleString(['array']);
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
