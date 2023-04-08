<?php

/**
 * Testing the FileShredder component used for file erasure operations.
 */

namespace CryptoManana\Tests\IntegrationSuite\Utilities;

use CryptoManana\Tests\TestTypes\AbstractIntegrationTest;
use CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable;
use CryptoManana\Core\Abstractions\Randomness\AbstractRandomness;
use CryptoManana\Randomness\CryptoRandom;
use CryptoManana\Randomness\PseudoRandom;
use CryptoManana\Randomness\QuasiRandom;
use CryptoManana\Utilities\FileShredder;

/**
 * Class FileShredderTest - Tests the file shredder class.
 *
 * @package CryptoManana\Tests\IntegrationSuite\Utilities
 */
final class FileShredderTest extends AbstractIntegrationTest
{
    /**
     * Creates new instances for testing.
     *
     * @param AbstractRandomness|CryptoRandom|PseudoRandom|QuasiRandom|null $generator Randomness source.
     *
     * @return FileShredder Testing instance.
     * @throws \Exception Wrong usage errors.
     */
    private function getFileShredderForTesting($generator = null)
    {
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
     * Testing the serialization of an instance.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testSerializationCapabilities()
    {
        $shredder = $this->getFileShredderForTesting();

        $tmp = serialize($shredder);
        $tmp = unserialize($tmp);

        $this->assertEquals($shredder, $tmp);
        $this->assertNotEmpty($shredder->getRandomGenerator());

        unset($tmp);
        $this->assertNotNull($shredder);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDebugCapabilities()
    {
        $shredder = $this->getFileShredderForTesting();

        $this->assertNotEmpty(var_export($shredder, true));
    }

    /**
     * Testing the dependency injection principle realization.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testDependencyInjection()
    {
        $shredder = $this->getFileShredderForTesting();

        $this->assertTrue($shredder instanceof AbstractRandomnessInjectable);
        $this->assertTrue($shredder->getRandomGenerator() instanceof CryptoRandom);

        $shredder->setRandomGenerator(new QuasiRandom());
        $this->assertTrue($shredder->getRandomGenerator() instanceof QuasiRandom);

        $shredder->setRandomGenerator(new PseudoRandom());
        $this->assertTrue($shredder->getRandomGenerator() instanceof PseudoRandom);

        $shredder = $shredder->setRandomGenerator(new CryptoRandom())->seedRandomGenerator();
        $this->assertTrue($shredder->getRandomGenerator() instanceof CryptoRandom);
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
