<?php

/**
 * The digital envelope data object.
 */

namespace CryptoManana\DataStructures;

use CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class EnvelopeData - The digital envelope data object.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $key The encrypted secret key.
 * @property string $iv The encrypted initialization vector.
 * @property string $cipherData The encrypted message information.
 * @property string $authenticationTag The message authentication code (tag).
 */
class EnvelopeData extends BasicDataStructure
{
    /**
     * The concealed symmetric encryption secret key property storage.
     *
     * @var string The encrypted secret key.
     */
    protected $key = '';

    /**
     * The concealed symmetric encryption initialization vector property storage.
     *
     * @var string The encrypted initialization vector.
     */
    protected $iv = '';

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
     * Envelope constructor.
     *
     * @param string $encryptedKey The encrypted secret key.
     * @param string $encryptedIv The encrypted initialization vector.
     * @param string $encryptedData The encrypted message data information.
     * @param string $digestionTag The message authentication code (tag).
     *
     * @throws \Exception Validation errors.
     */
    public function __construct($encryptedKey = '', $encryptedIv = '', $encryptedData = '', $digestionTag = '')
    {
        $this->__set('key', $encryptedKey);
        $this->__set('iv', $encryptedIv);
        $this->__set('cipherData', $encryptedData);
        $this->__set('authenticationTag', $digestionTag);
    }

    /**
     * Envelope destructor
     */
    public function __destruct()
    {
    }

    /**
     * The envelope string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'key : ' . $this->key .
            ' | iv : ' . $this->iv .
            ' | encrypted : ' . $this->cipherData .
            ' | tag : ' . $this->authenticationTag;
    }
}
