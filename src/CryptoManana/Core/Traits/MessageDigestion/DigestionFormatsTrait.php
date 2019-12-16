<?php

/**
 * Trait implementation of the hashing output formats for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\DigestionFormatsInterface as DigestionFormatsSpecification;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait DigestionFormatsTrait - Reusable implementation of `DigestionFormatsInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DigestionFormatsInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $digestFormat The digest output format property storage.
 *
 * @mixin DigestionFormatsSpecification
 */
trait DigestionFormatsTrait
{
    /**
     * Internal method for converting the digest's output format representation via the chosen format.
     *
     * @param string $digest The output digest.
     *
     * @return string The input data with proper salting.
     */
    protected function changeOutputFormat($digest)
    {
        switch ($this->digestFormat) {
            case self::DIGEST_OUTPUT_HEX_LOWER:
                break; // Comes already in lower, can be skipped
            case self::DIGEST_OUTPUT_HEX_UPPER:
                $digest = StringBuilder::stringToUpper($digest);
                break;
            case self::DIGEST_OUTPUT_BASE_64:
                $digest = base64_encode(pack('H*', $digest));
                break;
            case self::DIGEST_OUTPUT_BASE_64_URL:
                $digest = base64_encode(pack('H*', $digest));
                $digest = StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], $digest);
                break;
            default: // case self::DIGEST_OUTPUT_RAW:
                break;
        }

        return $digest;
    }

    /**
     * Setter for the digest format code property.
     *
     * @param int $digestFormat The digest format code.
     *
     * @return $this The hash algorithm object.
     * @throw \Exception Validation errors.
     */
    public function setDigestFormat($digestFormat)
    {
        $digestFormat = filter_var(
            $digestFormat,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::DIGEST_OUTPUT_RAW,
                    "max_range" => self::DIGEST_OUTPUT_BASE_64_URL,
                ],
            ]
        );

        if ($digestFormat === false) {
            throw new \InvalidArgumentException('Digest output format mode must be an integer between -1 and 3.');
        }

        $this->digestFormat = $digestFormat;

        return $this;
    }

    /**
     * Getter for the digest format code property.
     *
     * @return int The digest format code.
     */
    public function getDigestFormat()
    {
        return $this->digestFormat;
    }
}
