<?php

/**
 * Interface for specifying extra Red-Green-Blue (RGB) colour format output formats for pseudo-random generators.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface RgbOutputInterface - Interface for random RGB colour generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface RgbOutputInterface
{
    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using all colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     */
    public function getRgbColourPair($toArray = true);

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only greyscale colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     */
    public function getRgbGreyscalePair($toArray = true);

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only black&white colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     */
    public function getRgbBlackOrWhitePair($toArray = true);
}
