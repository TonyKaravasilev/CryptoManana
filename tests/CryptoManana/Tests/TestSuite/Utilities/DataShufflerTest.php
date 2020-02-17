<?php

/**
 * Testing the DataShuffler component used for string and array shuffling.
 */

namespace CryptoManana\Tests\TestSuite\Utilities;

use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable;
use CryptoManana\Core\Abstractions\Randomness\AbstractRandomness;
use CryptoManana\Randomness\CryptoRandom;
use CryptoManana\Randomness\PseudoRandom;
use CryptoManana\Randomness\QuasiRandom;
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
     * @param AbstractRandomness|CryptoRandom|PseudoRandom|QuasiRandom|null $generator Randomness source.
     *
     * @return DataShuffler Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getDataShufflerForTesting($generator = null)
    {
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
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $shuffler = $this->getDataShufflerForTesting();

        $tmp = serialize($shuffler);
        $tmp = unserialize($tmp);

        $this->assertEquals($shuffler, $tmp);
        $this->assertNotEmpty($tmp->shuffleString('test'));

        unset($tmp);
        $this->assertNotNull($shuffler);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDebugCapabilities()
    {
        $shuffler = $this->getDataShufflerForTesting();

        $this->assertNotEmpty(var_export($shuffler, true));
    }

    /**
     * Testing the dependency injection principle realization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDependencyInjection()
    {
        $shuffler = $this->getDataShufflerForTesting();

        $this->assertTrue($shuffler instanceof AbstractRandomnessInjectable);
        $this->assertTrue($shuffler->getRandomGenerator() instanceof CryptoRandom);

        $shuffler->setRandomGenerator(new QuasiRandom());
        $this->assertTrue($shuffler->getRandomGenerator() instanceof QuasiRandom);

        $shuffler->setRandomGenerator(new PseudoRandom());
        $this->assertTrue($shuffler->getRandomGenerator() instanceof PseudoRandom);

        $shuffler = $shuffler->setRandomGenerator(new CryptoRandom())->seedRandomGenerator();
        $this->assertTrue($shuffler->getRandomGenerator() instanceof CryptoRandom);
    }

    /**
     * Testing the string shuffling.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testStringShuffling()
    {
        $shuffler = $this->getDataShufflerForTesting(new PseudoRandom());
        $testString = 'Long string for testing in here!   #ThisIsNaughty';

        $resultOne = $shuffler->shuffleString($testString);
        $resultTwo = $shuffler->shuffleString($testString);

        $this->assertNotEquals($resultOne, $resultTwo);

        $shuffler->seedRandomGenerator(42);
        $resultOne = $shuffler->shuffleString($testString);

        $shuffler->seedRandomGenerator(42);
        $resultTwo = $shuffler->shuffleString($testString);

        $this->assertEquals($resultOne, $resultTwo);
    }

    /**
     * Testing the array shuffling.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testArrayShuffling()
    {
        $shuffler = $this->getDataShufflerForTesting(new PseudoRandom());
        $testArray = ['1', [3, 2], new \stdClass(), 33, 'test', [], '1', 69];

        $resultOne = $shuffler->shuffleArray($testArray);
        $resultTwo = $shuffler->shuffleArray($testArray);

        $this->assertNotEquals($resultOne, $resultTwo);

        $shuffler->seedRandomGenerator(42);
        $resultOne = $shuffler->shuffleArray($testArray);

        $shuffler->seedRandomGenerator(42);
        $resultTwo = $shuffler->shuffleArray($testArray);

        $this->assertEquals($resultOne, $resultTwo);
    }

    /**
     * Testing the shuffling of supported types with empty values.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testShufflingOfEmptyInput()
    {
        $shuffler = $this->getDataShufflerForTesting();

        $this->assertEmpty($shuffler->shuffleString(''));
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
