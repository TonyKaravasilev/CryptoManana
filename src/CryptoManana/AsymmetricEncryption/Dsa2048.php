<?php

/**
 * The DSA-2048 digital signature algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature as DsaAlgorithm;

/**
 * Class Dsa2048 - The DSA-2048 digital signature algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Dsa2048 extends DsaAlgorithm
{
    /**
     * The internal key size measured in raw bits length for the algorithm
     *
     * @note For the current algorithm: 2048 bits (256 bytes)
     */
    const KEY_SIZE = 2048;
}
