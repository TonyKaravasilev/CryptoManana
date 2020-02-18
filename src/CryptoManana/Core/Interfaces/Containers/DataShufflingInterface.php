<?php

/**
 * Interface for specifying data shuffling capabilities.
 */

namespace CryptoManana\Core\Interfaces\Containers;

/**
 * Interface DataShufflingInterface - Interface for data shuffling capabilities.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface DataShufflingInterface
{
    /**
     * Shuffle a string.
     *
     * @param string $string The string for shuffling.
     *
     * @return string The output shuffled/scrambled string.
     */
    public function shuffleString($string = '');

    /**
     * Shuffle an array.
     *
     * @param array $array The array for shuffling.
     *
     * @return array The output shuffled/scrambled array.
     */
    public function shuffleArray(array $array = []);
}
