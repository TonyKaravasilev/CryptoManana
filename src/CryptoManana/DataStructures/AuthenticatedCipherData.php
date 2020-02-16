<?php

/**
 * The authentication encryption output data object.
 */

namespace CryptoManana\DataStructures;

use \CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class AuthenticatedCipherData - The authentication cipher data structure.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $cipherData The encrypted data information.
 * @property string $authenticationTag The message authentication code (tag).
 */
class AuthenticatedCipherData extends BasicDataStructure
{
    /**
     * The encrypted information property storage.
     *
     * @var string The encrypted data information.
     */
    protected $cipherData = '';

    /**
     * The message authentication code property storage.
     *
     * @var string The message authentication code (tag).
     */
    protected $authenticationTag = '';

    /**
     * Authenticated cipher data constructor.
     *
     * @param string $encryptedData The encrypted data information.
     * @param string $digestionTag The message authentication code (tag).
     */
    public function __construct($encryptedData = '', $digestionTag = '')
    {
        if (is_string($encryptedData)) {
            $this->cipherData = $encryptedData;
        }

        if (is_string($digestionTag)) {
            $this->authenticationTag = $digestionTag;
        }
    }

    /**
     * Authenticated cipher data destructor
     */
    public function __destruct()
    {
    }

    /**
     * The authenticated cipher data string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'encrypted : ' . $this->cipherData . ' |  tag : ' . $this->authenticationTag;
    }
}
