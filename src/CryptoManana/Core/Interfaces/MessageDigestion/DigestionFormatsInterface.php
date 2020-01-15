<?php

/**
 * Interface for specifying hashing output formats for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DigestionFormatsInterface - Interface for hashing output formats.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DigestionFormatsInterface
{
    /**
     * The digest output format for a raw byte string representation.
     *
     * @internal Output Format: `�5�a`
     */
    const DIGEST_OUTPUT_RAW = -1;

    /**
     * The digest output format for a upper case HEX string representation.
     *
     * @internal Output Format: `DB35`
     */
    const DIGEST_OUTPUT_HEX_UPPER = 0;

    /**
     * The digest output format for a lower case HEX string representation.
     *
     * @internal Output Format: `8f36`
     */
    const DIGEST_OUTPUT_HEX_LOWER = 1;

    /**
     * The digest output format for a Base64 standard string representation.
     *
     * @internal Output Format: `C3gGTA==`
     */
    const DIGEST_OUTPUT_BASE_64 = 2;

    /**
     * The digest output format for a Base64 URL friendly string representation.
     *
     * @internal Output Format: `C3gGTA`
     */
    const DIGEST_OUTPUT_BASE_64_URL = 3;

    /**
     * Setter for the digest format code property.
     *
     * @param int $digestFormat The digest format code.
     *
     * @throws \Exception Validation errors.
     */
    public function setDigestFormat($digestFormat);

    /**
     * Getter for the digest format code property.
     *
     * @return int The digest format code.
     */
    public function getDigestFormat();
}
