<?php

/**
 * Cryptographic digital signature protocol.
 */

namespace CryptoManana\CryptographicProtocol;

use \CryptoManana\Core\Abstractions\Containers\AbstractCryptographicProtocol as CryptographicProtocol;
use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DataSigning;
use \CryptoManana\Core\Interfaces\Containers\SignatureStandardInjectableInterface as SignatureStandardSetter;
use \CryptoManana\Core\Interfaces\Containers\SignedDataInterface as SignedDataObjectProcessing;
use \CryptoManana\Core\Traits\Containers\SignatureStandardInjectableTrait as SignatureStandardSetterImplementation;
use \CryptoManana\DataStructures\SignedData as SignedDataStructure;

/**
 * Class DigitalSignature - The digital signature cryptographic protocol object.
 *
 * @package CryptoManana\CryptographicProtocol
 *
 * @mixin SignatureStandardSetterImplementation
 */
class DigitalSignature extends CryptographicProtocol implements SignatureStandardSetter, SignedDataObjectProcessing
{
    /**
     * The digital signature service dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `SignatureStandardInjectableInterface`. }}
     */
    use SignatureStandardSetterImplementation;

    /**
     * The digital signature service property storage.
     *
     * @var AsymmetricCipher|DataSigning|null The digital signature service.
     */
    protected $signatureSource = null;

    /**
     * Container constructor.
     *
     * @param AsymmetricCipher|DataSigning|null $signatureAlgorithm The digital signature service.
     *
     * @throws \Exception Initialization validation.
     */
    public function __construct(AsymmetricCipher $signatureAlgorithm = null)
    {
        if ($signatureAlgorithm instanceof DataSigning) {
            $this->signatureSource = $signatureAlgorithm;
        } else {
            throw new \RuntimeException('No digital signature standard service has been set.');
        }
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->signatureSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->signatureSource = clone $this->signatureSource;
    }

    /**
     * Creates a signed data object for the given input data.
     *
     * @param string $plainData The plain input string.
     *
     * @return SignedDataStructure The signed data object.
     * @throws \Exception Validation errors.
     */
    public function createSignedData($plainData)
    {
        $signatureData = $this->signatureSource->signData($plainData);

        return new SignedDataStructure($plainData, $signatureData);
    }

    /**
     * Verifies and extracts the plain data from a signed data object.
     *
     * @param SignedDataStructure $signedData The signed data object.
     *
     * @return string The verified plain information.
     * @throws \Exception Validation errors.
     */
    public function extractVerifiedData(SignedDataStructure $signedData)
    {
        if (!$this->signatureSource->verifyDataSignature($signedData->signature, $signedData->data)) {
            throw new \RuntimeException('Wrong signature, the data has been tampered with or defected.');
        }

        return $signedData->data;
    }
}
