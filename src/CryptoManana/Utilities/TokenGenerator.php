<?php

/**
 * Utility class for cryptography key and token generation.
 */

namespace CryptoManana\Utilities;

use \CryptoManana\Core\Abstractions\Containers\AbstractRandomnessInjectable as RandomnessContainer;
use \CryptoManana\Core\Interfaces\Randomness\EncryptionKeyGenerationInterface as EncryptionKeyGeneration;
use \CryptoManana\Core\Interfaces\Randomness\HashingKeyGenerationInterface as HashingKeyGeneration;
use \CryptoManana\Core\Interfaces\Randomness\TokenGenerationInterface as TokenStringGeneration;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class TokenGenerator - Utility class for cryptography token generation.
 *
 * @package CryptoManana\Utilities
 */
class TokenGenerator extends RandomnessContainer implements
    TokenStringGeneration,
    HashingKeyGeneration,
    EncryptionKeyGeneration
{
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
     * Generate a random token string in alphanumeric or hexadecimal format.
     *
     * Note: This method can generate HEX output if the `$useAlphaNumeric` parameter is set to `false`.
     *
     * @param int $length The desired output length (default => 40).
     * @param bool|int $useAlphaNumeric Flag for switching to alphanumerical (default => true).
     *
     * @return string Randomly generated alphanumeric/hexadecimal token string.
     * @throws \Exception Validation errors.
     */
    public function getTokenString($length = 40, $useAlphaNumeric = true)
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
     * @param int $length The desired output length (default => 10).
     * @param bool|int $stronger Flag for using all printable ASCII characters (default => true).
     *
     * @return string Randomly generated password string.
     * @throws \Exception Validation errors.
     */
    public function getPasswordString($length = 10, $stronger = true)
    {
        $this->applyLengthValidation($length);

        if ($stronger) {
            $password = $this->randomnessSource->getAscii($length);
        } else {
            $password = '';

            for ($i = 1; $i <= $length; $i++) {
                $tmp = $this->randomnessSource->getInt(1, 4);

                switch ($tmp) {
                    case 1:
                        $password .= $this->randomnessSource->getDigit(true);
                        break;
                    case 2:
                        $password .= $this->randomnessSource->getLetter(false);
                        break;
                    case 3:
                        $password .= StringBuilder::stringToUpper($this->randomnessSource->getLetter(false));
                        break;
                    default:
                        $password .= $this->randomnessSource->getString(1, ['!', '@', '#', '$', '%', '^']);
                        break;
                }
            }
        }

        return $password;
    }

    /**
     * Generate a random HMAC key for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated HMAC key.
     * @throws \Exception Validation errors.
     */
    public function getHashingKey($length = 128, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random salt string for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated hashing salt.
     * @throws \Exception Validation errors.
     */
    public function getHashingSalt($length = 128, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random encryption key for symmetrical cyphers.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption key.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionKey($length = 128, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }

    /**
     * Generate a random initialization vector (IV) for encryption purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption initialization vector.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionInitializationVector($length = 128, $printable = true)
    {
        $this->applyLengthValidation($length);

        return ($printable) ? $this->randomnessSource->getAscii($length) : $this->randomnessSource->getBytes($length);
    }
}
