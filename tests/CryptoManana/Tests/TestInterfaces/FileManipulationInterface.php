<?php

/**
 * This file defines methods for temporary file manipulations used by test cases.
 */

namespace CryptoManana\Tests\TestInterfaces;

/**
 * Interface FileManipulationInterface - Interface defining temporary file manipulation methods.
 *
 * @package CryptoManana\Tests\TestInterfaces
 */
interface FileManipulationInterface
{
    /**
     * Gets a filename at the server's temporary directory path.
     *
     * Note: Leaving the filename empty will generate a secure pseudo-random one.
     *
     * @param string $filename Specify a custom filename for usage.
     *
     * @return string The absolute path to the temporary file.
     */
    public function getTemporaryFilename($filename = '');

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
    public function writeToFile($filename, $content = '');

    /**
     * Reads entire content of a file into a string.
     *
     * @param string $filename The filename for reading.
     *
     * @return string The file's content as a string.
     */
    public function readFromFile($filename);

    /**
     * Deletes a file if it exists.
     *
     * @param string $filename The filename for deletion.
     *
     * @return bool The result of the delete operation.
     */
    public function deleteTheFile($filename);
}
