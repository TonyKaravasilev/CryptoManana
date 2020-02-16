<?php

/**
 * The signed data object.
 */

namespace CryptoManana\DataStructures;

use \CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class SignedData - The plain data with included digital signature.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $data The plain message information.
 * @property string $signature The message's signature information.
 */
class SignedData extends BasicDataStructure
{
    /**
     * The plain message property storage.
     *
     * @var string The plain message information.
     */
    protected $data = '';

    /**
     * The message's signature information property storage.
     *
     * @var string The message's digital signature information.
     */
    protected $signature = '';

    /**
     * Signed data constructor.
     *
     * @param string $plainData The plain message.
     * @param string $signatureData The message's signature.
     */
    public function __construct($plainData = '', $signatureData = '')
    {
        if (is_string($plainData)) {
            $this->data = $plainData;
        }

        if (is_string($signatureData)) {
            $this->signature = $signatureData;
        }
    }

    /**
     * Signed data destructor
     */
    public function __destruct()
    {
    }

    /**
     * The signed data string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'data : ' . $this->data . ' | signature : ' . $this->signature;
    }
}
