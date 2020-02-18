<?php

/**
 * The DSA-4096 digital signature algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature as DsaAlgorithm;

/**
 * Class Dsa4096 - The DSA-4096 digital signature algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Dsa4096 extends DsaAlgorithm
{
    /**
     * The internal key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 4096 bits (512 bytes)
     */
    const KEY_SIZE = 4096;
}
