<?php

/**
 * The RSA-2048 encryption algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption as RsaAlgorithm;

/**
 * Class Rsa2048 - The RSA-2048 encryption algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Rsa2048 extends RsaAlgorithm
{
    /**
     * The internal key size measured in raw bits length for the algorithm
     *
     * @note For the current algorithm: 2048 bits (256 bytes)
     */
    const KEY_SIZE = 2048;
}
