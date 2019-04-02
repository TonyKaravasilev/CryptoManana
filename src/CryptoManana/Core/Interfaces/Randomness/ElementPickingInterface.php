<?php

/**
 * Interface for specifying element picking/choosing capabilities.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface ElementPickingInterface - Interface for element picking capabilities.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface ElementPickingInterface
{
    /**
     * Pick a random character from string.
     *
     * @param string $string The string with characters for choosing from.
     *
     * @return string The chosen character string.
     */
    public function pickCharacterElement($string = '');

    /**
     * Pick a random element from array.
     *
     * @param array $array The array with elements for choosing from.
     *
     * @return mixed The chosen element from the array.
     */
    public function pickArrayElement(array $array = []);
}
