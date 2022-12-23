<?php

/**
 * The encryption layer configuration object.
 */

namespace CryptoManana\DataStructures;

use CryptoManana\Core\Abstractions\DataStructures\AbstractBasicStructure as BasicDataStructure;

/**
 * Class EncryptionLayer - The encryption layer configuration object.
 *
 * @package CryptoManana\DataStructures
 *
 * @property string $cipher The cipher object name.
 * @property string $key The secret key.
 * @property string $iv The initialization vector.
 * @property string $mode The block mode.
 * @property int $padding The padding standard.
 * @property int $format The output cipher format.
 */
class EncryptionLayer extends BasicDataStructure
{
    /**
     * The symmetric encryption cipher object name property storage.
     *
     * @var string The cipher object name.
     */
    protected $cipher = '';

    /**
     * The symmetric encryption secret key property storage.
     *
     * @var string The secret key.
     */
    protected $key = '';

    /**
     * The symmetric encryption initialization vector property storage.
     *
     * @var string The initialization vector.
     */
    protected $iv = '';

    /**
     * The symmetric encryption block mode property storage.
     *
     * @var string The block mode.
     */
    protected $mode = '';

    /**
     * The symmetric encryption padding standard property storage.
     *
     * @var int The padding standard.
     */
    protected $padding = 0;

    /**
     * The symmetric encryption output cipher format property storage.
     *
     * @var int The output cipher format.
     */
    protected $format = 0;

    /**
     * Encryption layer configuration constructor.
     *
     * @param string $cipher The cipher object name.
     * @param string $key The secret key.
     * @param string $iv The initialization vector.
     * @param string $mode The block mode.
     * @param int $padding The padding standard.
     * @param int $format The output cipher format.
     *
     * @throws \Exception Validation errors.
     */
    public function __construct($cipher = '', $key = '', $iv = '', $mode = '', $padding = 1, $format = 3)
    {
        $this->__set('cipher', $cipher);
        $this->__set('key', $key);
        $this->__set('iv', $iv);
        $this->__set('mode', $mode);
        $this->__set('padding', $padding);
        $this->__set('format', $format);
    }

    /**
     * Encryption layer destructor
     */
    public function __destruct()
    {
    }

    /**
     * The encryption layer string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'cipher : ' . $this->cipher .
            ' | key : ' . $this->key .
            ' | iv : ' . $this->iv .
            ' | mode : ' . $this->mode .
            ' | padding : ' . $this->padding .
            ' | format : ' . $this->format;
    }
}
