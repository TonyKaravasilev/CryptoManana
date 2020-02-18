<?php

/**
 * Trait implementation of the signature data output formats for signature algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\SignatureDataFormatsInterface as SignatureDataFormatsSpecification;
use CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait SignatureDataFormatsTrait - Reusable implementation of `SignatureDataFormatsInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\SignatureDataFormatsInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property int $signatureFormat The output signature format property storage.
 *
 * @mixin SignatureDataFormatsSpecification
 */
trait SignatureDataFormatsTrait
{
    /**
     * Internal method for converting format after signing operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function signingFormat(&$bytes)
    {
        switch ($this->signatureFormat) {
            case self::SIGNATURE_OUTPUT_HEX_LOWER:
                $bytes = bin2hex($bytes);
                break;
            case self::SIGNATURE_OUTPUT_HEX_UPPER:
                $bytes = StringBuilder::stringToUpper(bin2hex($bytes));
                break;
            case self::SIGNATURE_OUTPUT_BASE_64:
                $bytes = base64_encode($bytes);
                break;
            case self::SIGNATURE_OUTPUT_BASE_64_URL:
                $bytes = StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], base64_encode($bytes));
                break;
        }
    }

    /**
     * Internal method for converting from HEX formatted string after verification operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function verificationFormatHex(&$bytes)
    {
        if (preg_match('/^[a-f0-9]+$/', $bytes)) {
            $bytes = hex2bin($bytes);
        } elseif (preg_match('/^[A-F0-9]+$/', $bytes)) {
            $bytes = hex2bin(StringBuilder::stringToLower($bytes));
        }
    }

    /**
     * Internal method for converting from Base64 formatted string after verification operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function verificationFormatBase64(&$bytes)
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
     * Internal method for converting format after verification operations.
     *
     * @param string $bytes The bytes for conversion.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function verificationFormat(&$bytes)
    {
        $isHex = in_array(
            $this->signatureFormat,
            [self::SIGNATURE_OUTPUT_HEX_LOWER, self::SIGNATURE_OUTPUT_HEX_UPPER],
            true
        );

        $isBase64 = in_array(
            $this->signatureFormat,
            [self::SIGNATURE_OUTPUT_BASE_64, self::SIGNATURE_OUTPUT_BASE_64_URL],
            true
        );

        if ($isHex) {
            $this->verificationFormatHex($bytes);
        } elseif ($isBase64) {
            $this->verificationFormatBase64($bytes);
        }
    }

    /**
     * Internal method for converting the output format representation via the chosen format.
     *
     * @param string $bytes The bytes for conversion.
     * @param bool|int|null $direction Flag for signing direction (sign => `true` or verify => `false`).
     *
     * @return string The formatted bytes.
     */
    protected function changeOutputFormat($bytes, $direction = true)
    {
        if ($this->signatureFormat === self::SIGNATURE_OUTPUT_RAW) {
            return $bytes;
        }

        if ($direction == true) {
            $this->signingFormat($bytes);
        } else {
            $this->verificationFormat($bytes);
        }

        return $bytes;
    }

    /**
     * Setter for the output signature format code property.
     *
     * @param int $signatureFormat The output signature format code.
     *
     * @return $this The signature algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setSignatureFormat($signatureFormat)
    {
        $signatureFormat = filter_var(
            $signatureFormat,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::SIGNATURE_OUTPUT_RAW,
                    "max_range" => self::SIGNATURE_OUTPUT_BASE_64_URL,
                ],
            ]
        );

        if ($signatureFormat === false) {
            throw new \InvalidArgumentException(
                'The output format mode must be an integer between ' .
                self::SIGNATURE_OUTPUT_RAW . ' and ' . self::SIGNATURE_OUTPUT_BASE_64_URL . '.'
            );
        }

        $this->signatureFormat = $signatureFormat;

        return $this;
    }

    /**
     * Getter for the output signature format code property.
     *
     * @return int The output signature format code.
     */
    public function getSignatureFormat()
    {
        return $this->signatureFormat;
    }
}
