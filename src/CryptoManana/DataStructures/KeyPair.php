<?php

/**
 * The asymmetric key-pair object.
 */

namespace CryptoManana\DataStructures;

use \CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class KeyPair - The key-pair object.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $private The private key.
 * @property string $public The public key.
 */
class KeyPair extends BasicDataStructure
{
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
     * Key-pair constructor.
     *
     * @param string $privateKey The private key string.
     * @param string $publicKey The public key string.
     */
    public function __construct($privateKey = '', $publicKey = '')
    {
        if (is_string($privateKey)) {
            $this->private = $privateKey;
        }

        if (is_string($publicKey)) {
            $this->public = $publicKey;
        }
    }

    /**
     * Key-pair destructor
     */
    public function __destruct()
    {
    }

    /**
     * The key-pair string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'private : ' . $this->private . ' | public : ' . $this->public;
    }
}
