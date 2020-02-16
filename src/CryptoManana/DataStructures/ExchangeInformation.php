<?php

/**
 * The key exchange information data object.
 */

namespace CryptoManana\DataStructures;

use \CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class ExchangeInformation - The key exchange information data object.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $prime The hexadecimal representation of a prime number, also knows as `p`.
 * @property string $generator The hexadecimal generator number, a primitive root modulo of `p`, also known as `g`.
 * @property string $private The private key.
 * @property string $public The public key.
 */
class ExchangeInformation extends BasicDataStructure
{
    /**
     * The prime number property storage.
     *
     * @var string The chosen prime number, also knows as `p`
     */
    protected $prime = '';

    /**
     * The generator number property storage.
     *
     * @var string The chosen generator number, also knows as `g`
     */
    protected $generator = '';

    /**
     * The private key property storage.
     *
     * @var string The private key.
     */
    protected $private = '';

    /**
     * The public key property storage.
     *
     * @var string The public key.
     */
    protected $public = '';

    /**
     * Exchange information constructor.
     *
     * @param string $prime The hexadecimal representation of a prime number, also knows as `p`.
     * @param string $generator The hexadecimal generator number, a primitive root modulo of `p`, also known as `g`.
     * @param string $privateKey The private key.
     * @param string $publicKey The public key.
     */
    public function __construct($prime = '', $generator = '', $privateKey = '', $publicKey = '')
    {
        if (is_string($prime)) {
            $this->prime = $prime;
        }

        if (is_string($generator)) {
            $this->generator = $generator;
        }

        if (is_string($privateKey)) {
            $this->private = $privateKey;
        }

        if (is_string($publicKey)) {
            $this->public = $publicKey;
        }
    }

    /**
     * Exchange information destructor
     */
    public function __destruct()
    {
    }

    /**
     * The key exchange information data string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'prime : ' . $this->prime .
            ' | generator : ' . $this->generator .
            ' | private : ' . $this->private .
            ' | public : ' . $this->public;
    }
}
