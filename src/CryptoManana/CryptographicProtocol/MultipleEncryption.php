<?php

/**
 * Cryptographic protocol for multiple encryption.
 */

namespace CryptoManana\CryptographicProtocol;

use \CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationFunction;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use \CryptoManana\Core\Interfaces\Containers\KeyExpansionInjectableInterface as KeyExpansionFunctionSetter;
use \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface as SymmetricCipherSetter;
use \CryptoManana\Core\Interfaces\Containers\MultipleEncryptionInterface as MultiplePassDataProcessing;
use \CryptoManana\Core\Traits\Containers\KeyExpansionInjectableTrait as KeyExpansionFunctionSetterImplementation;
use \CryptoManana\Core\Traits\Containers\SymmetricEncryptionInjectableTrait as SymmetricCipherSetterImplementation;
use \CryptoManana\Hashing\HkdfShaTwo384 as DefaultDerivationSource;

/**
 * Class MultipleEncryption - The multiple encryption protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin KeyExpansionFunctionSetterImplementation
 * @mixin SymmetricCipherSetterImplementation
 */
class MultipleEncryption extends CryptographicProtocol implements
    KeyExpansionFunctionSetter,
    SymmetricCipherSetter,
    MultiplePassDataProcessing
{
    /**
     * The message key expansion derivation service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `KeyExpansionInjectableInterface`. }}
     */
    use KeyExpansionFunctionSetterImplementation;

    /**
     * The message symmetric encryption service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `SymmetricEncryptionInjectableInterface`. }}
     */
    use SymmetricCipherSetterImplementation;

    /**
     * The key expansion derivation algorithm service property storage.
     *
     * @var KeyDerivationFunction|null The key expansion derivation service.
     */
    protected $keyExpansionSource = null;

    /**
     * The message symmetric encryption algorithm service property storage.
     *
     * @var SymmetricBlockCipher|null The message symmetric encryption service.
     */
    protected $symmetricCipherSource = null;

    /**
     * Internal method for integer internal iteration count validation.
     *
     * @param int $iterations The number of internal iterations to perform.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateIterationsNumber($iterations)
    {
        $iterations = filter_var(
            $iterations,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 2,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($iterations === false) {
            throw new \InvalidArgumentException(
                'The multiple encryption or decryption iterations must be a valid integer bigger than 1.'
            );
        }
    }

    /**
     * Internal method for generating a list of secret keys and initialization vectors.
     *
     * @param int $iterations The number of internal iterations to perform.
     * @param bool|int|null $direction Flag for encryption direction (encrypt => `true` or decrypt => `false`).
     *
     * @return array The list of keys and IVs.
     * @throws \Exception Key expansion service errors.
     */
    protected function generateProcessingConfiguration($iterations, $direction = true)
    {
        // The key list property
        $keyList = [];

        // First key and IV are the original ones
        $keyList[0]['key'] = $this->symmetricCipherSource->getSecretKey();
        $keyList[0]['iv'] = $this->symmetricCipherSource->getInitializationVector();

        // Generate more keys and IVs via expand and extract procedure
        for ($i = 1; $i < $iterations; $i++) {
            $keyList[$i] = [];

            $keyList[$i]['key'] = $this->keyExpansionSource->hashData($keyList[$i - 1]['key']);
            $keyList[$i]['iv'] = $this->keyExpansionSource->hashData($keyList[$i - 1]['iv']);
        }

        return ($direction == true) ? $keyList : array_reverse($keyList, false);
    }

    /**
     * Container constructor.
     *
     * @param SymmetricBlockCipher|null $cipher the message symmetric encryption service.
     * @param KeyDerivationFunction|null $hasher The key expansion derivation service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(SymmetricBlockCipher $cipher = null, KeyDerivationFunction $hasher = null)
    {
        if ($cipher instanceof DataEncryption) {
            $this->symmetricCipherSource = $cipher;
        } else {
            throw new \RuntimeException('No symmetric encryption service has been set.');
        }

        if ($hasher !== null) {
            $this->keyExpansionSource = $hasher;
        } elseif (isset($this->symmetricCipherSource)) {
            $this->keyExpansionSource = new DefaultDerivationSource();

            $this->keyExpansionSource->setContextualString('CryptoMañana')
                ->setDerivationSalt($this->symmetricCipherSource->getSecretKey())
                ->setSalt($this->symmetricCipherSource->getInitializationVector());
        }
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->keyExpansionSource, $this->symmetricCipherSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->keyExpansionSource = clone $this->keyExpansionSource;
        $this->symmetricCipherSource = clone $this->symmetricCipherSource;
    }

    /**
     * Encrypts the given plain data multiple times with different extracted keys.
     *
     * @param string $plainData The plain input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public function multipleEncryptData($plainData, $iterations = 2)
    {
        $this->validateIterationsNumber($iterations);

        $last = ($iterations - 1);
        $list = $this->generateProcessingConfiguration($iterations, true);
        $oldCipherFormat = $this->symmetricCipherSource->getCipherFormat();
        $this->symmetricCipherSource->setCipherFormat(SymmetricBlockCipher::ENCRYPTION_OUTPUT_RAW);

        for ($i = 0; $i <= $last; $i++) {
            $this->symmetricCipherSource->setSecretKey($list[$i]['key'])->setInitializationVector($list[$i]['iv']);

            if ($i === $last) {
                $this->symmetricCipherSource->setCipherFormat($oldCipherFormat);
            }

            $plainData = $this->symmetricCipherSource->encryptData($plainData);
        }

        $this->symmetricCipherSource->setSecretKey($list[0]['key'])->setInitializationVector($list[0]['iv']);

        return $plainData;
    }

    /**
     * Decrypts the given cipher data multiple times with different extracted keys.
     *
     * @param string $cipherData The encrypted input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function multipleDecryptData($cipherData, $iterations = 2)
    {
        $this->validateIterationsNumber($iterations);

        $last = ($iterations - 1);
        $list = $this->generateProcessingConfiguration($iterations, false);
        $oldCipherFormat = $this->symmetricCipherSource->getCipherFormat();

        for ($i = 0; $i <= $last; $i++) {
            $this->symmetricCipherSource->setSecretKey($list[$i]['key'])->setInitializationVector($list[$i]['iv']);

            $format = ($i === 0) ? $oldCipherFormat : SymmetricBlockCipher::ENCRYPTION_OUTPUT_RAW;
            $this->symmetricCipherSource->setCipherFormat($format);

            $cipherData = $this->symmetricCipherSource->decryptData($cipherData);
        }

        $this->symmetricCipherSource->setCipherFormat($oldCipherFormat);
        $this->symmetricCipherSource->setSecretKey($list[$last]['key'])->setInitializationVector($list[$last]['iv']);

        return $cipherData;
    }
}
