<?php

/**
 * The CAMELLIA-192 encryption algorithm class.
 */

namespace CryptoManana\SymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipherAlgorithm;

/**
 * Class Camellia192 - The CAMELLIA-192 encryption algorithm object.
 *
 * @package CryptoManana\SymmetricEncryption
 */
class Camellia192 extends SymmetricBlockCipherAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'CAMELLIA';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @note For the current algorithm: 192 bits (24 bytes)
     */
    const KEY_SIZE = 24;

    /**
     * The internal initialization vector (IV) size measured in raw bytes length for the algorithm
     *
     * @note For the current algorithm: 128 bits (16 bytes)
     */
    const IV_SIZE = 16;

    /**
     * The internal operational block size measured in raw bytes length for the algorithm
     *
     * @note For the current algorithm: 128 bits (16 bytes)
     */
    const BLOCK_SIZE = 16;
}
