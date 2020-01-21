<?php

/**
 * Trait implementation of the block cipher capabilities and actions for symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\BlockOperationsInterface as BlockOperationsSpecification;

/**
 * Trait BlockOperationsTrait - Reusable implementation of `BlockOperationsInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\BlockOperationsInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property string $iv The initialization vector (IV) string property storage.
 * @property string $mode The block encryption operation mode string property.
 * @property int $padding The final block padding operation property.
 *
 * @mixin BlockOperationsSpecification
 */
trait BlockOperationsTrait
{
    /**
     * Setter for the initialization vector (IV) string property.
     *
     * @param string $iv The initialization vector (IV) string.
     *
     * @return $this The encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setInitializationVector($iv)
    {
        if (!is_string($iv)) {
            throw new \InvalidArgumentException('The initialization vector must be a string or a binary string.');
        }

        /**
         * {@internal The encryption standard is 8-bit wise (don not use StringBuilder) and utilizes performance. }}
         */
        if (strlen($iv) > static::IV_SIZE) {
            $iv = hash_hkdf('sha256', $iv, static::IV_SIZE, 'CryptoMañana', '');
        } elseif (strlen($iv) < static::IV_SIZE) {
            $iv = str_pad($iv, static::IV_SIZE, "\x0", STR_PAD_RIGHT);
        }

        $this->iv = $iv;

        return $this;
    }

    /**
     * Getter for the initialization vector (IV) string property.
     *
     * @return string The initialization vector (IV) string.
     */
    public function getInitializationVector()
    {
        return $this->iv;
    }

    /**
     * Setter for the block encryption operation mode string property.
     *
     * @param string $mode The block operation mode string.
     *
     * @return $this The encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setBlockOperationMode($mode)
    {
        $validModes = [
            self::CBC_MODE,
            self::CFB_MODE,
            self::OFB_MODE,
            self::CTR_MODE,
            self::ECB_MODE
        ];

        if (!is_string($mode) || !in_array(strtoupper($mode), $validModes, true)) {
            throw new \InvalidArgumentException(
                'The mode of operation must be a string and be a standardized block mode name.'
            );
        }

        $newMethodName = static::ALGORITHM_NAME . '-' . (static::KEY_SIZE * 8) . '-' . $mode;

        // @codeCoverageIgnoreStart
        if (!in_array($newMethodName, openssl_get_cipher_methods(), true)) {
            throw new \RuntimeException(
                'The algorithm `' . $newMethodName . '`is not supported under the current system configuration.'
            );
        }
        // @codeCoverageIgnoreEnd

        $this->mode = strtoupper($mode);

        return $this;
    }

    /**
     * Getter for the block encryption operation mode string property.
     *
     * @return string The block operation mode string.
     */
    public function getBlockOperationMode()
    {
        return $this->mode;
    }

    /**
     * Setter for the final block padding operation property.
     *
     * @param int $padding The padding standard integer code value.
     *
     * @return $this The encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setPaddingStandard($padding)
    {
        $padding = filter_var(
            $padding,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::PKCS7_PADDING,
                    "max_range" => self::ZERO_PADDING,
                ],
            ]
        );

        if ($padding === false) {
            throw new \InvalidArgumentException(
                'The padding standard must must be a valid integer between ' .
                self::PKCS7_PADDING . ' and ' . self::ZERO_PADDING . '.'
            );
        }

        $this->padding = $padding;

        return $this;
    }

    /**
     * Getter for the final block padding operation property.
     *
     * @return string The padding standard integer code value.
     */
    public function getPaddingStandard()
    {
        return $this->padding;
    }
}
