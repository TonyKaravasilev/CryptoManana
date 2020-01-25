<?php

/**
 * The RSA-4096 encryption algorithm class.
 */

namespace CryptoManana\AsymmetricEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractRsaEncryption as RsaAlgorithm;

/**
 * Class Rsa4096 - The RSA-4096 encryption algorithm object.
 *
 * @package CryptoManana\AsymmetricEncryption
 */
class Rsa4096 extends RsaAlgorithm
{
    /**
     * The internal key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 4096 bits (512 bytes)
     */
    const KEY_SIZE = 4096;
}
