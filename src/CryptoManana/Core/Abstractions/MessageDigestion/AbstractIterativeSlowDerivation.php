<?php

/**
 * Abstraction for the slow iterative derivation algorithm objects.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction as PasswordDerivation;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationSaltingInterface as DerivationSalting;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationDigestLengthInterface as DerivationDigestLength;
use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationIterationControlInterface as DerivationIterationControl;
use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as DataVerification;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationSaltingTrait as DerivationSaltingCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationDigestLengthTrait as DerivationDigestLengthCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\DerivationIterationControlTrait as IterationControlCapabilities;
use \CryptoManana\Core\Traits\MessageDigestion\SecureVerificationTrait as VerifyDataAndPasswords;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class AbstractIterativeSlowDerivation - The iterative derivation algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 *
 * @mixin DerivationSaltingCapabilities
 * @mixin DerivationDigestLengthCapabilities
 * @mixin IterationControlCapabilities
 * @mixin VerifyDataAndPasswords
 */
abstract class AbstractIterativeSlowDerivation extends PasswordDerivation implements
    DerivationSalting,
    DerivationDigestLength,
    DerivationIterationControl,
    DataVerification
{
    /**
     * Derivation data salting capabilities.
     *
     * {@internal Reusable implementation of `DerivationSaltingInterface`. }}
     */
    use DerivationSaltingCapabilities;

    /**
     * Derivation control over the outputting digest length capabilities.
     *
     * {@internal Reusable implementation of `DerivationDigestLengthInterface`. }}
     */
    use DerivationDigestLengthCapabilities;

    /**
     * Derivation internal iterations control capabilities.
     *
     * {@internal Reusable implementation of `DerivationIterationControlInterface`. }}
     */
    use IterationControlCapabilities;

    /**
     * Secure password and data verification capabilities.
     *
     * {@internal Reusable implementation of `SecureVerificationInterface`. }}
     */
    use VerifyDataAndPasswords;

    /**
     * The derivation salt string property storage.
     *
     * @var string The derivation salting string value.
     */
    protected $derivationSalt = '';

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @internal The default output size in bytes for this algorithm.
     */
    protected $outputLength = 0;

    /**
     * The derivation internal iteration count property storage.
     *
     * @var int The number of internal iterations to perform for the derivation.
     */
    protected $numberOfIterations = 1;

    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $data = $this->addSaltString($data);

        $digest = hash_pbkdf2(
            static::ALGORITHM_NAME,
            $data,
            $this->derivationSalt,
            $this->numberOfIterations,
            $this->outputLength,
            true // The format here by default is `self::DIGEST_OUTPUT_RAW`
        );

        if ($this->digestFormat !== self::DIGEST_OUTPUT_RAW) {
            $digest = bin2hex($digest);
        }

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }

    /**
     * Setter for the derivation salt string property.
     *
     * @param string $derivationSalt The derivation salt string.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setDerivationSalt($derivationSalt)
    {
        /**
         * {@internal An extra specification for the PBKDF2 digestion generation logic. }}
         */
        if (!is_string($derivationSalt) || StringBuilder::stringLength($derivationSalt) > PHP_INT_MAX - 4) {
            throw new \InvalidArgumentException(
                'The derivation salt must be of type string and be smaller than `PHP_INT_MAX - 4`.'
            );
        }

        $this->derivationSalt = $derivationSalt;

        return $this;
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
            'type' => 'key stretching or password-based key derivation',
            'salt' => $this->salt,
            'mode' => $this->saltingMode,
            'derivation salt' => $this->derivationSalt,
            'digestion output length in bytes' => $this->outputLength,
            'number of internal iterations' => $this->numberOfIterations,
        ];
    }
}
