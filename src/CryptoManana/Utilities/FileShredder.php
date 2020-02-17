<?php

/**
 * Utility class for secure file erasure.
 */

namespace CryptoManana\Utilities;

use \CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable as RandomnessContainer;
use \CryptoManana\Core\Interfaces\Randomness\FileErasureInterface as SecureFileErasure;
use \CryptoManana\Core\Traits\CommonValidations\FileNameValidationTrait as ValidateFileNames;

/**
 * Class FileShredder - Utility class for file shredding.
 *
 * @package CryptoManana\Utilities
 *
 * @property \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator $randomnessSource The randomness generator.
 */
class FileShredder extends RandomnessContainer implements SecureFileErasure
{
    /**
     * File name and path validations.
     *
     * {@internal Reusable implementation of the common file name validation. }}
     */
    use ValidateFileNames;

    /**
     * Internal method for calculating the file size in bytes.
     *
     * @param string $filename The filename and location.
     *
     * @return int The file size in bytes.
     * @throws \Exception Validation errors.
     */
    protected function calculateFileSize($filename)
    {
        $sizeInBytes = filesize($filename);

        // @codeCoverageIgnoreStart
        if ($sizeInBytes === false) {
            throw new \RuntimeException('The operating system could not obtain the correct size of the file.');
        }
        // @codeCoverageIgnoreEnd

        return $sizeInBytes;
    }

    /**
     * Internal method for shredding the physical file based on the DOD 5220.22-M (3 passes) standard.
     *
     * @param string $filename The filename and location.
     *
     * @return bool The file shredding operation result.
     * @throws \Exception Validation or filesystem errors.
     */
    protected function fileShredding($filename)
    {
        $sizeInBytes = $this->calculateFileSize($filename);

        $written = true;

        for ($i = 1; $i <= 3; $i++) {
            for ($j = 0; $j < $sizeInBytes; $j++) {
                $flag = ($j === 0) ? LOCK_EX : (FILE_APPEND | LOCK_EX);

                if ($i === 1) {
                    $written = file_put_contents($filename, "\x0", $flag);
                } elseif ($i === 2) {
                    $written = file_put_contents($filename, "\x1", $flag);
                } elseif ($i === 3) {
                    $written = file_put_contents($filename, $this->randomnessSource->getBytes(1), $flag);
                }

                // @codeCoverageIgnoreStart
                if ($written === false) {
                    throw new \RuntimeException('Problem with writing to the filesystem');
                }
                // @codeCoverageIgnoreEnd
            }

            // Sleep for 15 milliseconds for the disk verify the written data and sync buffers
            usleep(15000);
        }

        $written = file_put_contents($filename, '', LOCK_EX);

        return (unlink($filename) === true && $written !== false);
    }

    /**
     * Data erasure and wiping of a file.
     *
     * @param string $filename The filename and location.
     *
     * @throws \Exception Validation errors.
     *
     * @internal Files that are bigger than 2 GB may not be deletable or behave irrationally for x86 platforms.
     */
    public function eraseFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        // @codeCoverageIgnoreStart
        if ($this->fileShredding($filename) === false) {
            throw new \RuntimeException('Failed to delete the file from the filesystem.');
        }
        // @codeCoverageIgnoreEnd
    }
}
