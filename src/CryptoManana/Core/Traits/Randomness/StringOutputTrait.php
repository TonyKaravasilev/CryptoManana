<?php

/**
 * Trait implementation of string format generation for generator services.
 */

namespace CryptoManana\Core\Traits\Randomness;

use \CryptoManana\Core\Traits\Randomness\RandomnessTrait as RandomnessSpecification;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait StringOutputTrait - Reusable implementation of `StringOutputInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Randomness\StringOutputInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Randomness
 *
 * @mixin RandomnessSpecification
 */
trait StringOutputTrait
{
    /**
     * Internal method for character map validation.
     *
     * @param array $charMap The character map array.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateCharacterMap(array $charMap)
    {
        foreach ($charMap as $char) {
            if (!is_string($char)) {
                throw new \InvalidArgumentException(
                    'The provided symbol map must contain only elements of type string.'
                );
            } elseif (StringBuilder::stringLength($char) != 1) {
                throw new \LengthException(
                    'The provided symbol map\'s values must only be of 1 character length.'
                );
            }
        }

        if (count($charMap) < 2) {
            throw new \LengthException(
                'You must supply a set of at least 2 characters for the output string generation.'
            );
        }
    }

    /**
     * Forcing the implementation of the software abstract randomness.
     *
     * {@internal Forcing the implementation of `AbstractRandomness`. }}
     */
    use RandomnessSpecification;

    /**
     * Generate a random digit character.
     *
     * @param bool $includeZero Flag for including the zero digit (default => true).
     *
     * @return string Randomly generated digit character.
     * @throws \Exception Validation errors.
     */
    public function getDigit($includeZero = true)
    {
        return ($includeZero) ? (string)$this->getInt(0, 9) : (string)$this->getInt(1, 9);
    }

    /**
     * Generate a random english letter character.
     *
     * @param bool $caseSensitive Flag for enabling case sensitive generation (default => true).
     *
     * @return string Randomly generated english letter character.
     * @throws \Exception Validation errors.
     */
    public function getLetter($caseSensitive = true)
    {
        if ($caseSensitive) {
            $upper = $this->getBool();

            $letterCode = $upper ? $this->getInt(65, 90) : $this->getInt(97, 122);
        } else {
            $letterCode = $this->getInt(97, 122);
        }

        return StringBuilder::getChr($letterCode);
    }

    /**
     * Generate a random alphanumeric string.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $caseSensitive Flag for enabling case sensitive generation (default => true).
     *
     * @return string Randomly generated alphanumeric string.
     * @throws \Exception Validation errors.
     */
    public function getAlphaNumeric($length = 1, $caseSensitive = true)
    {
        $this->validatePositiveInteger($length);

        $id = '';

        for ($i = 1; $i <= $length; $i++) {
            if ($this->getBool()) {
                $id .= $this->getLetter($caseSensitive);
            } else {
                $id .= $this->getDigit(true);
            }
        }

        return $id;
    }

    /**
     * Generate a random ASCII (American Standard Code) string containing only printable characters.
     *
     * @param int $length The output string length (default => 1).
     * @param bool|int $includeSpace Flag for including the space character (default => true).
     *
     * @return string Randomly generated ASCII string.
     * @throws \Exception Validation errors.
     */
    public function getAscii($length = 1, $includeSpace = false)
    {
        $this->validatePositiveInteger($length);

        $asciiString = '';
        $startFrom = ($includeSpace == true) ? 32 : 33;

        for ($i = 1; $i <= $length; $i++) {
            $asciiString .= StringBuilder::getChr($this->getInt($startFrom, 126));
        }

        return $asciiString;
    }

    /**
     * Generate a random string with custom characters.
     *
     * @param int $length The output string length (default => 1).
     * @param array $characters The character map for the string generation (default => ASCII).
     *
     * @return string Randomly generated string using a custom character map.
     * @throws \Exception Validation errors.
     */
    public function getString($length = 1, array $characters = [])
    {
        if (empty($characters)) {
            return $this->getAscii($length, true);
        } else {
            $this->validatePositiveInteger($length);

            $this->validateCharacterMap($characters);

            $passwordString = '';
            $lastIndex = count($characters) - 1;

            for ($i = 1; $i <= $length; $i++) {
                $passwordString .= $characters[$this->getInt(0, $lastIndex)];
            }

            return $passwordString;
        }
    }
}
