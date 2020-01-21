<?php

/**
 * The CAMELLIA-128 encryption algorithm class.
 */

namespace CryptoManana\SymmetricEncryption;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipherAlgorithm;

/**
 * Class Camellia128 - The CAMELLIA-128 encryption algorithm object.
 *
 * @package CryptoManana\SymmetricEncryption
 */
class Camellia128 extends SymmetricBlockCipherAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'CAMELLIA';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 128 bits (16 bytes)
     */
    const KEY_SIZE = 16;

    /**
     * The internal initialization vector (IV) size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 128 bits (16 bytes)
     */
    const IV_SIZE = 16;

    /**
     * The internal operational block size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 128 bits (16 bytes)
     */
    const BLOCK_SIZE = 16;
}
