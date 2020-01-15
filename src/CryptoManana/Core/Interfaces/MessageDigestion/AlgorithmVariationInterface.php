<?php

/**
 * Interface for specifying the ability to switch between different variations of a digestion algorithm.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface AlgorithmVariationInterface - Interface for switching between different variations of an algorithm.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface AlgorithmVariationInterface
{
    /**
     * The first variation setting constant.
     */
    const VERSION_ONE = 1;

    /**
     * The second variation setting constant.
     */
    const VERSION_TWO = 2;

    /**
     * Setter for the algorithm variation version property.
     *
     * @param int $version The algorithm variation version.
     *
     * @throws \Exception Validation errors.
     */
    public function setAlgorithmVariation($version);

    /**
     * Getter for the algorithm variation version property.
     *
     * @return int The algorithm variation version.
     */
    public function getAlgorithmVariation();
}
