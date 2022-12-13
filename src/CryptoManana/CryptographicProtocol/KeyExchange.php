<?php

/**
 * Cryptographic protocol for secure key exchange.
 */

namespace CryptoManana\CryptographicProtocol;

use CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationFunction;
use CryptoManana\Core\Interfaces\Containers\KeyExchangeInterface as KeyExchangeInformationProcessing;
use CryptoManana\Core\Interfaces\Containers\KeyExpansionInjectableInterface as KeyExpansionFunctionSetter;
use CryptoManana\Core\Traits\Containers\KeyExpansionInjectableTrait as KeyExpansionFunctionSetterImplementation;
use CryptoManana\Core\Traits\CommonValidations\KeyPairFormatValidationTrait as KeyFormatValidations;
use CryptoManana\Core\Traits\CommonValidations\KeyPairSizeValidationTrait as KeyPairSizeValidations;
use CryptoManana\DataStructures\ExchangeInformation as ExchangeInformationStructure;

/**
 * Class KeyExchange - The key exchange protocol object, based on the Diffie-Hellman algorithm.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin KeyExchangeInformationProcessing
 * @mixin KeyExpansionFunctionSetterImplementation
 * @mixin KeyFormatValidations
 * @mixin KeyPairSizeValidations
 */
class KeyExchange extends CryptographicProtocol implements KeyExchangeInformationProcessing, KeyExpansionFunctionSetter
{
    /**
     * The message key expansion derivation service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `KeyExpansionInjectableInterface`. }}
     */
    use KeyExpansionFunctionSetterImplementation;

    /**
     * Asymmetric key pair format validations.
     *
     * {@internal Reusable implementation of the common key pair format validation. }}
     */
    use KeyFormatValidations;

    /**
     * Asymmetric key pair size in bits validations.
     *
     * {@internal Reusable implementation of the common key pair size in bits validation. }}
     */
    use KeyPairSizeValidations;

    /**
     * The key pair size in bytes length property storage.
     *
     * @param int $keySize The key pair size in bits.
     */
    protected $keyPairSize = 2048;

    /**
     * The key expansion derivation algorithm service property storage.
     *
     * @var KeyDerivationFunction|null The key expansion derivation service.
     */
    protected $keyExpansionSource = null;

    /**
     * Internal method for generating a new key pair resource base on the given configuration.
     *
     * @param array $settings The key generation configuration settings.
     *
     * @return resource The private key resource containing all necessary information (prime, generator and public key).
     * @throws \Exception Validation or system errors.
     *
     * @codeCoverageIgnore
     */
    protected function generateKeyPairResource(array $settings)
    {
        $settings = array_merge(
            [
                'private_key_bits' => $this->keyPairSize,
                'private_key_type' => OPENSSL_KEYTYPE_DH,
            ],
            $settings
        );

        $privateKeyResource = openssl_pkey_new($settings);

        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to generate a key pair, probably because of a misconfigured ' .
                'OpenSSL library or invalid prime and generator values.'
            );
        }

        return $privateKeyResource;
    }

    /**
     * Internal method for extracting the key pair details from from the private key resource.
     *
     * @param resource $privateKeyResource The private key resource.
     *
     * @return array The key pair details as an array.
     * @throws \Exception Validation or system errors.
     *
     * @note The private key resource is passed via reference from the main logical method for performance reasons.
     *
     * @codeCoverageIgnore
     */
    protected function getKeyPairInformation(&$privateKeyResource)
    {
        $myOptions = openssl_pkey_get_details($privateKeyResource);

        if ($myOptions === false) {
            throw new \RuntimeException(
                'Failed to generate a key pair, probably because of a misconfigured ' .
                'OpenSSL library or invalid prime and generator values.'
            );
        }

        return $myOptions;
    }

    /**
     * Internal method for exporting the private key as a Base64 string from the private key resource.
     *
     * @param resource $privateKeyResource The private key resource.
     *
     * @return string The exported private key as a Base64 string.
     * @throws \Exception Validation or system errors.
     *
     * @note The private key resource is passed via reference from the main logical method for performance reasons.
     *
     * @codeCoverageIgnore
     */
    protected function exportPrivateKeyString(&$privateKeyResource)
    {
        $privateKeyString = '';
        $privateExport = openssl_pkey_export($privateKeyResource, $privateKeyString);

        if (empty($privateKeyString) || $privateExport === false) {
            throw new \RuntimeException(
                'Failed to export the private key to a string, probably because of a misconfigured OpenSSL library.'
            );
        }

        return $privateKeyString;
    }

    /**
     * Generates fresh Diffie–Hellman key exchange information.
     *
     * @param null|string $prime The hexadecimal representation of a prime number or null to generate a new one.
     * @param null|string $generator The hexadecimal representation of a generator number or null to generate a new one.
     *
     * @return array The generated key pair information.
     * @throws \Exception Validation or generation errors.
     */
    protected function generateKeyPair($prime = null, $generator = null)
    {
        $settings = [];

        if (is_string($prime)) {
            $settings['dh']['p'] = hex2bin($prime);
        }

        if (is_string($generator)) {
            $settings['dh']['g'] = hex2bin($generator);
        }

        $privateKeyResource = $this->generateKeyPairResource($settings);
        $privateKeyString = $this->exportPrivateKeyString($privateKeyResource);
        $keyPairDetails = $this->getKeyPairInformation($privateKeyResource);

        // Free the private key (resource cleanup)
        @openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        /**
         * {@internal The array has always the accessed keys because of the OpenSSL library details format. }}
         */
        $details = [];
        $details['prime'] = bin2hex($keyPairDetails['dh']['p']);
        $details['generator'] = bin2hex($keyPairDetails['dh']['g']);
        $details['private'] = base64_encode($privateKeyString);
        $details['public'] = base64_encode($keyPairDetails['dh']['pub_key']);

        return $details;
    }

    /**
     * Generates and builds a key exchange information object.
     *
     * @param null|string $prime The hexadecimal representation of a prime number or null to generate a new one.
     * @param null|string $generator The hexadecimal representation of a generator number or null to generate a new one.
     *
     * @return ExchangeInformationStructure The key exchange information object.
     * @throws \Exception Validation or generation errors.
     */
    protected function buildExchangeInformation($prime = null, $generator = null)
    {
        $information = $this->generateKeyPair($prime, $generator);

        $exchangeInformation = new ExchangeInformationStructure();

        $exchangeInformation->prime = $information['prime'];
        $exchangeInformation->generator = $information['generator'];
        $exchangeInformation->private = $information['private'];
        $exchangeInformation->public = $information['public'];

        return $exchangeInformation;
    }

    /**
     * Setter for the key pair size property.
     *
     * @param int $keySize The key size in bits.
     *
     * @return $this The container object.
     * @throws \Exception Validation errors.
     */
    public function setKeyExchangeSize($keySize)
    {
        $this->validateKeyPairSize($keySize);

        $this->keyPairSize = (int)$keySize;

        return $this;
    }

    /**
     * Getter for the key pair size property.
     *
     * @return int The key pair size in bits.
     */
    public function getKeyExchangeSize()
    {
        return $this->keyPairSize;
    }

    /**
     * Container constructor.
     *
     * @param KeyDerivationFunction|null $hasher The message key expansion derivation service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(KeyDerivationFunction $hasher = null)
    {
        if ($hasher !== null) {
            $this->keyExpansionSource = $hasher;
        } else {
            throw new \RuntimeException('No key expansion derivation service has been set.');
        }
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->keyExpansionSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->keyExpansionSource = clone $this->keyExpansionSource;
    }

    /**
     * Generates fresh key exchange information for sending to the remote party.
     *
     * @return ExchangeInformationStructure The key exchange information object.
     * @throws \Exception Validation errors.
     *
     * @note Remember never to send the private key to the remote party!
     */
    public function generateExchangeRequestInformation()
    {
        return $this->buildExchangeInformation();
    }

    /**
     * Generates fresh key exchange information based on the received prime and generator values.
     *
     * @param string $prime The hexadecimal representation of a prime number, also knows as `p`.
     * @param string $generator The hexadecimal generator number, a primitive root modulo of `p`, also known as `g`.
     *
     * @return ExchangeInformationStructure The key exchange information object.
     * @throws \Exception Validation errors.
     *
     * @note Remember never to send the private key to the remote party!
     */
    public function generateExchangeResponseInformation($prime, $generator)
    {
        if (!is_string($prime)) {
            throw new \InvalidArgumentException('The prime number representation must be a hexadecimal string.');
        } elseif (!is_string($generator)) {
            throw new \InvalidArgumentException('The generator number representation must be a hexadecimal string.');
        }

        return $this->buildExchangeInformation($prime, $generator);
    }

    /**
     * Computes the secret shared key for usage of both parties.
     *
     * @param string $remotePublicKey The remote side's public key, based on the same prime and generator combination.
     * @param string $localPrivateKey The local side's private key, based on the same prime and generator combination.
     *
     * @return string The shared secret key.
     * @throws \Exception Validation errors.
     *
     * @note The key is digested before returning for both authentication, length control and output formatting.
     */
    public function computeSharedSecret($remotePublicKey, $localPrivateKey)
    {
        $this->validatePublicKeyFormat($remotePublicKey);
        $this->validatePrivateKeyFormat($localPrivateKey);

        $privateKeyResource = openssl_pkey_get_private(base64_decode($localPrivateKey));

        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to use the current private key, probably because of ' .
                'a misconfigured OpenSSL library or an invalid key.'
            );
        }

        $sharedKey = openssl_dh_compute_key(base64_decode($remotePublicKey), $privateKeyResource);

        if ($sharedKey === false) {
            throw new \RuntimeException('The public key is invalid or based on different prime and generator values.');
        }

        // Free the private key (resource cleanup)
        @openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        return $this->keyExpansionSource->hashData($sharedKey);
    }
}
