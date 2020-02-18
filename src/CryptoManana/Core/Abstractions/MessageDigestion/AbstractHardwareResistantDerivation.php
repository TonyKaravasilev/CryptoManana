<?php

/**
 * Abstraction for the strong/slow digestion algorithm objects that are resistant to hardware computational attacks.
 */

namespace CryptoManana\Core\Abstractions\MessageDigestion;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractPasswordBasedDerivationFunction as PasswordDerivation;
use CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class AbstractHardwareResistantDerivation - The hardware resistant digestion algorithm abstraction representation.
 *
 * @package CryptoManana\Core\Abstractions\MessageDigestion
 */
abstract class AbstractHardwareResistantDerivation extends PasswordDerivation
{
    /**
     * The internal maximum length in bytes of the raw output digest for the algorithm.
     */
    const ALGORITHM_MAXIMUM_OUTPUT = PHP_INT_MAX;

    /**
     * The digest output format property storage.
     *
     * @var int The output format integer code value.
     */
    protected $digestFormat = self::DIGEST_OUTPUT_RAW;

    /**
     * Internal method for converting the digest's output format representation via the chosen format.
     *
     * @param string $digest The output digest.
     *
     * @return string The input data with proper salting.
     */
    protected function changeOutputFormat($digest)
    {
        switch ($this->digestFormat) {
            case self::DIGEST_OUTPUT_HEX_LOWER:
                $digest = bin2hex($digest);
                break;
            case self::DIGEST_OUTPUT_HEX_UPPER:
                $digest = StringBuilder::stringToUpper(bin2hex($digest));
                break;
            case self::DIGEST_OUTPUT_BASE_64:
                $digest = base64_encode($digest);
                break;
            case self::DIGEST_OUTPUT_BASE_64_URL:
                $digest = base64_encode($digest);
                $digest = StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], $digest);
                break;
            default: // case self::DIGEST_OUTPUT_RAW:
                break;
        }

        return $digest;
    }

    /**
     * Internal method for converting a formatted digest to raw bytes.
     *
     * @param string $digest The digest string.
     *
     * @return string The raw bytes digest representation.
     */
    protected function convertFormattedDigest($digest)
    {
        $hexCasePattern = '/^[a-f0-9]+$/';
        $base64Pattern = '%^[a-zA-Z0-9/+]*={0,2}$%';
        $base64UrlFriendlyPattern = '/^[a-zA-Z0-9_-]+$/';

        if (preg_match($hexCasePattern, StringBuilder::stringToLower($digest))) {
            $digest = hex2bin(StringBuilder::stringToLower($digest));
        } elseif (preg_match($base64Pattern, $digest) && StringBuilder::stringLength($digest) % 4 === 0) {
            $digest = base64_decode($digest);
        } elseif (preg_match($base64UrlFriendlyPattern, $digest)) {
            $digest = StringBuilder::stringReplace(['-', '_'], ['+', '/'], $digest);
            $digest .= str_repeat('=', StringBuilder::stringLength($digest) % 4);
            $digest = base64_decode($digest);
        }

        return $digest;
    }

    /**
     * Fetch the correctly formatted internal variation for digestion.
     *
     * @return int|string The chosen variation for password hashing.
     */
    abstract protected function fetchAlgorithmVariation();

    /**
     * Fetch the correctly formatted internal parameters for digestion.
     *
     * @return array The chosen parameters for password hashing.
     */
    abstract protected function fetchAlgorithmParameters();

    /**
     * Password-based key derivation algorithm constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'standard' => static::ALGORITHM_NAME,
            'type' => 'key stretching or password-based key derivation',
            'salt' => $this->salt,
            'mode' => $this->saltingMode,
            'algorithm variation version' => $this->fetchAlgorithmVariation(),
            'digestion parameters' => $this->fetchAlgorithmParameters(),
        ];
    }

    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashData($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data for hashing must be a string or a binary string.');
        }

        $data = $this->addSaltString($data);

        $digest = password_hash($data, $this->fetchAlgorithmVariation(), $this->fetchAlgorithmParameters());

        $digest = $this->changeOutputFormat($digest);

        return $digest;
    }

    /**
     * Securely compares and verifies if a digestion value is for the given input data.
     *
     * @param string $data The input string.
     * @param string $digest The digest string.
     *
     * @return bool The result of the secure comparison.
     * @throws \Exception Validation errors.
     */
    public function verifyHash($data, $digest)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data for hashing must be a string or a binary string.');
        } elseif (!is_string($digest)) {
            throw new \InvalidArgumentException('The digest must be a string or a binary string.');
        }

        $data = $this->addSaltString($data);

        $digest = $this->convertFormattedDigest($digest);

        return password_verify($data, $digest);
    }
}
