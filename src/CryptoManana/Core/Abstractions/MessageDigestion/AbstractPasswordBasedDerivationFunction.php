<?php

/**
 * Abstraction for key stretching and password-based key derivation objects like for the PBKDF2, Bcrypt and Argon2 ones.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyStretchingFunction as KeyStretchingAlgorithm;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as PasswordVerification;

/**
 * Class AbstractPasswordBasedDerivationFunction - Abstraction for password-based key derivation classes.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 */
abstract class AbstractPasswordBasedDerivationFunction extends KeyStretchingAlgorithm implements PasswordVerification
{
    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
