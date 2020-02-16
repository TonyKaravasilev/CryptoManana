<?php

/**
 * Utility class for cryptography key and token generation.
 */

namespace CryptoManana\Utilities;

use \CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable as RandomnessContainer;
use \CryptoManana\Core\Interfaces\Randomness\AsymmetricKeyPairGenerationInterface as KeyPairGeneration;
use \CryptoManana\Core\Interfaces\Randomness\EncryptionKeyGenerationInterface as EncryptionKeyGeneration;
use \CryptoManana\Core\Interfaces\Randomness\HashingKeyGenerationInterface as HashingKeyGeneration;
use \CryptoManana\Core\Interfaces\Randomness\TokenGenerationInterface as TokenStringGeneration;
use \CryptoManana\Core\Traits\CommonValidations\KeyPairSizeValidationTrait as KeyPairSizeValidations;
use \CryptoManana\DataStructures\KeyPair as KeyPairStructure;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class TokenGenerator - Utility class for cryptography token generation.
 *
 * @package CryptoManana\Utilities
 *
 * @property \CryptoManana\Core\Abstractions\Randomness\AbstractGenerator $randomnessSource The randomness generator.
 *
 * @mixin KeyPairSizeValidations
 */
class TokenGenerator extends RandomnessContainer implements
    TokenStringGeneration,
    HashingKeyGeneration,
    EncryptionKeyGeneration,
    KeyPairGeneration
{
    /**
     * Asymmetric key pair size in bits validations.
     *
     * {@internal Reusable implementation of the common key pair size in bits validation. }}
     */
    use KeyPairSizeValidations;

    /**
     * Internal method for validation of positive output length.
     *
     * @param int $length The output length value for validation.
     *
     * @throws \Exception Validation errors.
     */
    protected function applyLengthValidation($length)
    {
        $length = filter_var(
            $length,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($length === false) {
            throw new \LengthException(
                'The length of the desired output data must me at least 1 character long.'
            );
        }
    }

    /**
     * Internal method for asymmetric algorithm type validation.
     *
     * @param int $algorithmType The asymmetric algorithm type integer code.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateAsymmetricAlgorithmType($algorithmType)
    {
        $algorithmType = filter_var(
            $algorithmType,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::RSA_KEY_PAIR_TYPE,
                    "max_range" => self::DSA_KEY_PAIR_TYPE,
                ],
            ]
        );

        if ($algorithmType === false) {
            throw new \InvalidArgumentException(
                'The asymmetric algorithm type must be a valid integer between ' .
                self::RSA_KEY_PAIR_TYPE . ' and ' . self::DSA_KEY_PAIR_TYPE . '.'
            );
        }
    }

    /**
     * Internal method for generating a fresh private key pair of the given size and type.
     *
     * @param int $keySize The private key size in bits.
     * @param string $algorithmType The asymmetric algorithm type.
     *
     * @return resource The private key resource.
     * @throws \Exception Validation or system errors.
     *
     * @codeCoverageIgnore
     */
    protected function generatePrivateKey($keySize, $algorithmType)
    {
        $privateKeyResource = openssl_pkey_new([
            'private_key_bits' => $keySize, // Size of the key (>= 384)
            'private_key_type' => $algorithmType, // The algorithm type (RSA/DSA) type
        ]);

        if ($privateKeyResource === false) {
            throw new \RuntimeException(
                'Failed to generate a private key, probably because of a misconfigured OpenSSL library.'
            );
        }

        return $privateKeyResource;
    }

    /**
     * Internal method for generating a fresh public key pair of the given size by extracting it from the private key.
     *
     * @param int $keySize The private key size in bits.
     * @param resource $privateKeyResource The private key resource.
     *
     * @return string The extracted public key string.
     * @throws \Exception Validation or system errors.
     *
     * @internal The private key resource is passed via reference from the main logical method for performance reasons.
     *
     * @codeCoverageIgnore
     */
    protected function generatePublicKey($keySize, &$privateKeyResource)
    {
        $privateKeyInformation = openssl_pkey_get_details($privateKeyResource);

        if ($privateKeyInformation === false) {
            throw new \RuntimeException(
                'Failed to generate/extract and export a public key, probably because of a misconfigured ' .
                'OpenSSL library or an invalid private key.'
            );
        } elseif ($privateKeyInformation['bits'] !== $keySize) {
            throw new \DomainException('The extracted public key is not of the correct size: `' . $keySize . '`.');
        }

        // Free the private key (resource cleanup)
        openssl_free_key($privateKeyResource);
        $privateKeyResource = null;

        return (string)$privateKeyInformation['key']; // <- The public key
    }

    /**
     * Internal method for generation of characters used for secure password string building.
     *
     * @param int|mixed $case Generation case as integer.
     *
     * @return string Password character.
     * @throws \Exception Validation Errors.
     */
    protected function getPasswordCharacter($case)
    {
        switch ($case) {
            case 1:
                return $this->randomnessSource->getDigit(true);
            case 2:
                return $this->randomnessSource->getLetter(false);
            case 3:
                return StringBuilder::stringToUpper($this->randomnessSource->getLetter(false));

            default:
                return $this->randomnessSource->getString(1, ['!', '@', '#', '$', '%', '^']);
        }
    }

    /**
     * Generate a random token string in alphanumeric or hexadecimal format.
     *
     * Note: This method can generate HEX output if the `$useAlphaNumeric` parameter is set to `false`.
     *
     * @param int $length The desired output length (default => 32).
     * @param bool|int $useAlphaNumeric Flag for switching to alphanumerical (default => true).
     *
     * @return string Randomly generated alphanumeric/hexadecimal token string.
     * @throws \Exception Validation errors.
     */
    public function getTokenString($length = self::MODERATE_TOKEN_LENGTH, $useAlphaNumeric = true)
    {
        $this->applyLengthValidation($length);

        if ($useAlphaNumeric) {
            $token = $this->randomnessSource->getAlphaNumeric($length, true);
        } else {
            $token = $this->randomnessSource->getHex($length, true);
        }

        return StringBuilder::stringReverse($token);
    }

    /**
     * Generate a random password string.
     *
     * Note: This method can use more special symbols on generation if the `$stronger` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 12).
     * @param bool|int $stronger Flag for using all printable ASCII characters (default => true).
     *
     * @return string Randomly generated password string.
     * @throws \Exception Validation errors.
     */
    public function getPasswordString($length = self::MODERATE_PASSWORD_LENGTH, $stronger = true)
    {
        $this->applyLengthValidation($length);

        if ($stronger) {
            $password = $this->randomnessSource->getAscii($length);
        } else {
            $password = '';

            for ($i = 1; $i <= $length; $i++) {
                $case = $this->randomnessSource->getInt(1, 4);

                $password .= $this->getPasswordCharacter($case);
            }
        }

        return $password;
    }

    /**
     * Generate a random HMAC key for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated HMAC key.
     * @throws \Exception Validation errors.
     */
    public function getHashingKey($length = self::DIGESTION_KEY_128_BITS, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random salt string for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated hashing salt.
     * @throws \Exception Validation errors.
     */
    public function getHashingSalt($length = self::DIGESTION_SALT_128_BITS, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random encryption key for symmetrical cyphers.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption key.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionKey($length = self::SECRET_KEY_128_BITS, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random initialization vector (IV) for encryption purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption initialization vector.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionInitializationVector($length = self::IV_128_BITS, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random key pair for asymmetrical cyphers.
     *
     * @param int $keySize The key size in bits.
     * @param int $algorithmType The asymmetric algorithm type integer code.
     *
     * @return KeyPairStructure Randomly generated asymmetric key pair (private and public keys) as an object.
     * @throws \Exception Validation errors.
     *
     * @codeCoverageIgnore
     */
    public function getAsymmetricKeyPair($keySize = self::KEY_PAIR_4096_BITS, $algorithmType = self::RSA_KEY_PAIR_TYPE)
    {
        $this->validateAsymmetricAlgorithmType($algorithmType);
        $this->validateKeyPairSize($keySize);

        $privateKeyResource = $this->generatePrivateKey((int)$keySize, (int)$algorithmType);

        $privateKeyString = '';
        $privateExport = openssl_pkey_export($privateKeyResource, $privateKeyString);

        if (empty($privateKeyString) || $privateExport === false) {
            throw new \RuntimeException(
                'Failed to export the private key to a string, probably because of a misconfigured OpenSSL library.'
            );
        }

        $publicKeyString = $this->generatePublicKey((int)$keySize, $privateKeyResource);

        $object = new KeyPairStructure();

        $object->private = base64_encode($privateKeyString);
        $object->public = base64_encode($publicKeyString);

        return $object;
    }
}
