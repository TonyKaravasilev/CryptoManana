<?php

/**
 * Trait implementation of the cipher/encryption data output formats for encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\CipherDataFormatsInterface as CipherDataFormatsSpecification;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait CipherDataFormatsTrait - Reusable implementation of `CipherDataFormatsInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\CipherDataFormatsInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property int $cipherFormat The output cipher format property storage.
 *
 * @mixin CipherDataFormatsSpecification
 */
trait CipherDataFormatsTrait
{
    /**
     * Internal method for converting format after encryption operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function encryptionFormat(&$bytes)
    {
        switch ($this->cipherFormat) {
            case self::ENCRYPTION_OUTPUT_HEX_LOWER:
                $bytes = bin2hex($bytes);
                break;
            case self::ENCRYPTION_OUTPUT_HEX_UPPER:
                $bytes = StringBuilder::stringToUpper(bin2hex($bytes));
                break;
            case self::ENCRYPTION_OUTPUT_BASE_64:
                $bytes = base64_encode($bytes);
                break;
            case self::ENCRYPTION_OUTPUT_BASE_64_URL:
                $bytes = StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], base64_encode($bytes));
                break;
        }
    }

    /**
     * Internal method for converting from HEX formatted string after decryption operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function decryptionFormatHex(&$bytes)
    {
        if (preg_match('/^[a-f0-9]+$/', $bytes)) {
            $bytes = hex2bin($bytes);
        } elseif (preg_match('/^[A-F0-9]+$/', $bytes)) {
            $bytes = hex2bin(StringBuilder::stringToLower($bytes));
        }
    }

    /**
     * Internal method for converting from Base64 formatted string after decryption operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function decryptionFormatBase64(&$bytes)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $bytes) && StringBuilder::stringLength($bytes) % 4 === 0) {
            $bytes = base64_decode($bytes);
        } elseif (preg_match('/^[a-zA-Z0-9_-]+$/', $bytes)) {
            $bytes = StringBuilder::stringReplace(['-', '_'], ['+', '/'], $bytes);
            $bytes .= str_repeat('=', StringBuilder::stringLength($bytes) % 4);
            $bytes = base64_decode($bytes);
        }
    }

    /**
     * Internal method for converting format after decryption operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function decryptionFormat(&$bytes)
    {
        $isHex = in_array(
            $this->cipherFormat,
            [self::ENCRYPTION_OUTPUT_HEX_LOWER, self::ENCRYPTION_OUTPUT_HEX_UPPER],
            true
        );

        $isBase64 = in_array(
            $this->cipherFormat,
            [self::ENCRYPTION_OUTPUT_BASE_64, self::ENCRYPTION_OUTPUT_BASE_64_URL],
            true
        );

        if ($isHex) {
            $this->decryptionFormatHex($bytes);
        } elseif ($isBase64) {
            $this->decryptionFormatBase64($bytes);
        }
    }

    /**
     * Internal method for converting the output format representation via the chosen format.
     *
     * @param string $bytes The bytes for conversion.
     * @param bool|int|null $direction Flag for encryption direction (encrypt => `true` or decrypt => `false`).
     *
     * @return string The formatted bytes.
     */
    protected function changeOutputFormat($bytes, $direction = true)
    {
        if ($this->cipherFormat === self::ENCRYPTION_OUTPUT_RAW) {
            return $bytes;
        }

        if ($direction == true) {
            $this->encryptionFormat($bytes);
        } else {
            $this->decryptionFormat($bytes);
        }

        return $bytes;
    }

    /**
     * Setter for the output cipher format code property.
     *
     * @param int $cipherFormat The output cipher format code.
     *
     * @return $this The encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setCipherFormat($cipherFormat)
    {
        $cipherFormat = filter_var(
            $cipherFormat,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::ENCRYPTION_OUTPUT_RAW,
                    "max_range" => self::ENCRYPTION_OUTPUT_BASE_64_URL,
                ],
            ]
        );

        if ($cipherFormat === false) {
            throw new \InvalidArgumentException(
                'The output format mode must be an integer between ' .
                self::ENCRYPTION_OUTPUT_RAW . ' and ' . self::ENCRYPTION_OUTPUT_BASE_64_URL . '.'
            );
        }

        $this->cipherFormat = $cipherFormat;

        return $this;
    }

    /**
     * Getter for the output cipher format code property.
     *
     * @return int The output cipher format code.
     */
    public function getCipherFormat()
    {
        return $this->cipherFormat;
    }
}
