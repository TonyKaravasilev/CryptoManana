<?php

/**
 * Interface for specifying file erasure capabilities.
 */

namespace CryptoManana\Core\Interfaces\Containers;

/**
 * Interface FileErasureInterface - Interface for file erasure capabilities.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface FileErasureInterface
{
    /**
     * Data erasure and wiping of a file.
     *
     * @param string $filename The filename and location.
     *
     * @throws \Exception Validation errors.
     */
    public function eraseFile($filename);
}
