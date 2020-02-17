<?php

/**
 * Cryptographic protocol for symmetric key authentication.
 */

namespace CryptoManana\CryptographicProtocol;

use \CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use \CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessSource;
use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessGenerator;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use \CryptoManana\Core\Interfaces\Containers\EntityIdentificationInterface as IdentifyEntities;
use \CryptoManana\Core\Interfaces\Containers\EntityAuthenticationInterface as AuthenticateEntities;
use \CryptoManana\Core\Interfaces\Containers\TokenTransformationInterface as GenerateAuthenticationTokens;
use \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface as RandomGeneratorSetter;
use \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface as SymmetricCipherSetter;
use \CryptoManana\Core\Traits\Containers\RandomnessInjectableTrait as RandomGeneratorSetterImplementation;
use \CryptoManana\Core\Traits\Containers\SymmetricEncryptionInjectableTrait as SymmetricCipherSetterImplementation;
use \CryptoManana\Core\Traits\Containers\EntityIdentificationTrait as EntityIdentificationProcess;
use \CryptoManana\Core\Traits\Containers\EntityAuthenticationViaTokenTrait as EntityAuthenticationProcess;
use \CryptoManana\Core\Traits\Containers\TokenSymmetricTransformationTrait as AuthenticationTokenTransformation;
use \CryptoManana\Randomness\CryptoRandom as DefaultRandomnessSource;

/**
 * Class SymmetricKeyAuthentication - The symmetric key authentication protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin RandomGeneratorSetterImplementation
 * @mixin SymmetricCipherSetterImplementation
 * @mixin EntityIdentificationProcess
 * @mixin EntityAuthenticationProcess
 * @mixin AuthenticationTokenTransformation
 */
class SymmetricKeyAuthentication extends CryptographicProtocol implements
    RandomGeneratorSetter,
    SymmetricCipherSetter,
    IdentifyEntities,
    AuthenticateEntities,
    GenerateAuthenticationTokens
{
    /**
     * Dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `RandomnessInjectableInterface`. }}
     */
    use RandomGeneratorSetterImplementation;

    /**
     * The message symmetric encryption service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `SymmetricEncryptionInjectableInterface`. }}
     */
    use SymmetricCipherSetterImplementation;

    /**
     * The entity identification capabilities.
     *
     * {@internal Reusable implementation of `EntityIdentificationInterface`. }}
     */
    use EntityIdentificationProcess;

    /**
     * The entity authentication capabilities.
     *
     * {@internal Reusable implementation of `EntityAuthenticationInterface`. }}
     */
    use EntityAuthenticationProcess;

    /**
     * The generation and transformation capabilities of authentication tokens.
     *
     * {@internal Reusable implementation of `TokenTransformationInterface`. }}
     */
    use AuthenticationTokenTransformation;

    /**
     * The pseudo-random generator service property storage.
     *
     * @var RandomnessSource|RandomnessGenerator|null The pseudo-random generator service.
     */
    protected $randomnessSource = null;

    /**
     * The message symmetric encryption algorithm service property storage.
     *
     * @var SymmetricBlockCipher|DataEncryption|null The message symmetric encryption service.
     */
    protected $symmetricCipherSource = null;

    /**
     * Container constructor.
     *
     * @param SymmetricBlockCipher $cipher The message encryption service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(SymmetricBlockCipher $cipher = null)
    {
        if ($cipher instanceof DataEncryption) {
            $this->symmetricCipherSource = $cipher;
        } else {
            throw new \RuntimeException('No symmetric encryption service has been set.');
        }

        $this->randomnessSource = new DefaultRandomnessSource();
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->randomnessSource, $this->symmetricCipherSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->randomnessSource = clone $this->randomnessSource;
        $this->symmetricCipherSource = clone $this->symmetricCipherSource;
    }
}
