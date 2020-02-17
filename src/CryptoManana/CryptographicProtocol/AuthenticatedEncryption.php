<?php

/**
 * Cryptographic protocol for authenticated encryption.
 */

namespace CryptoManana\CryptographicProtocol;

use \CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashFunction;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use \CryptoManana\Core\Interfaces\Containers\KeyedDigestionInjectableInterface as KeyedHashFunctionSetter;
use \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface as SymmetricCipherSetter;
use \CryptoManana\Core\Interfaces\Containers\AuthenticatedEncryptionInterface as AuthenticatedEncryptionProcessing;
use \CryptoManana\Core\Traits\Containers\KeyedDigestionInjectableTrait as KeyedHashFunctionSetterImplementation;
use \CryptoManana\Core\Traits\Containers\SymmetricEncryptionInjectableTrait as SymmetricCipherSetterImplementation;
use \CryptoManana\DataStructures\AuthenticatedCipherData as CipherDataStructure;
use \CryptoManana\Hashing\HmacShaTwo384 as DefaultDigestionSource;

/**
 * Class AuthenticatedEncryption - The authenticated encryption protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin KeyedHashFunctionSetterImplementation
 * @mixin SymmetricCipherSetterImplementation
 */
class AuthenticatedEncryption extends CryptographicProtocol implements
    KeyedHashFunctionSetter,
    SymmetricCipherSetter,
    AuthenticatedEncryptionProcessing
{
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
     * The authenticated encryption mode operation property.
     *
     * @var int The authenticated encryption mode integer code value.
     */
    protected $authenticationMode = self::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC;

    /**
     * Internal method for the injection of a MAC tag inside a plain message.
     *
     * @param string $plainData The plain input string.
     * @param string $macTag The message authentication code (tag).
     *
     * @return string The plain message containing the MAC tag inside.
     *
     * @internal Used by the Encrypt-then-MAC (EtM) authenticated encryption mode realization.
     */
    protected function injectMacIntoMessageData($plainData, $macTag)
    {
        return $plainData . '%7C__%7C' . $macTag; // URL friendly delimiter of 64 bytes
    }

    /**
     * Internal method for the extraction of a MAC tag contained inside a plain message.
     *
     * @param string $plainData The plain input string.
     *
     * @return array The separated message data and MAC tag as a tuple/array representation.
     *
     * @internal Used by the Encrypt-then-MAC (EtM) authenticated encryption mode realization.
     */
    protected function extractConcatenatedMacFromMessageData($plainData)
    {
        $cipherCode = explode('%7C__%7C', $plainData); // URL friendly delimiter of 64 bytes

        $messageData = (count($cipherCode) > 1 && isset($cipherCode[0])) ? $cipherCode[0] : '';
        $macTag = (count($cipherCode) > 1 && isset($cipherCode[1])) ? $cipherCode[1] : '';

        return [$messageData, $macTag]; // <- Tuple
    }

    /**
     * Builds a mutual key configuration with preserving the original/previous configuration state.
     *
     * @return array The mutual keys and the original keys configuration as a tuple/array representation.
     */
    protected function getMutualKeyCombinationWithSavingPreviousState()
    {
        $oldSecretKey = $this->symmetricCipherSource->getSecretKey();
        $oldIv = $this->symmetricCipherSource->getInitializationVector();
        $oldDigestionKey = $this->keyedDigestionSource->getKey();
        $oldDigestionSalt = $this->keyedDigestionSource->getSalt();

        $mutualKey = $oldSecretKey . $oldDigestionKey;
        $mutualSubKey = $oldIv . $oldDigestionSalt;

        return [
            [$mutualKey, $mutualSubKey], // New mutual configuration
            [$oldSecretKey, $oldIv, $oldDigestionKey, $oldDigestionSalt] // Old configuration
        ]; // <- Tuple
    }

    /**
     * Encrypts and authenticates the given plain data in Encrypt-and-MAC (E&M) mode.
     *
     * @param string $plainData The plain input string.
     *
     * @return CipherDataStructure The authenticated cipher data object.
     * @throws \Exception Validation errors.
     */
    protected function encryptAndMac($plainData)
    {
        list($mutualConfiguration, $originalConfiguration) = $this->getMutualKeyCombinationWithSavingPreviousState();
        list($mutualKey, $mutualSubKey) = $mutualConfiguration;
        list($oldSecretKey, $oldIv, $oldDigestionKey, $oldDigestionSalt) = $originalConfiguration;

        $this->symmetricCipherSource->setSecretKey($mutualKey)->setInitializationVector($mutualSubKey);
        $this->keyedDigestionSource->setKey($mutualKey)->setSalt($mutualSubKey);

        $cipherData = $this->symmetricCipherSource->encryptData($plainData);
        $macTag = $this->keyedDigestionSource->hashData($plainData);

        $this->symmetricCipherSource->setSecretKey($oldSecretKey)->setInitializationVector($oldIv);
        $this->keyedDigestionSource->setKey($oldDigestionKey)->setSalt($oldDigestionSalt);

        return new CipherDataStructure($cipherData, $macTag);
    }

    /**
     * Decrypts and authenticates the given plain data in Encrypt-and-MAC (E&M) mode.
     *
     * @param CipherDataStructure $authenticatedCipherData The authenticated cipher data object.
     *
     * @return string The plain data information.
     * @throws \Exception Validation or authentication errors.
     */
    protected function decryptAndMac(CipherDataStructure $authenticatedCipherData)
    {
        $cipherData = $authenticatedCipherData->cipherData;
        $macTag = $authenticatedCipherData->authenticationTag;

        list($mutualConfiguration, $originalConfiguration) = $this->getMutualKeyCombinationWithSavingPreviousState();
        list($mutualKey, $mutualSubKey) = $mutualConfiguration;
        list($oldSecretKey, $oldIv, $oldDigestionKey, $oldDigestionSalt) = $originalConfiguration;

        $this->symmetricCipherSource->setSecretKey($mutualKey)->setInitializationVector($mutualSubKey);
        $this->keyedDigestionSource->setKey($mutualKey)->setSalt($mutualSubKey);

        $plainData = $this->symmetricCipherSource->decryptData($cipherData);
        $dataDigest = $this->keyedDigestionSource->hashData($plainData);

        $this->symmetricCipherSource->setSecretKey($oldSecretKey)->setInitializationVector($oldIv);
        $this->keyedDigestionSource->setKey($oldDigestionKey)->setSalt($oldDigestionSalt);

        if (!hash_equals($macTag, $dataDigest)) {
            throw new \RuntimeException('Wrong MAC tag, the data has been tampered with or defected.');
        }

        return $plainData;
    }

    /**
     * Encrypts and authenticates the given plain data in MAC-then-Encrypt (MtE) mode.
     *
     * @param string $plainData The plain input string.
     *
     * @return CipherDataStructure The authenticated cipher data object.
     * @throws \Exception Validation errors.
     */
    protected function macThenEncrypt($plainData)
    {
        list($mutualConfiguration, $originalConfiguration) = $this->getMutualKeyCombinationWithSavingPreviousState();
        list($mutualKey, $mutualSubKey) = $mutualConfiguration;
        list($oldSecretKey, $oldIv, $oldDigestionKey, $oldDigestionSalt) = $originalConfiguration;

        $this->symmetricCipherSource->setSecretKey($mutualKey)->setInitializationVector($mutualSubKey);
        $this->keyedDigestionSource->setKey($mutualKey)->setSalt($mutualSubKey);

        $macTag = $this->keyedDigestionSource->hashData($plainData);
        $plainData = $this->injectMacIntoMessageData($plainData, $macTag);
        $cipherData = $this->symmetricCipherSource->encryptData($plainData);

        $this->symmetricCipherSource->setSecretKey($oldSecretKey)->setInitializationVector($oldIv);
        $this->keyedDigestionSource->setKey($oldDigestionKey)->setSalt($oldDigestionSalt);

        return new CipherDataStructure($cipherData, '');
    }

    /**
     * Decrypts and authenticates the given plain data in MAC-then-Encrypt (MtE) mode.
     *
     * @param CipherDataStructure $authenticatedCipherData The authenticated cipher data object.
     *
     * @return string The plain data information.
     * @throws \Exception Validation or authentication errors.
     */
    protected function macThenDecrypt(CipherDataStructure $authenticatedCipherData)
    {
        list($mutualConfiguration, $originalConfiguration) = $this->getMutualKeyCombinationWithSavingPreviousState();
        list($mutualKey, $mutualSubKey) = $mutualConfiguration;
        list($oldSecretKey, $oldIv, $oldDigestionKey, $oldDigestionSalt) = $originalConfiguration;

        $this->symmetricCipherSource->setSecretKey($mutualKey)->setInitializationVector($mutualSubKey);
        $this->keyedDigestionSource->setKey($mutualKey)->setSalt($mutualSubKey);

        list($plainData, $macTag) = $this->extractConcatenatedMacFromMessageData(
            $this->symmetricCipherSource->decryptData($authenticatedCipherData->cipherData)
        );

        $dataDigest = $this->keyedDigestionSource->hashData($plainData);

        $this->symmetricCipherSource->setSecretKey($oldSecretKey)->setInitializationVector($oldIv);
        $this->keyedDigestionSource->setKey($oldDigestionKey)->setSalt($oldDigestionSalt);

        if (!hash_equals($macTag, $dataDigest) || $authenticatedCipherData->authenticationTag !== '') {
            throw new \RuntimeException('Wrong MAC tag, the data has been tampered with or defected.');
        }

        return $plainData;
    }

    /**
     * Encrypts and authenticates the given plain data in Encrypt-then-MAC (EtM) mode.
     *
     * @param string $plainData The plain input string.
     *
     * @return CipherDataStructure The authenticated cipher data object.
     * @throws \Exception Validation errors.
     */
    protected function encryptThenMac($plainData)
    {
        $cipherData = $this->symmetricCipherSource->encryptData($plainData);
        $macTag = $this->keyedDigestionSource->hashData($cipherData);

        return new CipherDataStructure($cipherData, $macTag);
    }

    /**
     * Decrypts and authenticates the given plain data in Encrypt-then-MAC (EtM) mode.
     *
     * @param CipherDataStructure $authenticatedCipherData The authenticated cipher data object.
     *
     * @return string The plain data information.
     * @throws \Exception Validation or authentication errors.
     */
    protected function decryptThenMac(CipherDataStructure $authenticatedCipherData)
    {
        $cipherData = $authenticatedCipherData->cipherData;
        $macTag = $authenticatedCipherData->authenticationTag;

        $plainData = $this->symmetricCipherSource->decryptData($cipherData);
        $cipherDigest = $this->keyedDigestionSource->hashData($cipherData);

        if (!hash_equals($macTag, $cipherDigest)) {
            throw new \RuntimeException('Wrong MAC tag, the data has been tampered with or defected.');
        }

        return $plainData;
    }

    /**
     * Setter for authenticated encryption mode operation property.
     *
     * @param int $mode The authenticated encryption mode integer code value.
     *
     * @return $this The authenticated encryption object.
     * @throws \Exception Validation errors.
     */
    public function setAuthenticationMode($mode)
    {
        $mode = filter_var(
            $mode,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::AUTHENTICATION_MODE_ENCRYPT_AND_MAC,
                    "max_range" => self::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC,
                ],
            ]
        );

        if ($mode === false) {
            throw new \InvalidArgumentException(
                'The padding standard must be a valid integer between ' .
                self::AUTHENTICATION_MODE_ENCRYPT_AND_MAC . ' and ' . self::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC . '.'
            );
        }

        $this->authenticationMode = $mode;

        return $this;
    }

    /**
     * Getter for the authenticated encryption mode operation property.
     *
     * @return int The authenticated encryption mode integer code value.
     */
    public function getAuthenticationMode()
    {
        return $this->authenticationMode;
    }

    /**
     * The message keyed digestion service property storage.
     *
     * @var KeyedHashFunction|null The message keyed digestion service.
     */
    protected $keyedDigestionSource = null;

    /**
     * The message symmetric encryption algorithm service property storage.
     *
     * @var SymmetricBlockCipher|null The message symmetric encryption service.
     */
    protected $symmetricCipherSource = null;

    /**
     * Container constructor.
     *
     * @param SymmetricBlockCipher|null $cipher the message symmetric encryption service.
     * @param KeyedHashFunction|null $hasher The message keyed digestion service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(SymmetricBlockCipher $cipher = null, KeyedHashFunction $hasher = null)
    {
        if ($cipher instanceof DataEncryption) {
            $this->symmetricCipherSource = $cipher;
        } else {
            throw new \RuntimeException('No symmetric encryption service has been set.');
        }

        if ($hasher !== null) {
            $this->keyedDigestionSource = $hasher;
        } elseif (isset($this->symmetricCipherSource)) {
            $this->keyedDigestionSource = new DefaultDigestionSource();

            $this->keyedDigestionSource->setKey($this->symmetricCipherSource->getSecretKey())
                ->setSalt($this->symmetricCipherSource->getInitializationVector());
        }
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->keyedDigestionSource, $this->symmetricCipherSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->keyedDigestionSource = clone $this->keyedDigestionSource;
        $this->symmetricCipherSource = clone $this->symmetricCipherSource;
    }

    /**
     * Encrypts and authenticates the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return CipherDataStructure The authenticated cipher data object.
     * @throws \Exception Validation errors.
     */
    public function authenticatedEncryptData($plainData)
    {
        if (!is_string($plainData)) {
            throw new \InvalidArgumentException('The data for encryption must be a string or a binary string.');
        }

        if ($this->authenticationMode === self::AUTHENTICATION_MODE_ENCRYPT_AND_MAC) {
            $outputCipherData = $this->encryptAndMac($plainData);
        } elseif ($this->authenticationMode === self::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT) {
            $outputCipherData = $this->macThenEncrypt($plainData);
        } elseif ($this->authenticationMode === self::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC) {
            $outputCipherData = $this->encryptThenMac($plainData);
        } else {
            throw new \OutOfBoundsException('Unsupported authenticated encryption mode was given.');
        }

        return $outputCipherData;
    }

    /**
     * Decrypts and authenticates the given cipher data.
     *
     * @param CipherDataStructure $authenticatedCipherData The authenticated cipher data object.
     *
     * @return string The plain data information.
     * @throws \Exception Validation errors.
     */
    public function authenticatedDecryptData(CipherDataStructure $authenticatedCipherData)
    {
        if ($this->authenticationMode === self::AUTHENTICATION_MODE_ENCRYPT_AND_MAC) {
            $plainData = $this->decryptAndMac($authenticatedCipherData);
        } elseif ($this->authenticationMode === self::AUTHENTICATION_MODE_MAC_THEN_ENCRYPT) {
            $plainData = $this->macThenDecrypt($authenticatedCipherData);
        } elseif ($this->authenticationMode === self::AUTHENTICATION_MODE_ENCRYPT_THEN_MAC) {
            $plainData = $this->decryptThenMac($authenticatedCipherData);
        } else {
            throw new \OutOfBoundsException('Unsupported authenticated decryption mode was given.');
        }

        return $plainData;
    }
}
