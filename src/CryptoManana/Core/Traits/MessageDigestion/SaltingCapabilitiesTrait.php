<?php

/**
 * Trait implementation of the salting capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\SaltingCapabilitiesInterface as SaltingCapabilitiesSpecification;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait SaltingCapabilitiesTrait - Reusable implementation of `SaltingCapabilitiesInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\SaltingCapabilitiesInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property string $salt The salt string property storage.
 * @property int $saltingMode The salting mode property storage.
 *
 * @mixin SaltingCapabilitiesSpecification
 */
trait SaltingCapabilitiesTrait
{
    /**
     * List of salting modes that add the salt at the front side of the input data.
     *
     * @var array Salting mode codes.
     */
    private $inFrontCases = [
        self::SALTING_MODE_PREPEND,
        self::SALTING_MODE_REVERSE_PREPEND,
        self::SALTING_MODE_DUPLICATE_PREFIX
    ];

    /**
     * List of salting modes that add the salt at the back side of the input data.
     *
     * @var array Salting mode codes.
     */
    private $inBackCases = [
        self::SALTING_MODE_APPEND,
        self::SALTING_MODE_REVERSE_APPEND,
        self::SALTING_MODE_DUPLICATE_SUFFIX
    ];

    /**
     * List of salting modes that use complex salt and input data manipulation procedures.
     *
     * @var array Salting mode codes.
     */
    private $inSpecialCases = [
        self::SALTING_MODE_INFIX_INPUT,
        self::SALTING_MODE_INFIX_SALT,
        self::SALTING_MODE_PALINDROME_MIRRORING
    ];

    /**
     * Internal method for grouping salting modes that add to the front of the input data.
     *
     * @param string $data The input data for hashing.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    private function saltAtFront(&$data)
    {
        switch ($this->saltingMode) {
            case self::SALTING_MODE_PREPEND: // SALTpassword
                $data = $this->salt . $data;
                break;
            case self::SALTING_MODE_REVERSE_PREPEND: // TLASpassword
                $data = StringBuilder::stringReverse($this->salt) . $data;
                break;
            case self::SALTING_MODE_DUPLICATE_PREFIX: // SALTTLASpassword
                $data = $this->salt . StringBuilder::stringReverse($this->salt) . $data;
                break;
        }
    }

    /**
     * Internal method for grouping salting modes that add to the back of the input data.
     *
     * @param string $data The input data for hashing.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    private function saltAtBack(&$data)
    {
        switch ($this->saltingMode) {
            case self::SALTING_MODE_APPEND: // passwordSALT
                $data .= $this->salt;
                break;
            case self::SALTING_MODE_REVERSE_APPEND: // passwordTLAS
                $data .= StringBuilder::stringReverse($this->salt);
                break;
            case self::SALTING_MODE_DUPLICATE_SUFFIX: // passwordSALTTLAS
                $data = $data . $this->salt . StringBuilder::stringReverse($this->salt);
                break;
        }
    }

    /**
     * Internal method for grouping salting modes that use complex manipulations of the salt and input data.
     *
     * @param string $data The input data for hashing.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    private function saltAtSpecial(&$data)
    {
        switch ($this->saltingMode) {
            case self::SALTING_MODE_INFIX_INPUT: // SALTpasswordTLAS
                $data = $this->salt . $data . StringBuilder::stringReverse($this->salt);
                break;
            case self::SALTING_MODE_INFIX_SALT: // passwordSALTdrowssap
                $data = $data . $this->salt . StringBuilder::stringReverse($data);
                break;
            case self::SALTING_MODE_PALINDROME_MIRRORING: // SALTpassworddrowssapTLAS
                $data = $this->salt . $data . StringBuilder::stringReverse($data);
                $data .= StringBuilder::stringReverse($this->salt);
                break;
        }
    }

    /**
     * Internal method for adding the salt string to the input data via the chosen salting mode.
     *
     * @param string $data The input data for hashing.
     *
     * @return string The input data with proper salting.
     */
    protected function addSaltString($data)
    {
        if ($this->saltingMode === self::SALTING_MODE_NONE) {
            return $data;
        }

        if (in_array($this->saltingMode, $this->inBackCases)) {
            $this->saltAtBack($data);
        } elseif (in_array($this->saltingMode, $this->inFrontCases)) {
            $this->saltAtFront($data);
        } elseif (in_array($this->saltingMode, $this->inSpecialCases)) {
            $this->saltAtSpecial($data);
        }

        return $data;
    }

    /**
     * Setter for the salt string property.
     *
     * @param string $salt The salt string.
     *
     * @return $this The hash algorithm object.
     * @throw \Exception Validation errors.
     */
    public function setSalt($salt)
    {
        if (!is_string($salt)) {
            throw new \InvalidArgumentException('Salt must be of type string.');
        }

        $this->salt = $salt;

        return $this;
    }

    /**
     * Getter for the salt string property.
     *
     * @return string The salt string.
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Setter for the salting mode code property.
     *
     * @param int $saltingMode The salting mode code.
     *
     * @return $this The hash algorithm object.
     * @throw \Exception Validation errors.
     */
    public function setSaltingMode($saltingMode)
    {
        $saltingMode = filter_var(
            $saltingMode,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => self::SALTING_MODE_NONE, // -1
                    "max_range" => self::SALTING_MODE_PALINDROME_MIRRORING, // 8
                ],
            ]
        );

        if ($saltingMode === false) {
            throw new \InvalidArgumentException('Salting mode must be an integer between -1 and 8.');
        }

        $this->saltingMode = $saltingMode;

        return $this;
    }

    /**
     * Getter for the salt mode code property.
     *
     * @return int The salt mode code.
     */
    public function getSaltingMode()
    {
        return $this->saltingMode;
    }
}
