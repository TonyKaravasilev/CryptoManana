<?php

/**
 * Cryptographic digital envelope protocol for secure data transfers.
 */

namespace CryptoManana\CryptographicProtocol;

use \CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use \CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessSource;
use \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessGenerator;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashFunction;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricEncryption;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use \CryptoManana\Core\Interfaces\Containers\DigitalEnvelopeInterface as DigitalEnvelopeProcessing;
use \CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface as RandomGeneratorSetter;
use \CryptoManana\Core\Interfaces\Containers\KeyedDigestionInjectableInterface as KeyedHashFunctionSetter;
use \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface as SymmetricCipherSetter;
use \CryptoManana\Core\Interfaces\Containers\AsymmetricEncryptionInjectableInterface as AsymmetricCipherSetter;
use \CryptoManana\Core\Traits\Containers\RandomnessInjectableTrait as RandomGeneratorSetterImplementation;
use \CryptoManana\Core\Traits\Containers\KeyedDigestionInjectableTrait as KeyedHashFunctionSetterImplementation;
use \CryptoManana\Core\Traits\Containers\SymmetricEncryptionInjectableTrait as SymmetricCipherSetterImplementation;
use \CryptoManana\Core\Traits\Containers\AsymmetricEncryptionInjectableTrait as AsymmetricCipherSetterImplementation;
use \CryptoManana\Randomness\CryptoRandom as DefaultRandomnessSource;
use \CryptoManana\DataStructures\EnvelopeData as EnvelopeStructure;

/**
 * Class DigitalEnvelope - The digital envelope cryptographic protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin RandomGeneratorSetterImplementation
 * @mixin KeyedHashFunctionSetterImplementation
 * @mixin SymmetricCipherSetterImplementation
 * @mixin AsymmetricCipherSetterImplementation
 */
class DigitalEnvelope extends CryptographicProtocol implements
    RandomGeneratorSetter,
    KeyedHashFunctionSetter,
    SymmetricCipherSetter,
    AsymmetricCipherSetter,
    DigitalEnvelopeProcessing
{
    /**
     * Dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `RandomnessInjectableInterface`. }}
     */
    use RandomGeneratorSetterImplementation;

    /**
     * The message keyed digestion service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `KeyedDigestionInjectableInterface`. }}
     */
    use KeyedHashFunctionSetterImplementation;

    /**
     * The message symmetric encryption service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `SymmetricEncryptionInjectableInterface`. }}
     */
    use SymmetricCipherSetterImplementation;

    /**
     * The message asymmetric encryption service dependency injection via a setter method.
     *
     * {@internal Reusable implementation of `AsymmetricEncryptionInjectableInterface`. }}
     */
    use AsymmetricCipherSetterImplementation;

    /**
     * The pseudo-random generator service property storage.
     *
     * @var RandomnessSource|RandomnessGenerator|null The pseudo-random generator service.
     */
    protected $randomnessSource = null;

    /**
     * The message keyed digestion service property storage.
     *
     * @var KeyedHashFunction|null The message keyed digestion service.
     */
    protected $keyedDigestionSource = null;

    /**
     * The message encryption symmetric algorithm service property storage.
     *
     * @var SymmetricBlockCipher|DataEncryption|null The symmetric message encryption service.
     */
    protected $symmetricCipherSource = null;

    /**
     * The message asymmetric encryption algorithm service property storage.
     *
     * @var AsymmetricEncryption|DataEncryption|null The message asymmetric encryption service.
     */
    protected $asymmetricCipherSource = null;

    /**
     * Container constructor.
     *
     * @param AsymmetricEncryption|DataEncryption|null $asymmetric The asymmetric message encryption service.
     * @param SymmetricBlockCipher|null $symmetric the message encryption service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(AsymmetricEncryption $asymmetric = null, SymmetricBlockCipher $symmetric = null)
    {
        if ($asymmetric instanceof DataEncryption) {
            $this->asymmetricCipherSource = $asymmetric;
        } else {
            throw new \RuntimeException('No asymmetric encryption service has been set.');
        }

        if ($symmetric instanceof DataEncryption) {
            $this->symmetricCipherSource = $symmetric;
        } else {
            throw new \RuntimeException('No symmetric encryption service has been set.');
        }

        $this->randomnessSource = new DefaultRandomnessSource();
        $this->keyedDigestionSource = null;
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->randomnessSource, $this->keyedDigestionSource);
        unset($this->symmetricCipherSource, $this->asymmetricCipherSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->randomnessSource = clone $this->randomnessSource;
        $this->symmetricCipherSource = clone $this->symmetricCipherSource;
        $this->asymmetricCipherSource = clone $this->asymmetricCipherSource;

        if ($this->keyedDigestionSource !== null) {
            $this->keyedDigestionSource = clone $this->keyedDigestionSource;
        }
    }

    /**
     * Seals the envelope with the secured information inside.
     *
     * @param string $plainData The plain message information.
     *
     * @return EnvelopeStructure The sealed envelope object.
     * @throws \Exception Validation errors.
     */
    public function sealEnvelope($plainData)
    {
        $key = $this->randomnessSource->getBytes(64);
        $iv = $this->randomnessSource->getBytes(64);

        $this->symmetricCipherSource->setSecretKey($key)->setInitializationVector($iv);

        $cipherData = $this->symmetricCipherSource->encryptData($plainData);
        $key = $this->asymmetricCipherSource->encryptData($key);
        $iv = $this->asymmetricCipherSource->encryptData($iv);
        $macTag = isset($this->keyedDigestionSource) ? $this->keyedDigestionSource->hashData($cipherData) : '';

        return new EnvelopeStructure($key, $iv, $cipherData, $macTag);
    }

    /**
     * Opens the envelope and extracts secured information from it.
     *
     * @param EnvelopeStructure $envelopeData The sealed envelope object.
     *
     * @return string The plain message information.
     * @throws \Exception Validation errors.
     */
    public function openEnvelope(EnvelopeStructure $envelopeData)
    {
        $key = $this->asymmetricCipherSource->decryptData($envelopeData->key);
        $iv = $this->asymmetricCipherSource->decryptData($envelopeData->iv);

        $this->symmetricCipherSource->setSecretKey($key)->setInitializationVector($iv);

        if (isset($this->keyedDigestionSource) && isset($envelopeData->authenticationTag)) {
            if (!$this->keyedDigestionSource->verifyHash($envelopeData->cipherData, $envelopeData->authenticationTag)) {
                throw new \RuntimeException('Wrong MAC tag, the data has been tampered with or defected.');
            }
        }

        return $this->symmetricCipherSource->decryptData($envelopeData->cipherData);
    }
}
