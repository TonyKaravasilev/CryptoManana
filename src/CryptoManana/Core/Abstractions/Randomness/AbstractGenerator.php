<?php

/**
 * Abstraction for pseudo-random generator objects.
 */

namespace CryptoManana\Core\Abstractions\Randomness;

use \CryptoManana\Core\Abstractions\Randomness\AbstractRandomness as RandomnessRepresentation;
use \CryptoManana\Core\Interfaces\Randomness\InterfaceFloatOutput as FloatOutput;
use \CryptoManana\Core\Interfaces\Randomness\InterfaceArbitraryBaseOutput as BaseOutput;
use \CryptoManana\Core\Interfaces\Randomness\InterfaceStringOutput as StringOutput;
use \CryptoManana\Core\Interfaces\Randomness\InterfaceIdentifierOutput as UuidOutput;
use \CryptoManana\Core\Interfaces\Randomness\InterfaceRgbOutput as RgbOutput;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Class AbstractGenerator - Abstraction for pseudo-random generator classes.
 *
 * @package CryptoManana\Core\Abstractions\Randomness
 */
abstract class AbstractGenerator extends RandomnessRepresentation implements
    FloatOutput,
    BaseOutput,
    StringOutput,
    UuidOutput,
    RgbOutput
{
    /**
     * The default system precision storage.
     *
     * @var int|null The used default floating number precision.
     */
    protected static $systemPrecision = null;

    /**
     * Internal method for converting RGB integer colours to HEX notations.
     *
     * @param array $rgbColour An array containing three integers between 0 and 255.
     *
     * @return string The HEX representation of the RGB colour pair.
     */
    protected function calculateRgbArrayToHexString(array $rgbColour)
    {
        $pairCount = count($rgbColour);

        for ($i = 0; $i < $pairCount; $i++) {
            $rgbColour[$i] = ($rgbColour[$i] <= 15) ? '0' . dechex($rgbColour[$i]) : dechex($rgbColour[$i]);
        }

        return ($pairCount > 0) ? '#' . implode('', $rgbColour) : '';
    }

    /**
     * Internal method for calculating the machine epsilon value based on the used precision.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision The wanted precision for the machine epsilon.
     *
     * @return float The machine epsilon used for floating number comparison operations.
     */
    protected function calculateEpsilon($precision = null)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        // Calculate epsilon based on precision digits
        $epsilon = 0.1;

        for ($i = 1; $i < $precision; $i++) {
            $epsilon *= 0.1;
        }

        return $epsilon;
    }

    /**
     * Internal method for integer range validation.
     *
     * @param int $from The minimum number in the wanted range.
     * @param int $to The maximum number in the wanted range.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateIntegerRange($from, $to)
    {
        $from = filter_var(
            $from,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $this->getMinNumber(),
                    "max_range" => $this->getMaxNumber(),
                ],
            ]
        );

        $to = filter_var(
            $to,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $this->getMinNumber(),
                    "max_range" => $this->getMaxNumber(),
                ],
            ]
        );

        if ($from === false || $to === false) {
            throw new \DomainException(
                "The provided values are of invalid type or are out of the supported range."
            );
        }

        if ($from >= $to) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }
    }

    /**
     * Internal method for double range validation.
     *
     * @param int|float $from The minimum number in the wanted range.
     * @param int|float $to The maximum number in the wanted range.
     * @param null|int $precision The used precision for comparison.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateDoubleRange($from, $to, $precision = 14)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        if ($from < (float)$this->getMinNumber() || $to > (float)$this->getMaxNumber()) {
            throw new \DomainException(
                "The provided values are out of the supported range."
            );
        }

        if ($from > $to) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }

        $epsilon = $this->calculateEpsilon($precision);

        $difference = abs($from - $to);

        if ($difference < $epsilon) {
            throw new \LogicException(
                "The chosen generation maximum is less or equal the provided minimum value."
            );
        }
    }

    /**
     * Internal method for validation of positive integers.
     *
     * @param int $integer The positive integer value.
     * @param bool $includeZero Flag for enabling the zero as a valid value.
     *
     * @throws \Exception Validation errors.
     */
    protected function validatePositiveInteger($integer, $includeZero = false)
    {
        $integer = filter_var(
            $integer,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => $includeZero ? 0 : 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($integer === false) {
            throw new \DomainException('The provided value must be a positive integer.');
        }
    }

    /**
     * Randomness generator constructor.
     */
    public function __construct()
    {
        // Fetch the global system precision setting
        if (is_null(self::$systemPrecision)) {
            self::$systemPrecision = (int)ini_get('precision');
        }
    }

    /**
     * Randomness generator reinitialization tasks after unserialization.
     */
    public function __wakeup()
    {
        // Ensures randomness is reinitialized and auto-seeded
        $this->__construct();
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            'systemPrecision' => self::$systemPrecision,
        ];
    }

    /**
     * Generate a probability format float number between 0.0 and 1.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 10).
     *
     * @return float Randomly generated probability value.
     * @throws \Exception Validation errors.
     */
    public function getProbability($precision = 10)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        $number = $this->getInt(0, $this->getMaxNumber());

        $isNotRangeBoarders = ($number !== 0 && $number !== $this->getMaxNumber());

        $number = ($number === 0) ? 0.00 : ($number === $this->getMaxNumber()) ? 100.00 : (float)$number;

        $number = $isNotRangeBoarders ? round($number / (float)$this->getMaxNumber(), $precision) : $number;

        return $number;
    }

    /**
     * Generate a random float number in a certain range.
     *
     * Note: Passing `null` will use the default parameter value or for precision the global system value.
     *
     * @param null|float|int $from The lowest value to be returned (default => 0.0).
     * @param null|float|int $to The highest value to be returned (default => (float)$this->getMaxNumber()).
     * @param null|int $precision Rounding precision (default => 8).
     *
     * @return float Randomly generated float value.
     * @throws \Exception Validation errors.
     */
    public function getFloat($from = 0.0, $to = null, $precision = 8)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        $fromInvalidType = !in_array(gettype($from), ['integer', 'double', 'NULL']);
        $toInvalidType = !in_array(gettype($to), ['integer', 'double', 'NULL']);

        if ($fromInvalidType || $toInvalidType) {
            throw new \DomainException(
                "The provided values are of invalid type."
            );
        }

        $from = ($from === null) ? 0.0 : (float)$from;
        $to = ($to === null) ? (float)$this->getMaxNumber() : (float)$to;

        $this->validateDoubleRange($from, $to, $precision);

        $epsilon = $this->calculateEpsilon($precision);

        $toIsTheMaximum = abs($this->getMaxNumber() - $to) < $epsilon;
        $fromIsTheZero = abs(0.0 - $from) < $epsilon;

        $fromIsTheMinimumPlusOne = abs(($this->getMinNumber() + 1.0) - $from) < $epsilon;
        $toIsTheZero = abs(0.0 - $to) < $epsilon;

        // Improves the overall calculation quality for range calls
        if ($toIsTheMaximum && $fromIsTheZero) {
            $from = 0.01;
        } elseif ($toIsTheZero && $fromIsTheMinimumPlusOne) {
            $to = 0.01;
        }

        // Minimum precision for probability fetching
        $scope = ($precision > 14) ? $precision : 14;

        return round($from + $this->getProbability($scope) * abs($to - $from), $precision);
    }

    /**
     * Generate a percentage format float number between 0.0 and 100.0.
     *
     * Note: Passing `null` will use the global system precision value.
     *
     * @param null|int $precision Rounding precision (default => 2).
     * @param bool|int $lowerTheScope Flag for using a smaller calculation range.
     *
     * @return float Randomly generated percentage value.
     * @throws \Exception Validation errors.
     */
    public function getPercent($precision = 2, $lowerTheScope = false)
    {
        $precision = ($precision === null) ? self::$systemPrecision : $precision;

        $this->validatePositiveInteger($precision, true);

        if ($lowerTheScope) {
            $max = 9999; // 0-9999

            $number = $this->getInt(0, $max);

            $isNotRangeBoarders = ($number !== 0 && $number !== $max);

            $number = ($number === 0) ? 0.00 : ($number === $max) ? 100.00 : $number;

            $number = $isNotRangeBoarders ? ($number / $max) * 100.00 : $number;
        } else {
            // Minimum precision for probability fetching
            $scope = ($precision > 14) ? $precision : 14;

            $number = $this->getProbability($scope) * 100.00;
        }

        return round($number, $precision);
    }

    /**
     * Generate a random boolean.
     *
     * @return bool Randomly generated boolean value.
     * @throws \Exception Validation errors.
     */
    public function getBool()
    {
        return $this->getInt() % 2 === 0;
    }

    /**
     * Generate a random ternary format (-1, 0, 1).
     *
     * Note: Passing `false` to the `$asInteger` parameter will convert values to `null`, `false` and `true`.
     *
     * @param bool|int $asInteger Flag for returning as integer (default => true).
     *
     * @return bool|int Randomly generated ternary value.
     * @throws \Exception Validation errors.
     */
    public function getTernary($asInteger = true)
    {
        $ternary = $this->getInt(-1, 1);

        if ($asInteger) {
            return $ternary;
        } else {
            switch ($ternary) {
                case 1:
                    return true;
                case -1:
                    return null;
                default: // case 0:
                    return false;
            }
        }
    }

    /**
     * Generate a random HEX string.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $upperCase Flag for using uppercase output (default => false).
     *
     * @return string Randomly generated HEX string.
     * @throws \Exception Validation errors.
     */
    public function getHex($length = 1, $upperCase = false)
    {
        $hexString = bin2hex($this->getBytes($length));

        return ($upperCase) ? StringBuilder::stringToUpper($hexString) : $hexString;
    }

    /**
     * Generate a random Base64 string.
     *
     * @param int $length The internal byte string length (default => 1).
     * @param bool $urlFriendly Flag for using URL friendly output (default => false).
     *
     * @return string Randomly generated Base64 RFC 4648 standard string.
     * @throws \Exception Validation errors.
     */
    public function getBase64($length = 1, $urlFriendly = false)
    {
        $base64 = base64_encode($this->getBytes($length));

        if ($urlFriendly) {
            return StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], $base64);
        } else {
            return $base64;
        }
    }

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
            foreach ($characters as $char) {
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

            if (count($characters) < 2) {
                throw new \LengthException(
                    'You must supply a set of at least 2 characters for the output string generation.'
                );
            }

            $this->validatePositiveInteger($length);

            $passwordString = '';
            $lastIndex = count($characters) - 1;

            for ($i = 1; $i <= $length; $i++) {
                $passwordString .= $characters[$this->getInt(0, $lastIndex)];
            }

            return $passwordString;
        }
    }

    /**
     * Generate a random version 4 Globally Unique Identifier (GUID) standard string.
     *
     * Note: The identifier string uses 32 alphanumeric characters and 4 hyphens (optional).
     *
     * @param string $prefix Optional prefix for output strings (default => '').
     * @param bool $withDashes Flag for using dashes format (default => true).
     * @param bool $upperCase Flag for using uppercase format (default => false).
     *
     * @return string Randomly generated GUID string representing a 128-bit number.
     * @throws \Exception Validation errors.
     */
    public function getGloballyUniqueId($prefix = '', $withDashes = true, $upperCase = false)
    {
        $tmp = $this->getBytes(16);

        $tmp[6] = StringBuilder::getChr(StringBuilder::getOrd($tmp[6]) & 0x0f | 0x40);
        $tmp[8] = StringBuilder::getChr(StringBuilder::getOrd($tmp[8]) & 0x3f | 0x80);

        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', StringBuilder::stringSplit(bin2hex($tmp), 4));

        $id = ($withDashes) ? $id : StringBuilder::stringReplace('-', '', $id);

        $id = ($upperCase) ? StringBuilder::stringToUpper($id) : $id;

        return StringBuilder::stringFullTrimming($prefix) . $id;
    }

    /**
     * Generate a strong Universally Unique Identifier (UUID) string in hexadecimal or alphanumeric format.
     *
     * Note: The identifier string is exactly 128 characters long.
     *
     * @param string $prefix Optional prefix for output strings (default => '').
     * @param bool $alphaNumeric Flag for switching to alphanumerical format (default => false).
     *
     * @return string Randomly generated strong hexadecimal/alphanumerical UUID string.
     * @throws \Exception Validation errors.
     */
    public function getStrongUniqueId($prefix = '', $alphaNumeric = false)
    {
        if ($alphaNumeric) {
            $id = $this->getAlphaNumeric(128);

            $id = $this->getBool() ? StringBuilder::stringReverse($id) : $id;
        } else {
            $id = hash_hmac(
                'sha512', // exactly 128 chars output (1024-bit)
                $this->getBytes(64), // 512-bit input
                $this->getBytes(64)  // 512-bit key
            );

            $id = StringBuilder::stringToUpper($id);
        }

        return StringBuilder::stringFullTrimming($prefix) . $id;
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using all colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbColourPair($toArray = true)
    {
        $rgb = [$this->getInt(0, 255), $this->getInt(0, 255), $this->getInt(0, 255)];

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only greyscale colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbGreyscalePair($toArray = true)
    {
        $grayChart = [
            [255, 255, 255], // white
            [220, 220, 220], // gainsboro
            [211, 211, 211], // lightgrey
            [192, 192, 192], // silver
            [169, 169, 169], // darkgray
            [128, 128, 128], // gray
            [105, 105, 105], // dimgray
            [0, 0, 0] // black
        ];

        $rgb = $grayChart[$this->getInt(0, 255) % 8]; // A bit faster

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }

    /**
     * Generate a random Red-Green-Blue (RGB) colour combination using only black&white colours.
     *
     * @param bool $toArray Flag to force array output instead of string (default => true).
     *
     * @return array|string Randomly generated RGB array or hexadecimal RGB color.
     * @throws \Exception Validation errors.
     */
    public function getRgbBlackOrWhitePair($toArray = true)
    {
        $rgb = $this->getBool() ? [0, 0, 0] : [255, 255, 255];

        return ($toArray) ? $rgb : $this->calculateRgbArrayToHexString($rgb);
    }
}
