<?php

/**
 * The Bcrypt hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHardwareResistantDerivation as StrongDerivationAlgorithm;
use CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmicCostInterface as AlgorithmicCostTuning;
use CryptoManana\Core\Traits\MessageDigestion\AlgorithmicCostTrait as AlgorithmicCostTuningCapabilities;

/**
 * Class Bcrypt - The Bcrypt hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 *
 * @mixin AlgorithmicCostTuningCapabilities
 */
class Bcrypt extends StrongDerivationAlgorithm implements AlgorithmicCostTuning
{
    /**
     * Algorithmic cost tuning capabilities.
     *
     * {@internal Reusable implementation of `AlgorithmicCostInterface`. }}
     */
    use AlgorithmicCostTuningCapabilities;

    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'bcrypt';

    /**
     * The internal maximum length in bytes of the raw output digest for the algorithm.
     *
     * @note For the current algorithm: `72`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 72;

    /**
     * The digestion internal computational cost property storage.
     *
     * @var int The algorithmic cost value.
     */
    protected $computationalCost = PASSWORD_BCRYPT_DEFAULT_COST;

    /**
     * Fetch the correctly formatted internal variation for digestion.
     *
     * @return int|string The chosen variation for password hashing.
     */
    protected function fetchAlgorithmVariation()
    {
        return PASSWORD_BCRYPT;
    }

    /**
     * Fetch the correctly formatted internal parameters for digestion.
     *
     * @return array The chosen parameters for password hashing.
     */
    protected function fetchAlgorithmParameters()
    {
        return [
            'cost' => $this->computationalCost
        ];
    }

    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // @codeCoverageIgnoreStart
        if (!in_array(PASSWORD_BCRYPT, password_algos(), true)) {
            throw new \RuntimeException(
                'The Bcrypt algorithm is not supported under the current system configuration.'
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
