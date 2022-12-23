<?php

/**
 * Interface for specifying block cipher capabilities and actions for symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface BlockOperationsInterface - Interface for block cipher capabilities and operations.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface BlockOperationsInterface
{
    /**
     * The Cipher Block Chaining (CBC) mode of operation representation.
     */
    const CBC_MODE = 'CBC';

    /**
     * The Cipher Feedback (CFB) mode of operation representation.
     */
    const CFB_MODE = 'CFB';

    /**
     * The Output Feedback (OFB) mode of operation representation.
     */
    const OFB_MODE = 'OFB';

    /**
     * The Counter (CTR) mode of operation representation.
     */
    const CTR_MODE = 'CTR';

    /**
     * The Electronic Codebook (ECB) mode of operation representation.
     */
    const ECB_MODE = 'ECB';

    /**
     * The zero-padding (non-standard) representation.
     */
    const ZERO_PADDING = OPENSSL_ZERO_PADDING;

    /**
     * The PKCS#7 (Cryptographic Message Syntax Standard) padding representation.
     */
    const PKCS7_PADDING = OPENSSL_RAW_DATA;

    /**
     * Setter for the initialization vector (IV) string property.
     *
     * @param string $iv The initialization vector (IV) string.
     *
     * @throws \Exception Validation errors.
     */
    public function setInitializationVector($iv);

    /**
     * Getter for the initialization vector (IV) string property.
     *
     * @return string The initialization vector (IV) string.
     */
    public function getInitializationVector();

    /**
     * Setter for the block encryption operation mode string property.
     *
     * @param string $mode The block operation mode string.
     *
     * @throws \Exception Validation errors.
     */
    public function setBlockOperationMode($mode);

    /**
     * Getter for the block encryption operation mode string property.
     *
     * @return string The block operation mode string.
     */
    public function getBlockOperationMode();

    /**
     * Setter for the final block padding operation property.
     *
     * @param int $padding The padding standard integer code value.
     *
     * @throws \Exception Validation errors.
     */
    public function setPaddingStandard($padding);

    /**
     * Getter for the final block padding operation property.
     *
     * @return int The padding standard integer code value.
     */
    public function getPaddingStandard();
}
