<?php

/**
 * Interface for the key exchange cryptographic protocols.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\DataStructures\ExchangeInformation as ExchangeInformationStructure;

/**
 * Interface KeyExchangeInterface - Interface specification for key exchange protocols.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface KeyExchangeInterface
{
    /**
     * Setter for the key pair size property.
     *
     * @param int $keySize The key pair size in bits.
     *
     * @throws \Exception Validation errors.
     */
    public function setKeyExchangeSize($keySize);

    /**
     * Getter for the key pair size property.
     *
     * @return int The key pair size in bits.
     */
    public function getKeyExchangeSize();

    /**
     * Generates fresh key exchange information for sending to the remote party.
     *
     * @return ExchangeInformationStructure The key exchange information object.
     * @throws \Exception Validation errors.
     *
     * @internal Remember never to send the private key to the remote party.
     */
    public function generateExchangeRequestInformation();

    /**
     * Generates fresh key exchange information based on the received prime and generator values.
     *
     * @param string $prime The hexadecimal representation of a prime number, also knows as `p`.
     * @param string $generator The hexadecimal generator number, a primitive root modulo of `p`, also known as `g`.
     *
     * @return ExchangeInformationStructure The key exchange information object.
     * @throws \Exception Validation errors.
     *
     * @internal Remember never to send the private key to the remote party.
     */
    public function generateExchangeResponseInformation($prime, $generator);

    /**
     * Computes the secret shared key for usage of both parties.
     *
     * @param string $remotePublicKey The remote side's public key, based on the same prime and generator combination.
     * @param string $localPrivateKey The local side's private key, based on the same prime and generator combination.
     *
     * @return string The shared secret key.
     * @throws \Exception Validation errors.
     *
     * @internal The key is digested before returning for both authentication, length control and output formatting.
     */
    public function computeSharedSecret($remotePublicKey, $localPrivateKey);
}
