<?php

/**
 * The key exchange information data object.
 */

namespace CryptoManana\DataStructures;

use CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

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
     *
     * @throws \Exception Validation errors.
     */
    public function __construct($prime = '', $generator = '', $privateKey = '', $publicKey = '')
    {
        $this->__set('prime', $prime);
        $this->__set('generator', $generator);
        $this->__set('private', $privateKey);
        $this->__set('public', $publicKey);
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
