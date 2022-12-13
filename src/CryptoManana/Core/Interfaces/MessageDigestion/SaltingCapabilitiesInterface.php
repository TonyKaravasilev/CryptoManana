<?php

/**
 * Interface for specifying salting capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface SaltingCapabilitiesInterface - Interface for salting capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface SaltingCapabilitiesInterface
{
    /**
     * The mode to disable salting.
     *
     * @note Mode Logic: Salting is disabled.
     */
    const SALTING_MODE_NONE = -1;

    /**
     * The mode to append the salt string (after the data).
     *
     * @note Mode Logic: `dataSALT`
     */
    const SALTING_MODE_APPEND = 0;

    /**
     * The mode to prepend the salt string (before the data).
     *
     * @note Mode Logic: `SALTdata`
     */
    const SALTING_MODE_PREPEND = 1;

    /**
     * The mode to infix the salt string (around the data).
     *
     * @note Mode Logic: `SALTdataTLAS`
     */
    const SALTING_MODE_INFIX_INPUT = 2;

    /**
     * The mode to infix the data string (around the salt).
     *
     * @note Mode Logic: `dataSALTatad`
     */
    const SALTING_MODE_INFIX_SALT = 3;

    /**
     * The mode to reverse and append the salt string (after the data).
     *
     * @note Mode Logic: `dataTLAS`
     */
    const SALTING_MODE_REVERSE_APPEND = 4;

    /**
     * The mode to reverse and prepend the salt string (before the data).
     *
     * @note Mode Logic: `TLASdata`
     */
    const SALTING_MODE_REVERSE_PREPEND = 5;

    /**
     * The mode to create a suffix via reversing the salt string and concatenating it to its original value.
     *
     * @note Mode Logic: `dataSALTTLAS`
     */
    const SALTING_MODE_DUPLICATE_SUFFIX = 6;

    /**
     * The mode to create a prefix via reversing the salt string and concatenating it to its original value.
     *
     * @note Mode Logic: `SALTTLASdata`
     */
    const SALTING_MODE_DUPLICATE_PREFIX = 7;

    /**
     * The mode to use the salting string and the input data to create a palindrome string.
     *
     * @note Mode Logic: `SALTdataatadTLAS`
     */
    const SALTING_MODE_PALINDROME_MIRRORING = 8;

    /**
     * Setter for the salt string property.
     *
     * @param string $salt The salt string.
     *
     * @throws \Exception Validation errors.
     */
    public function setSalt($salt);

    /**
     * Getter for the salt string property.
     *
     * @return string The salt string.
     */
    public function getSalt();

    /**
     * Setter for the salting mode code property.
     *
     * @param int $saltingMode The salting mode code.
     *
     * @throws \Exception Validation errors.
     */
    public function setSaltingMode($saltingMode);

    /**
     * Getter for the salt mode code property.
     *
     * @return int The salt mode code.
     */
    public function getSaltingMode();
}
