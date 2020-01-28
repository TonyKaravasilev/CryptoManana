<?php

/**
 * The DSA-3072 digital signature algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractDsaSignature as DsaAlgorithm;

/**
 * Class Dsa3072 - The DSA-3072 digital signature algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Dsa3072 extends DsaAlgorithm
{
    /**
     * The internal key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 3072 bits (384 bytes)
     */
    const KEY_SIZE = 3072;
}
