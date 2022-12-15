<?php

/**
 * The DSA-1024 digital signature algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature as DsaAlgorithm;

/**
 * Class Dsa1024 - The DSA-1024 digital signature algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Dsa1024 extends DsaAlgorithm
{
    /**
     * The internal key size measured in raw bits length for the algorithm
     *
     * @note For the current algorithm: 1024 bits (128 bytes)
     */
    const KEY_SIZE = 1024;
}
