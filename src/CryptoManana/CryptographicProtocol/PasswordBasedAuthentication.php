<?php

/**
 * Cryptographic protocol for password-based authentication.
 */

namespace CryptoManana\CryptographicProtocol;

use CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashFunction;
use CryptoManana\Core\Interfaces\Containers\EntityIdentificationInterface as IdentifyEntities;
use CryptoManana\Core\Interfaces\Containers\EntityAuthenticationInterface as AuthenticateEntities;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as VerificationAlgorithm;
use CryptoManana\Core\Interfaces\Containers\VerificationAlgorithmInjectableInterface as HashAlgorithmSetter;
use CryptoManana\Core\Traits\Containers\VerificationAlgorithmInjectableTrait as HashAlgorithmSetterImplementation;
use CryptoManana\Core\Traits\Containers\EntityIdentificationTrait as EntityIdentificationProcess;

/**
 * Class PasswordBasedAuthentication - The password-based authentication protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin HashAlgorithmSetterImplementation
 * @mixin EntityIdentificationProcess
 */
class PasswordBasedAuthentication extends CryptographicProtocol implements
    HashAlgorithmSetter,
    IdentifyEntities,
    AuthenticateEntities
{
    /**
     * The verification message digestion service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `VerificationAlgorithmInjectableInterface`. }}
     */
    use HashAlgorithmSetterImplementation;

    /**
     * The entity identification capabilities.
     *
     * {@internal Reusable implementation of `EntityIdentificationInterface`. }}
     */
    use EntityIdentificationProcess;

    /**
     * The message digestion and verification service property storage.
     *
     * @var HashFunction|VerificationAlgorithm|null The message digestion service.
     */
    protected $verificationSource = null;

    /**
     * Container constructor.
     *
     * @param HashFunction|VerificationAlgorithm|null $hasher The message digestion and verification service.
     *
     * @throws \Exception Initialization validation.
     *
     * @internal If `null` is passed, then the comparison will be raw binary based.
     */
    public function __construct(VerificationAlgorithm $hasher = null)
    {
        $this->verificationSource = $hasher;
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->verificationSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        if ($this->verificationSource !== null) {
            $this->verificationSource = clone $this->verificationSource;
        }
    }

    /**
     * Authenticate a user or a client entity.
     *
     * @param string $correctPassphrase The correct passphrase information.
     * @param string $suppliedPassphrase The supplied passphrase information.
     *
     * @return bool The identity authentication result.
     * @throws \Exception Validation errors.
     */
    public function authenticateEntity($correctPassphrase, $suppliedPassphrase)
    {
        if (!is_string($correctPassphrase)) {
            throw new \InvalidArgumentException(
                'The correct passphrase or hash value for verification must be a string or a binary string.'
            );
        } elseif (!is_string($suppliedPassphrase)) {
            throw new \InvalidArgumentException(
                'The supplied user passphrase value must be a string or a binary string.'
            );
        }

        if ($this->verificationSource !== null) {
            return $this->verificationSource->verifyHash($suppliedPassphrase, $correctPassphrase);
        } else {
            return hash_equals($correctPassphrase, $suppliedPassphrase);
        }
    }
}
