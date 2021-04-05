<?php

/**
 * Trait implementation of the public and private key pair capabilities for asymmetric encryption/signing algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\KeyPairInterface as KeyPairSpecification;
use CryptoManana\Core\Traits\CommonValidations\KeyPairFormatValidationTrait as KeyFormatValidations;
use CryptoManana\DataStructures\KeyPair as KeyPairStructure;

/**
 * Trait KeyPairTrait - Reusable implementation of `KeyPairInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\KeyPairInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property string $privateKey The private key string property storage.
 * @property string $publicKey The public key string property storage.
 *
 * @mixin KeyPairSpecification
 * @mixin KeyFormatValidations
 */
trait KeyPairTrait
{
    /**
     * Asymmetric key pair format validations.
     *
     * {@internal Reusable implementation of the common key pair format validation. }}
     */
    use KeyFormatValidations;

    /**
     * Internal method for the validation of the private key resource.
     *
     * @param string $privateKey The private key input string.
     *
     * @return string The extracted public key string from the private key resource.
     * @throws \Exception Validation errors.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     *
     * @codeCoverageIgnore
     */
    protected function validatePrivateKeyResource(&$privateKey)
    {
        $privateKeyResource = openssl_pkey_get_private(base64_decode($privateKey));

        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to import the private key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }

        $privateKeyInformation = openssl_pkey_get_details($privateKeyResource);

        if ($privateKeyInformation === false) {
            throw new \RuntimeException(
                'Failed to export the key\'s information, probably because of a misconfigured ' .
                'OpenSSL library or an invalid private key.'
            );
        } elseif ($privateKeyInformation['bits'] !== static::KEY_SIZE) {
            throw new \DomainException('The private key is not of the correct size: `' . static::KEY_SIZE . '`.');
        }

        // Free the private key (resource cleanup)
        @openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        return base64_encode((string)$privateKeyInformation['key']); // <- The extracted public key
    }

    /**
     * Internal method for the validation of the public key resource.
     *
     * @param string $publicKey The public key input string.
     *
     * @throws \Exception Validation errors.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     *
     * @codeCoverageIgnore
     */
    protected function validatePublicKeyResource(&$publicKey)
    {
        $publicKeyResource = openssl_pkey_get_public(base64_decode($publicKey));

        if ($publicKeyResource === false) {
            throw new \RuntimeException(
                'Failed to import the public key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }

        $publicKeyInformation = openssl_pkey_get_details($publicKeyResource);

        if ($publicKeyInformation === false) {
            throw new \RuntimeException(
                'Failed to export the key\'s information, probably because of a misconfigured ' .
                'OpenSSL library or an invalid public key.'
            );
        } elseif ($publicKeyInformation['bits'] !== static::KEY_SIZE) {
            throw new \DomainException('The public key is not of the correct size: `' . static::KEY_SIZE . '`.');
        }

        // Free the public key (resource cleanup)
        @openssl_free_key($publicKeyResource);
        $publicKeyResource = null;
    }

    /**
     * Internal method for the validation of the private and public key pair string representations.
     *
     * @param string $privateKey The private key input string.
     * @param string $publicKey The public key input string.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateKeyPair($privateKey, $publicKey)
    {
        $this->validatePrivateKeyFormat($privateKey);
        $this->validatePublicKeyFormat($publicKey);

        $extractedPublicKey = $this->validatePrivateKeyResource($privateKey);

        $this->validatePublicKeyResource($publicKey);

        // @codeCoverageIgnoreStart
        $thePairIsNotMatching = (
            strlen($extractedPublicKey) !== strlen($publicKey) ||
            substr($extractedPublicKey, -8) !== substr($extractedPublicKey, -8) ||
            hash('sha256', $extractedPublicKey) !== hash('sha256', $publicKey)
        );

        if ($thePairIsNotMatching) {
            throw new \RuntimeException('The private and public key pair are not matching and can not be used.');
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Setter for the whole key pair as an array.
     *
     * @param KeyPairStructure $keyPair The private and public key pair as an object.
     *
     * @return $this The encryption/signature algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setKeyPair(KeyPairStructure $keyPair)
    {
        $this->validateKeyPair($keyPair->private, $keyPair->public);

        // Set the key pair
        $this->privateKey = $keyPair->private;
        $this->publicKey = $keyPair->public;

        return $this;
    }

    /**
     * Getter for the whole key pair as an array.
     *
     * @return KeyPairStructure The private and public key pair as an object.
     * @throws \Exception Validation errors.
     */
    public function getKeyPair()
    {
        return new KeyPairStructure($this->privateKey, $this->publicKey);
    }

    /**
     * Setter for the private key string property.
     *
     * @param string $privateKey The private key string.
     *
     * @return $this The encryption/signature algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setPrivateKey($privateKey)
    {
        $this->validatePrivateKeyFormat($privateKey);
        $this->validatePrivateKeyResource($privateKey);

        // Set the key pair
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Getter for the private key string property.
     *
     * @return string The private key string.
     */
    public function getPrivateKey()
    {
        return ($this->privateKey !== '') ? $this->privateKey : null;
    }

    /**
     * Setter for the public key string property.
     *
     * @param string $publicKey The public key string.
     *
     * @return $this The encryption/signature algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setPublicKey($publicKey)
    {
        $this->validatePublicKeyFormat($publicKey);
        $this->validatePublicKeyResource($publicKey);

        // Set the key pair
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Getter for the public key string property.
     *
     * @return string The public key string.
     */
    public function getPublicKey()
    {
        return ($this->publicKey !== '') ? $this->publicKey : null;
    }

    /**
     * Checks if the private key is present.
     *
     * @throws \Exception If there is no private key set.
     */
    public function checkIfThePrivateKeyIsSet()
    {
        if ($this->privateKey === '') {
            throw new \RuntimeException('There is no private key set, please generate or import your key pair.');
        }
    }

    /**
     * Checks if the public key is present.
     *
     * @throws \Exception If there is no public key set.
     */
    public function checkIfThePublicKeyIsSet()
    {
        if ($this->publicKey === '') {
            throw new \RuntimeException('There is no public key set, please generate or import your key pair.');
        }
    }
}
