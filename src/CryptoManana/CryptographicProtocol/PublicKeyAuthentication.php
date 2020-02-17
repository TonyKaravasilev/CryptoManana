<?php

/**
 * Cryptographic protocol for asymmetric/public key authentication.
 */

namespace CryptoManana\CryptographicProtocol;

use CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessSource;
use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessGenerator;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use CryptoManana\Core\Interfaces\Containers\EntityIdentificationInterface as IdentifyEntities;
use CryptoManana\Core\Interfaces\Containers\EntityAuthenticationInterface as AuthenticateEntities;
use CryptoManana\Core\Interfaces\Containers\TokenTransformationInterface as GenerateAuthenticationTokens;
use CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface as RandomGeneratorSetter;
use CryptoManana\Core\Interfaces\Containers\AsymmetricEncryptionInjectableInterface as AsymmetricCipherSetter;
use CryptoManana\Core\Traits\Containers\RandomnessInjectableTrait as RandomGeneratorSetterImplementation;
use CryptoManana\Core\Traits\Containers\AsymmetricEncryptionInjectableTrait as AsymmetricCipherSetterImplementation;
use CryptoManana\Core\Traits\Containers\EntityIdentificationTrait as EntityIdentificationProcess;
use CryptoManana\Core\Traits\Containers\EntityAuthenticationViaTokenTrait as EntityAuthenticationProcess;
use CryptoManana\Core\Traits\Containers\TokenAsymmetricTransformationTrait as AuthenticationTokenTransformation;
use CryptoManana\Randomness\CryptoRandom as DefaultRandomnessSource;

/**
 * Class PublicKeyAuthentication - The asymmetric/public key authentication protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin RandomGeneratorSetterImplementation
 * @mixin AsymmetricCipherSetterImplementation
 * @mixin EntityIdentificationProcess
 * @mixin EntityAuthenticationProcess
 * @mixin AuthenticationTokenTransformation
 */
class PublicKeyAuthentication extends CryptographicProtocol implements
    RandomGeneratorSetter,
    AsymmetricCipherSetter,
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
     * The message asymmetric encryption service dependency injection via a setter method.
     *
     * {@internal Reusable implementation of `AsymmetricEncryptionInjectableInterface`. }}
     */
    use AsymmetricCipherSetterImplementation;

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
     * The message asymmetric encryption algorithm service property storage.
     *
     * @var AsymmetricEncryption|DataEncryption|null The message asymmetric encryption service.
     */
    protected $asymmetricCipherSource = null;

    /**
     * Container constructor.
     *
     * @param AsymmetricEncryption $cipher The message encryption service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(AsymmetricEncryption $cipher = null)
    {
        if ($cipher instanceof DataEncryption) {
            $this->asymmetricCipherSource = $cipher;
        } else {
            throw new \RuntimeException('No asymmetric encryption service has been set.');
        }

        $this->randomnessSource = new DefaultRandomnessSource();
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->randomnessSource, $this->asymmetricCipherSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->randomnessSource = clone $this->randomnessSource;
        $this->asymmetricCipherSource = clone $this->asymmetricCipherSource;
    }
}
