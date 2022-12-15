<?php

/**
 * The CAMELLIA-256 encryption algorithm class.
 */

namespace CryptoManana\SymmetricEncryption;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipherAlgorithm;

/**
 * Class Camellia256 - The CAMELLIA-256 encryption algorithm object.
 *
 * @package CryptoManana\SymmetricEncryption
 */
class Camellia256 extends SymmetricBlockCipherAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'CAMELLIA';

    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @note For the current algorithm: 256 bits (32 bytes)
     */
    const KEY_SIZE = 32;

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
