<?php

/**
 * Testing the FileShredder component used for file erasure operations.
 */

namespace CryptoManana\Tests\TestSuite\Utilities;

use CryptoManana\Randomness\PseudoRandom;
use CryptoManana\Tests\TestTypes\AbstractUnitTest;
use CryptoManana\Utilities\FileShredder;

/**
 * Class FileShredderTest - Tests the file shredder class.
 *
 * @package CryptoManana\Tests\TestSuite\Utilities
 */
final class FileShredderTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return FileShredder Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getFileShredderForTesting()
    {
        $generator = $this->getMockBuilder(PseudoRandom::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $generator->expects($this->atLeast(0))
            ->method('getBytes')
            ->willReturn("\0");

        return new FileShredder($generator);
    }

    /**
     * Testing the cloning of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testCloningCapabilities()
    {
        $shredder = $this->getFileShredderForTesting();

        $tmp = clone $shredder;

        $this->assertEquals($shredder, $tmp);
        $this->assertNotEmpty($shredder->getRandomGenerator());

        unset($tmp);
        $this->assertNotNull($shredder);
    }

    /**
     * Testing the file erasure.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSecureFileErasure()
    {
        $shredder = $this->getFileShredderForTesting();
        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, 'test');

        $shredder->eraseFile($fileName);

        $this->assertEquals('', $this->readFromFile($fileName));

        // For safety, if the above assert fails
        $this->deleteTheFile($fileName);
    }

    /**
     * Testing the empty file erasure.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testEmptyFileErasure()
    {
        $shredder = $this->getFileShredderForTesting();
        $fileName = $this->getTemporaryFilename();

        $this->writeToFile($fileName, '');

        $shredder->eraseFile($fileName);

        $this->assertEquals('', $this->readFromFile($fileName));

        // For safety, if the above assert fails
        $this->deleteTheFile($fileName);
    }

    /**
     * Testing validation case for invalid type of filename used for erasure.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testValidationCaseForInvalidFileNameUsedForErasure()
    {
        $shredder = $this->getFileShredderForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);

            $shredder->eraseFile(['wrong']);
        } else {
            $hasThrown = null;

            try {
                $shredder->eraseFile(['wrong']);
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
    public function testValidationCaseForNonExistingFileNameUsedForErasure()
    {
        $shredder = $this->getFileShredderForTesting();

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(\RuntimeException::class);

            $shredder->eraseFile('non-existing.tmp');
        } else {
            $hasThrown = null;

            try {
                $shredder->eraseFile('non-existing.tmp');
            } catch (\RuntimeException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
