<?php

/**
 * The authentication token object.
 */

namespace CryptoManana\DataStructures;

use CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class AuthenticationToken - The authentication token structure.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $tokenData The raw/plain token.
 * @property string $cipherData The encrypted token.
 */
class AuthenticationToken extends BasicDataStructure
{
    /**
     * The raw token data property storage.
     *
     * @var string The raw/plain token.
     */
    protected $tokenData = '';

    /**
     * The encrypted token property storage.
     *
     * @var string The encrypted token.
     */
    protected $cipherData = '';

    /**
     * Authentication token constructor.
     *
     * @param string $rawToken The raw token string.
     * @param string $encryptedToken The encrypted token string.
     *
     * @throws \Exception Validation errors.
     */
    public function __construct($rawToken = '', $encryptedToken = '')
    {
        $this->__set('tokenData', $rawToken);
        $this->__set('cipherData', $encryptedToken);
    }

    /**
     * Authentication token destructor
     */
    public function __destruct()
    {
    }

    /**
     * The authentication token string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'token : ' . $this->tokenData . ' | encrypted : ' . $this->cipherData;
    }
}
