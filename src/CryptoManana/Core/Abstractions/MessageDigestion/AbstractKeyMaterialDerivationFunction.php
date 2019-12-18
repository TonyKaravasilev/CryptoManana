<?php

/**
 * Abstraction for key stretching and key derivation objects like the HKDF algorithm.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction as KeyStretchingAlgorithm;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationSaltingInterface as DerivationSalting;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationContextInterface as DerivationContext;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationDigestLengthInterface as DerivationDigestLength;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationSaltingTrait as DerivationSaltingCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationContextTrait as DerivationContextualCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationDigestLengthTrait as DerivationDigestLengthCapabilities;

/**
 * Class AbstractKeyMaterialDerivationFunction - Abstraction for output key material derivation classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin DerivationSaltingCapabilities
 * @mixin DerivationContextualCapabilities
 * @mixin DerivationDigestLengthCapabilities
 */
abstract class AbstractKeyMaterialDerivationFunction extends KeyStretchingAlgorithm implements
    DerivationSalting,
    DerivationContext,
    DerivationDigestLength
{
    /**
     * Derivation data salting capabilities.
     *
     * {@internal Reusable implementation of `DerivationSaltingInterface`. }}
     */
    use DerivationSaltingCapabilities;

    /**
     * Derivation application/context information salting capabilities.
     *
     * {@internal Reusable implementation of `DerivationContextInterface`. }}
     */
    use DerivationContextualCapabilities;

    /**
     * Derivation derivation control over the outputting digest length capabilities.
     *
     * {@internal Reusable implementation of `DerivationDigestLengthInterface`. }}
     */
    use DerivationDigestLengthCapabilities;

    /**
     * The derivation salt string property storage.
     *
     * @var string The derivation salting string value.
     */
    protected $derivationSalt = '';

    /**
     * The derivation context/application information string property storage.
     *
     * @var string The derivation context/application information string value.
     */
    protected $contextualString = '';

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     */
    protected $outputLength = 0;

    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashData($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data for hashing must be a string or a binary string.');
        }

        $data = $this->addSaltString(($data === '') ? ' ' : $data);

        $digest = hash_hkdf(
            static::ALGORITHM_NAME,
            $data,
            $this->outputLength,
            $this->contextualString,
            $this->derivationSalt
        ); // The format here by default is `self::DIGEST_OUTPUT_RAW`

        if ($this->digestFormat !== self::DIGEST_OUTPUT_RAW) {
            $digest = bin2hex($digest);
        }

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'standard' => static::ALGORITHM_NAME,
            'type' => 'key stretching or key material derivation',
            'salt' => $this->salt,
            'mode' => $this->saltingMode,
            'derivation salt' => $this->derivationSalt,
            'context information string' => $this->contextualString,
            'digestion output length in bytes' => $this->outputLength,
        ];
    }
}
