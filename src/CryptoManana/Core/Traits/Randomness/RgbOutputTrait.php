<?php

/**
 * Trait implementation of Red-Green-Blue (RGB) colour format generation for generator services.
 */

namespace CryptoManana\Core\Traits\Randomness;

/**
 * Trait RgbOutputTrait - Reusable implementation of `RgbOutputInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Randomness\RgbOutputInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Randomness
 */
trait RgbOutputTrait
{
    /**
     * Internal method for converting RGB integer colours to HEX notations.
     *
     * @param array $rgbColour An array containing three integers between 0 and 255.
     *
     * @return string The HEX representation of the RGB colour pair.
     */
    protected function calculateRgbArrayToHexString(array $rgbColour)
    {
        $pairCount = count($rgbColour);

        for ($i = 0; $i < $pairCount; $i++) {
            $rgbColour[$i] = ($rgbColour[$i] <= 15) ? '0' . dechex($rgbColour[$i]) : dechex($rgbColour[$i]);
        }

        return ($pairCount > 0) ? '#' . implode('', $rgbColour) : '';
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using all colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbColourPair($toArray = true)
    {
        $rgb = [$this->getInt(0, 255), $this->getInt(0, 255), $this->getInt(0, 255)];

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only greyscale colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbGreyscalePair($toArray = true)
    {
        $grayChart = [
            [255, 255, 255], // white
            [220, 220, 220], // gainsboro
            [211, 211, 211], // lightgrey
            [192, 192, 192], // silver
            [169, 169, 169], // darkgray
            [128, 128, 128], // gray
            [105, 105, 105], // dimgray
            [0, 0, 0] // black
        ];

        $rgb = $grayChart[$this->getInt(0, 255) % 8]; // A bit faster

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only black&white colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbBlackOrWhitePair($toArray = true)
    {
        $rgb = $this->getBool() ? [0, 0, 0] : [255, 255, 255];

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }
}
