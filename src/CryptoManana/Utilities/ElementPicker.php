<?php

/**
 * Utility class for random element picking.
 */

namespace CryptoManana\Utilities;

use \CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable as RandomnessContainer;
use \CryptoManana\Core\Interfaces\Randomness\ElementPickingInterface as ElementChoosing;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class ElementPicker - Utility class for random element picking.
 *
 * @package CryptoManana\Utilities
 */
class ElementPicker extends RandomnessContainer implements ElementChoosing
{
    /**
     * Pick a random character from string.
     *
     * @param string $string The string with characters for choosing from.
     *
     * @return string The chosen character string.
     */
    public function pickCharacterElement($string = '')
    {
        // Validate input
        if (!is_string($string)) {
            throw new \InvalidArgumentException('The supplied argument is not of type string.');
        }

        if (empty($string)) {
            return $string;
        }

        // Convert the string to an array
        $array = StringBuilder::stringSplit($string, 1);

        // Reuse the code for array element choosing
        return static::pickArrayElement($array);
    }

    /**
     * Pick a random element from array.
     *
     * @param array $array The array with elements for choosing from.
     *
     * @return mixed The chosen element from the array.
     */
    public function pickArrayElement(array $array = [])
    {
        // Validate input
        if (empty($array)) {
            return $array;
        }

        $count = count($array);

        $iterator = 0;

        // Choose random index
        $elementIndex = ($count === 1) ? 0 : $this->randomnessSource->getInt(0, $count - 1);

        // Reset pointer to begging
        reset($array);

        $tmpElement = null;

        // Iterate through array elements
        foreach ($array as $keyName => $value) {
            // If element is found
            if ($elementIndex == $iterator) {
                $tmpElement = $value;

                break;
            }

            // Update the index
            $iterator++;
        }

        // Return the chosen element
        return $tmpElement;
    }
}
