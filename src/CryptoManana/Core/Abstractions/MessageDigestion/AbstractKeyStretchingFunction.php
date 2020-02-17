<?php

/**
 * Abstraction for key stretching and key derivation objects.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashAlgorithm;

/**
 * Class AbstractKeyStretchingFunction - Abstraction for key stretching classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 */
abstract class AbstractKeyStretchingFunction extends HashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'none';

    /**
     * Flag to force native code polyfill realizations, if available.
     *
     * @var bool Flag to force native realizations.
     */
    protected $useNative = false;

    /**
     * Key stretching algorithm constructor.
     */
    public function __construct()
    {
    }
}
