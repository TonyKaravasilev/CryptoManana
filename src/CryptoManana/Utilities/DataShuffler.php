<?php

/**
 * Utility class for shuffling information.
 */

namespace CryptoManana\Utilities;

use \CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable as RandomnessContainer;
use \CryptoManana\Core\Interfaces\Randomness\DataShufflingInterface as Shuffling;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class DataShuffler - Utility class for data shuffling.
 *
 * @package CryptoManana\Utilities
 */
class DataShuffler extends RandomnessContainer implements Shuffling
{
    /**
     * Shuffle a string.
     *
     * @param string $string The string for shuffling.
     *
     * @return string The output shuffled/scrambled string.
     */
    public function shuffleString($string = '')
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

        // Reuse the code for array shuffling and convert result to string
        return implode('', static::shuffleArray($array));
    }

    /**
     * Shuffle an array.
     *
     * @param array $array The array for shuffling.
     *
     * @return array The output shuffled/scrambled array.
     */
    public function shuffleArray(array $array = [])
    {
        // Validate input
        if (empty($array)) {
            return $array;
        }

        $shuffled = [];
        $iterator = 0;

        // Reset pointer to the begging
        reset($array);

        // Iterate through array elements
        foreach ($array as $keyName => $value) {
            // Choose random index
            $newIndex = ($iterator === 0) ? 0 : $this->randomnessSource->getInt(0, $iterator);

            // Shuffling logic
            if ($newIndex === $iterator) {
                $shuffled[] = $value;
            } else {
                $shuffled[] = $shuffled[$newIndex];
                $shuffled[$newIndex] = $value;
            }

            // Update the index
            $iterator++;
        }

        // Return the shuffled array
        return $shuffled;
    }
}
