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
                default:
                    $bytes = base64_encode($bytes);
                    $bytes = StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], $bytes);
                    break;
            }
        } else {
            $hexCasePattern = '/^[a-f0-9]+$/';
            $base64Pattern = '%^[a-zA-Z0-9/+]*={0,2}$%';
            $base64UrlFriendlyPattern = '/^[a-zA-Z0-9_-]+$/';

            switch ($this->cipherFormat) {
                case self::ENCRYPTION_OUTPUT_HEX_LOWER:
                case self::ENCRYPTION_OUTPUT_HEX_UPPER:
                    if (preg_match($hexCasePattern, StringBuilder::stringToLower($bytes))) {
                        $bytes = hex2bin(StringBuilder::stringToLower($bytes));
                    }
                    break;
                case self::ENCRYPTION_OUTPUT_BASE_64:
                    if (preg_match($base64Pattern, $bytes) && StringBuilder::stringLength($bytes) % 4 === 0) {
                        $bytes = base64_decode($bytes);
                    }
                    break;
                case self::ENCRYPTION_OUTPUT_BASE_64_URL:
                default:
                    if (preg_match($base64UrlFriendlyPattern, $bytes)) {
                        $bytes = StringBuilder::stringReplace(['-', '_'], ['+', '/'], $bytes);
                        $times = StringBuilder::stringLength($bytes) % 4;

                        // Instead of str_pad for encoding friendly appending
                        for ($i = 0; $i < $times; $i++) {
                            $bytes .= '=';
                        }

                        $bytes = base64_decode($bytes);
                    }
                    break;
            }
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
