<?php

/**
 * The RSA-1024 encryption algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption as RsaAlgorithm;

/**
 * Class Rsa1024 - The RSA-1024 encryption algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Rsa1024 extends RsaAlgorithm
{
    /**
     * The internal key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 1024 bits (128 bytes)
     */
    const KEY_SIZE = 1024;
}
