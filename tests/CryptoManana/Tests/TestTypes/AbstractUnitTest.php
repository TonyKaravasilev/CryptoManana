<?php

/**
 * This file defines the main unit test case abstraction class.
 */

namespace CryptoManana\Tests\TestTypes;

use \PHPUnit\Framework\TestCase as FrameworkUnitTest;
use \CryptoManana\Tests\TestInterfaces\UnitTestDebuggingInterface as DataDumping;
use \CryptoManana\Tests\TestInterfaces\FileManipulationInterface as TemporaryFileManipulation;

/**
 * Class AbstractUnitTest - Main class for unit test case creation.
 *
 * @package CryptoManana\Tests\TestTypes
 */
abstract class AbstractUnitTest extends FrameworkUnitTest implements DataDumping, TemporaryFileManipulation
{
    /**
     * Dump the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function dump($data)
    {
        dump($data);
    }

    /**
     * Stop execution after dumping the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function stop($data)
    {
        dd($data);
    }

    /**
     * Throw an exception with error message.
     *
     * @param mixed $message The error message.
     *
     * @throws \RuntimeException Custom error happened.
     */
    public function error($message = 'Oops')
    {
        $message = is_string($message) ? $message : 'Test Error!';

        throw new \RuntimeException($message);
    }

    /**
     * Sleep execution for some time (1 second = 1000 milliseconds).
     *
     * @param int $milliSeconds Sleep time in milliseconds.
     */
    public function sleep($milliSeconds = 1000)
    {
        $milliSeconds = is_int($milliSeconds) ? abs($milliSeconds) * 1000 : 1000;

        usleep($milliSeconds);
    }

    /**
     * Gets a filename at the server's temporary directory path.
     *
     * Note: Leaving the filename empty will generate a secure pseudo-random one.
     *
     * @param string $filename Specify a custom filename for usage.
     *
     * @return string The absolute path to the temporary file.
     */
    public function getTemporaryFilename($filename = '')
    {
        $path = sys_get_temp_dir();

        if (is_string($filename) && !empty($filename)) {
            $filename = str_replace([" ", "\n", "\r", "\x09", "\x0B", "\0"], '', $filename);

            $filename = str_replace(['..', '-', '*'], ['.', '_', ''], $filename);

            return $path . DIRECTORY_SEPARATOR . $filename;
        }

        return $path . DIRECTORY_SEPARATOR . strtoupper(bin2hex(openssl_random_pseudo_bytes(5)));
    }

    /**
     * Writes string content to a file.
     *
     * Note: Creates the file if it does not already exist.
     *
     * @param string $filename The filename for usage.
     * @param string $content The content string for writing.
     *
     * @return bool The result of the write operation.
     */
    public function writeToFile($filename, $content = '')
    {
        if (is_string($filename) && !empty($filename) && is_string($content)) {
            $filename = str_replace("\0", '', $filename); // (ASCII 0 (0x00))

            return is_writable($filename) && (@file_put_contents($filename, $content, LOCK_EX) !== false);
        }

        return false;
    }

    /**
     * Reads entire content of a file into a string.
     *
     * @param string $filename The filename for reading.
     *
     * @return string The file's content as a string.
     */
    public function readFromFile($filename)
    {
        if (is_string($filename) && !empty($filename)) {
            $filename = str_replace("\0", '', $filename);

            $filename = realpath($filename);

            if (!empty($filename)) {
                clearstatcache(true, $filename);
            }

            if ($filename === false || !file_exists($filename) || !is_readable($filename) || !is_file($filename)) {
                return '';
            }

            return (string)@file_get_contents($filename);
        }

        return '';
    }

    /**
     * Deletes a file if it exists.
     *
     * @param string $filename The filename for deletion.
     *
     * @return bool The result of the delete operation.
     */
    public function deleteTheFile($filename)
    {
        if (is_string($filename) && !empty($filename)) {
            $filename = str_replace("\0", '', $filename);
            $filename = realpath($filename);

            if (!empty($filename)) {
                clearstatcache(true, $filename);
            }

            if ($filename === false || !file_exists($filename) || !is_readable($filename) || !is_file($filename)) {
                return false;
            }

            return @unlink($filename);
        }

        return false;
    }
}
