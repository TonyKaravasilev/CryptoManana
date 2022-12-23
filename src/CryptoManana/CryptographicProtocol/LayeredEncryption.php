<?php

/**
 * Cryptographic protocol for multiple layered encryption.
 */

namespace CryptoManana\CryptographicProtocol;

use CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use CryptoManana\Core\Interfaces\Containers\LayeredEncryptionInterface as LayeredDataProcessing;
use CryptoManana\DataStructures\EncryptionLayer as LayerConfiguration;

/**
 * Class LayeredEncryption - The multiple layered encryption protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 */
class LayeredEncryption extends CryptographicProtocol implements LayeredDataProcessing
{
    /**
     * The internal encryption cipher collection property storage.
     *
     * @var SymmetricBlockCipher[]
     */
    protected $ciphers = [];

    /**
     * Setter for the encryption layers' configuration.
     *
     * @param LayerConfiguration[]|array $layers Collection of layers.
     *
     * @return $this The cryptographic protocol object.
     * @throws \Exception Validation errors.
     */
    public function setLayers(array $layers)
    {
        if (count($layers) < 2) {
            throw new \RuntimeException('This protocol must have at least two layers to operate.');
        }

        foreach ($layers as $layer) {
            if (!$layer instanceof LayerConfiguration) {
                throw new \RuntimeException(
                    'All supplied  configuration must be of the `EncryptionLayer` data structure.'
                );
            }

            $this->addLayer($layer);
        }

        return $this;
    }

    /**
     * Add a single new layer at the last of the list.
     *
     * @param LayerConfiguration $layer The layer configuration.
     *
     * @return $this The cryptographic protocol object.
     * @throws \Exception Validation errors.
     */
    public function addLayer(LayerConfiguration $layer)
    {
        if (class_exists($layer->cipher) && is_subclass_of($layer->cipher, SymmetricBlockCipher::class)) {
            /** @var SymmetricBlockCipher $cipher */
            $cipher = new $layer->cipher();

            $cipher->setSecretKey($layer->key)
                ->setInitializationVector($layer->iv)
                ->setBlockOperationMode($layer->mode)
                ->setPaddingStandard($layer->padding)
                ->setCipherFormat($layer->format);

            $this->ciphers[] = $cipher;
        } else {
            throw new \RuntimeException('All supplied ciphers must be existing and for symmetric encryption.');
        }

        return $this;
    }

    /**
     * Getter for the encryption layers' configuration.
     *
     * @return LayerConfiguration[]|array Collection of used layers' configuration.
     *
     * @throws \Exception Validation errors.
     */
    public function getLayers()
    {
        $layers = [];

        foreach ($this->ciphers as $cipher) {
            $layers [] = new LayerConfiguration(
                get_class($cipher),
                $cipher->getSecretKey(),
                $cipher->getInitializationVector(),
                $cipher->getBlockOperationMode(),
                $cipher->getPaddingStandard(),
                $cipher->getCipherFormat()
            );
        }

        return $layers;
    }


    /**
     * Container constructor.
     *
     * @param LayerConfiguration[]|array $configuration The layers' configuration.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(array $configuration = [])
    {
        $this->setLayers($configuration);
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->ciphers);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $ciphers = [];

        foreach ($this->ciphers as $cipher) {
            $ciphers [] = clone $cipher;
        }

        $this->ciphers = $ciphers;
    }

    /**
     * Calculates a XOR of two binary strings.
     *
     * @param string $stringOne The first binary string.
     * @param string $stringTwo The second binary string.
     *
     * @return string The XOR output of both strings.
     */
    protected function xorTwoStrings($stringOne, $stringTwo)
    {
        /**
         * {@internal The encryption standard is 8-bit wise (do not use StringBuilder) and utilizes performance. }}
         */
        if (strlen($stringTwo) < strlen($stringOne)) {
            $stringTwo = str_pad($stringTwo, strlen($stringOne), "\x0", STR_PAD_RIGHT);
        }

        $dataLength = strlen($stringOne);
        $keyLength = strlen($stringTwo);
        $xorOutput = $stringOne;

        for ($i = 0; $i < $dataLength; ++$i) {
            $xorOutput[$i] = $stringOne[$i] ^ $stringTwo[$i % $keyLength];
        }

        return $xorOutput;
    }

    /**
     * Internal method for the validation of the one-time pad string.
     *
     * @param string $oneTimePad The optional one-time pad key.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateOneTimePad($oneTimePad)
    {
        if (!is_string($oneTimePad)) {
            throw new \InvalidArgumentException('The one-time pad key must be a string or a binary string.');
        }
    }

    /**
     * Encrypts the given plain data multiple times with different algorithms as layers.
     *
     * @param string $plainData The plain input string.
     * @param string $oneTimePad The optional one-time pad key.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     *
     * @note The one-time pad key must be the same length as the input date to maximize security.
     */
    public function layeredEncryptData($plainData, $oneTimePad = '')
    {
        $this->validateOneTimePad($oneTimePad);

        if (!is_string($plainData)) {
            throw new \InvalidArgumentException(
                'The data for encryption must be a string or a binary string.'
            );
        } elseif (!empty(trim($oneTimePad))) {
            $plainData = $this->xorTwoStrings($plainData, $oneTimePad);
        }

        $last = count($this->ciphers) - 1;

        for ($i = 0; $i <= $last; $i++) {
            $cipher = $this->ciphers[$i];

            $plainData = $cipher->encryptData($plainData);
        }

        return $plainData;
    }

    /**
     * Decrypts the given cipher data multiple times with different algorithms as layers.
     *
     * @param string $cipherData The encrypted input string.
     * @param string $oneTimePad The optional one-time pad key.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function layeredDecryptData($cipherData, $oneTimePad = '')
    {
        $this->validateOneTimePad($oneTimePad);

        $last = count($this->ciphers) - 1;

        for ($i = $last; $i >= 0; $i--) {
            $cipher = $this->ciphers[$i];

            $cipherData = $cipher->decryptData($cipherData);
        }

        if (!empty(trim($oneTimePad))) {
            $cipherData = $this->xorTwoStrings($cipherData, $oneTimePad);
        }

        return $cipherData;
    }
}
