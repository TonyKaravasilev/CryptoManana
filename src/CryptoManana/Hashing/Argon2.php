<?php

/**
 * The Argon2 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHardwareResistantDerivation as StrongDerivationAlgorithm;
use CryptoManana\Core\Interfaces\MessageDigestion\ComplexAlgorithmicCostInterface as ComplexAlgorithmicCostTuning;
use CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmVariationInterface as AlgorithmVariationSwitching;
use CryptoManana\Core\Traits\MessageDigestion\ComplexAlgorithmicCostTrait as ComplexAlgorithmicCostTuningCapabilities;
use CryptoManana\Core\Traits\MessageDigestion\AlgorithmVariationTrait as AlgorithmVariationSwitchingCapabilities;

/**
 * Class Argon2 - The Argon2 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 *
 * @mixin ComplexAlgorithmicCostTuningCapabilities
 *
 * @codeCoverageIgnore
 */
class Argon2 extends StrongDerivationAlgorithm implements ComplexAlgorithmicCostTuning, AlgorithmVariationSwitching
{
    /**
     * Algorithmic cost tuning complex capabilities.
     *
     * {@internal Reusable implementation of `ComplexAlgorithmicCostInterface`. }}
     */
    use ComplexAlgorithmicCostTuningCapabilities;

    /**
     * Algorithm variation switching capabilities.
     *
     * {@internal Reusable implementation of `AlgorithmVariationInterface`. }}
     */
    use AlgorithmVariationSwitchingCapabilities;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'argon2';

    /**
     * The internal maximum length in bytes of the raw output digest for the algorithm.
     *
     * @internal For the current algorithm: `PHP_INT_MAX`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = PHP_INT_MAX;

    /**
     * The Argon2i variation setting constant.
     *
     * @see AlgorithmVariationSwitching::VERSION_ONE For internal value.
     *
     * @internal Same as `Argon2::VERSION_ONE`.
     */
    const VERSION_I = 1;

    /**
     * The Argon2id variation setting constant.
     *
     * @see AlgorithmVariationSwitching::VERSION_TWO For internal value.
     *
     * @internal Same as `Argon2::VERSION_TWO`.
     */
    const VERSION_ID = 2;

    /**
     * The internal algorithm variation property storage.
     *
     * @var int The algorithm variation value.
     */
    protected $algorithmVariation = self::VERSION_I;

    /**
     * The digestion internal computational memory cost property storage.
     *
     * @var int The algorithmic memory cost value.
     */
    protected $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST;

    /**
     * The digestion internal computational time cost property storage.
     *
     * @var int The algorithmic time cost value.
     */
    protected $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST;

    /**
     * The digestion internal computational thread cost property storage.
     *
     * @var int The algorithmic thread cost value.
     */
    protected $threadsCost = PASSWORD_ARGON2_DEFAULT_THREADS;


    /**
     * Fetch the correctly formatted internal variation for digestion.
     *
     * @return int|string The chosen variation for password hashing.
     */
    protected function fetchAlgorithmVariation()
    {
        return ($this->algorithmVariation === self::VERSION_ID) ? PASSWORD_ARGON2ID : PASSWORD_ARGON2I;
    }

    /**
     * Fetch the correctly formatted internal parameters for digestion.
     *
     * @return array The chosen parameters for password hashing.
     */
    protected function fetchAlgorithmParameters()
    {
        return [
            'memory_cost' => $this->memoryCost,
            'time_cost ' => $this->timeCost,
            'threads' => $this->threadsCost,
        ];
    }

    /**
     * Internal method for version range validation.
     *
     * @param int $version The version for validation checks.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateVersion($version)
    {
        if (!in_array(($version === self::VERSION_ID) ? PASSWORD_ARGON2ID : PASSWORD_ARGON2I, password_algos(), true)) {
            throw new \RuntimeException(
                'The Argon2 algorithm variation is not supported under the current system configuration.'
            );
        }
    }

    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!in_array(PASSWORD_ARGON2I, password_algos(), true)) {
            throw new \RuntimeException(
                'The Argon2 algorithm is not supported under the current system configuration.'
            );
        }
    }
}
