<?php

/**
 * The RSA-3072 encryption algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption as RsaAlgorithm;

/**
 * Class Rsa3072 - The RSA-3072 encryption algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Rsa3072 extends RsaAlgorithm
{
    /**
     * The internal key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 3072 bits (384 bytes)
     */
    const KEY_SIZE = 3072;
}
