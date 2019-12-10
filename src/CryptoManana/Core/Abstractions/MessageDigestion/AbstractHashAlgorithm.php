<?php

/**
 * The hash algorithm abstraction specification.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\SaltingCapabilitiesInterface as DataSalting;
use \CryptoManana\Core\Interfaces\MessageDigestion\DigestionFormatsInterface as DigestFormatting;
use \CryptoManana\Core\Traits\MessageDigestion\SaltingCapabilitiesTrait as SaltingCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\DigestionFormatsTrait as DigestOutputFormats;

/**
 * Class AbstractHashAlgorithm - The hash algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin SaltingCapabilities
 * @mixin DigestOutputFormats
 */
abstract class AbstractHashAlgorithm implements DataSalting, DigestFormatting
{
    /**
     * Data salting capabilities.
     *
     * {@internal Reusable implementation of `SaltingCapabilitiesInterface`. }}
     */
    use SaltingCapabilities;

    /**
     * Digest outputting formats.
     *
     * {@internal Reusable implementation of `DigestionFormatsInterface`. }}
     */
    use DigestOutputFormats;

    /**
     * The salt string property storage.
     *
     * @var string The salting string value.
     */
    protected $salt = '';

    /**
     * The salting mode property storage.
     *
     * @var int The salting mode integer code value.
     */
    protected $saltingMode = self::SALTING_MODE_APPEND;

    /**
     * The digest output format property storage.
     *
     * @var int The output format integer code value.
     */
    protected $digestFormat = self::DIGEST_OUTPUT_HEX_UPPER;

    /**
     * Hash algorithm constructor.
     */
    abstract public function __construct();

    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    abstract public function hashData($data);
}
