<?php

/**
 * Interface for specifying cipher/encryption data output formats for encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface CipherDataFormatsInterface - Interface for the encryption output formats.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface CipherDataFormatsInterface
{
    /**
     * The encryption output format for a raw byte string representation.
     *
     * @internal Output Format: `�6�a`
     */
    const ENCRYPTION_OUTPUT_RAW = -1;

    /**
     * The encryption output format for a upper case HEX string representation.
     *
     * @internal Output Format: `AB45`
     */
    const ENCRYPTION_OUTPUT_HEX_UPPER = 0;

    /**
     * The encryption output format for a lower case HEX string representation.
     *
     * @internal Output Format: `7f39`
     */
    const ENCRYPTION_OUTPUT_HEX_LOWER = 1;

    /**
     * The encryption output format for a Base64 standard string representation.
     *
     * @internal Output Format: `B3xGTA==`
     */
    const ENCRYPTION_OUTPUT_BASE_64 = 2;

    /**
     * The encryption output format for a Base64 URL friendly string representation.
     *
     * @internal Output Format: `B3xGTA`
     */
    const ENCRYPTION_OUTPUT_BASE_64_URL = 3;

    /**
     * Setter for the output cipher format code property.
     *
     * @param int $cipherFormat The output cipher format code.
     *
     * @throws \Exception Validation errors.
     */
    public function setCipherFormat($cipherFormat);

    /**
     * Getter for the output cipher format code property.
     *
     * @return int The output cipher format code.
     */
    public function getCipherFormat();
}
