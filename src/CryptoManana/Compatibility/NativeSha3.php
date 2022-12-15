<?php

/**
 * The SHA-3 pure PHP implementation that is compatible with PHP versions before 7.1 and older `hash` extensions.
 */

namespace CryptoManana\Compatibility;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;
use CryptoManana\Core\Traits\DesignPatterns\SingleInstancingTrait as SingleInstancingImplementation;

/**
 * Class NativeSha3 - Pure PHP implementation of the SHA-3 algorithm.
 *
 * @package CryptoManana\Compatibility
 */
class NativeSha3 extends SingletonPattern
{
    /**
     * Single instancing implementation.
     *
     * {@internal Reusable implementation of `SingleInstancingInterface`. }}
     */
    use SingleInstancingImplementation;

    /**
     * Internal algorithm rounds count.
     */
    const KECCAK_ROUNDS = 24;

    /**
     * Internal algorithm suffix byte.
     */
    const KECCAK_SUFIX = 0x06;

    /**
     * Internal flag marking if the PHP version is x64 based.
     *
     * Note: `null` => auto-check on next call, `true` => x64, `false` => x32.
     *
     * @var null|bool Is it a x64 system.
     */
    protected static $isX64 = null;

    /**
     * Internal flag to enable or disable the `mbstring` extension usage.
     *
     * Note: `null` => auto-check on next call, `true` => available, `false` => not available.
     *
     * @var null|bool Is the `mbstring` extension supported.
     */
    protected static $mbString = null;

    /**
     * Internal algorithm hardcoded data.
     *
     * @var array Internal data for manipulations.
     */
    protected static $fKeccakRotc = [
        1,
        3,
        6,
        10,
        15,
        21,
        28,
        36,
        45,
        55,
        2,
        14,
        27,
        41,
        56,
        8,
        25,
        43,
        62,
        18,
        39,
        61,
        20,
        44,
    ];

    /**
     * Internal algorithm hardcoded data.
     *
     * @var array Internal data for manipulations.
     */
    protected static $fKeccakPiln = [
        10,
        7,
        11,
        17,
        18,
        3,
        5,
        16,
        8,
        21,
        24,
        4,
        15,
        23,
        19,
        13,
        12,
        2,
        20,
        14,
        22,
        9,
        6,
        1,
    ];

    /**
     * Get different hardcoded data for internal manipulations depending on the system word size.
     *
     * @return array Internal data for manipulations.
     */
    protected static function getRndcArray()
    {
        if (self::$isX64) {
            return [
                [0x00000000, 0x00000001],
                [0x00000000, 0x00008082],
                [0x80000000, 0x0000808a],
                [0x80000000, 0x80008000],
                [0x00000000, 0x0000808b],
                [0x00000000, 0x80000001],
                [0x80000000, 0x80008081],
                [0x80000000, 0x00008009],
                [0x00000000, 0x0000008a],
                [0x00000000, 0x00000088],
                [0x00000000, 0x80008009],
                [0x00000000, 0x8000000a],
                [0x00000000, 0x8000808b],
                [0x80000000, 0x0000008b],
                [0x80000000, 0x00008089],
                [0x80000000, 0x00008003],
                [0x80000000, 0x00008002],
                [0x80000000, 0x00000080],
                [0x00000000, 0x0000800a],
                [0x80000000, 0x8000000a],
                [0x80000000, 0x80008081],
                [0x80000000, 0x00008080],
                [0x00000000, 0x80000001],
                [0x80000000, 0x80008008],
            ];
        } else {
            return [
                [0x0000, 0x0000, 0x0000, 0x0001],
                [0x0000, 0x0000, 0x0000, 0x8082],
                [0x8000, 0x0000, 0x0000, 0x0808a],
                [0x8000, 0x0000, 0x8000, 0x8000],
                [0x0000, 0x0000, 0x0000, 0x808b],
                [0x0000, 0x0000, 0x8000, 0x0001],
                [0x8000, 0x0000, 0x8000, 0x08081],
                [0x8000, 0x0000, 0x0000, 0x8009],
                [0x0000, 0x0000, 0x0000, 0x008a],
                [0x0000, 0x0000, 0x0000, 0x0088],
                [0x0000, 0x0000, 0x8000, 0x08009],
                [0x0000, 0x0000, 0x8000, 0x000a],
                [0x0000, 0x0000, 0x8000, 0x808b],
                [0x8000, 0x0000, 0x0000, 0x008b],
                [0x8000, 0x0000, 0x0000, 0x08089],
                [0x8000, 0x0000, 0x0000, 0x8003],
                [0x8000, 0x0000, 0x0000, 0x8002],
                [0x8000, 0x0000, 0x0000, 0x0080],
                [0x0000, 0x0000, 0x0000, 0x0800a],
                [0x8000, 0x0000, 0x8000, 0x000a],
                [0x8000, 0x0000, 0x8000, 0x8081],
                [0x8000, 0x0000, 0x0000, 0x8080],
                [0x0000, 0x0000, 0x8000, 0x00001],
                [0x8000, 0x0000, 0x8000, 0x8008],
            ];
        }
    }

    /**
     * Internal data manipulation for the Keccak algorithm.
     *
     * @param array $state The state matrix.
     * @param int $rounds The rounds count.
     */
    protected static function fKeccakAlgorithm(&$state, $rounds)
    {
        $fKeccakRndc = self::getRndcArray();

        $bc = [];

        for ($round = 0; $round < $rounds; $round++) {
            // Theta
            for ($i = 0; $i < 5; $i++) {
                if (self::$isX64) {
                    $bc[$i] = [
                        $state[$i][0] ^ $state[$i + 5][0] ^ $state[$i + 10][0] ^
                        $state[$i + 15][0] ^ $state[$i + 20][0],

                        $state[$i][1] ^ $state[$i + 5][1] ^ $state[$i + 10][1] ^
                        $state[$i + 15][1] ^ $state[$i + 20][1],
                    ];
                } else {
                    $bc[$i] = [
                        $state[$i][0] ^ $state[$i + 5][0] ^ $state[$i + 10][0] ^
                        $state[$i + 15][0] ^ $state[$i + 20][0],

                        $state[$i][1] ^ $state[$i + 5][1] ^ $state[$i + 10][1] ^
                        $state[$i + 15][1] ^ $state[$i + 20][1],

                        $state[$i][2] ^ $state[$i + 5][2] ^ $state[$i + 10][2] ^
                        $state[$i + 15][2] ^ $state[$i + 20][2],

                        $state[$i][3] ^ $state[$i + 5][3] ^ $state[$i + 10][3] ^
                        $state[$i + 15][3] ^ $state[$i + 20][3],
                    ];
                }
            }

            for ($i = 0; $i < 5; $i++) {
                if (self::$isX64) {
                    $tmp = [
                        $bc[($i + 4) % 5][0] ^ (($bc[($i + 1) % 5][0] << 1) |
                            ($bc[($i + 1) % 5][1] >> 31)) & (0xFFFFFFFF),

                        $bc[($i + 4) % 5][1] ^ (($bc[($i + 1) % 5][1] << 1) |
                            ($bc[($i + 1) % 5][0] >> 31)) & (0xFFFFFFFF),
                    ];
                } else {
                    $tmp = [
                        $bc[($i + 4) % 5][0] ^ ((($bc[($i + 1) % 5][0] << 1) |
                                ($bc[($i + 1) % 5][1] >> 15)) & (0xFFFF)),

                        $bc[($i + 4) % 5][1] ^ ((($bc[($i + 1) % 5][1] << 1) |
                                ($bc[($i + 1) % 5][2] >> 15)) & (0xFFFF)),

                        $bc[($i + 4) % 5][2] ^ ((($bc[($i + 1) % 5][2] << 1) |
                                ($bc[($i + 1) % 5][3] >> 15)) & (0xFFFF)),

                        $bc[($i + 4) % 5][3] ^ ((($bc[($i + 1) % 5][3] << 1) |
                                ($bc[($i + 1) % 5][0] >> 15)) & (0xFFFF)),
                    ];
                }

                for ($j = 0; $j < 25; $j += 5) {
                    if (self::$isX64) {
                        $state[$j + $i] = [
                            $state[$j + $i][0] ^ $tmp[0],
                            $state[$j + $i][1] ^ $tmp[1],
                        ];
                    } else {
                        $state[$j + $i] = [
                            $state[$j + $i][0] ^ $tmp[0],
                            $state[$j + $i][1] ^ $tmp[1],
                            $state[$j + $i][2] ^ $tmp[2],
                            $state[$j + $i][3] ^ $tmp[3],
                        ];
                    }
                }
            }

            // Rho Pi
            $tmp = $state[1];

            for ($i = 0; $i < 24; $i++) {
                $j = self::$fKeccakPiln[$i];
                $bc[0] = $state[$j];

                if (self::$isX64) {
                    $n = self::$fKeccakRotc[$i];
                    $hi = $tmp[0];
                    $lo = $tmp[1];

                    if ($n >= 32) {
                        $n -= 32;
                        $hi = $tmp[1];
                        $lo = $tmp[0];
                    }

                    $state[$j] = [
                        (($hi << $n) | ($lo >> (32 - $n))) & (0xFFFFFFFF),
                        (($lo << $n) | ($hi >> (32 - $n))) & (0xFFFFFFFF),
                    ];
                } else {
                    $n = self::$fKeccakRotc[$i] >> 4;
                    $m = self::$fKeccakRotc[$i] % 16;

                    $state[$j] = [
                        ((($tmp[(0 + $n) % 4] << $m) | ($tmp[(1 + $n) % 4] >> (16 - $m))) & (0xFFFF)),
                        ((($tmp[(1 + $n) % 4] << $m) | ($tmp[(2 + $n) % 4] >> (16 - $m))) & (0xFFFF)),
                        ((($tmp[(2 + $n) % 4] << $m) | ($tmp[(3 + $n) % 4] >> (16 - $m))) & (0xFFFF)),
                        ((($tmp[(3 + $n) % 4] << $m) | ($tmp[(0 + $n) % 4] >> (16 - $m))) & (0xFFFF)),
                    ];
                }

                $tmp = $bc[0];
            }

            // Chi
            for ($j = 0; $j < 25; $j += 5) {
                for ($i = 0; $i < 5; $i++) {
                    $bc[$i] = $state[$j + $i];
                }

                for ($i = 0; $i < 5; $i++) {
                    if (self::$isX64) {
                        $state[$j + $i] = [
                            $state[$j + $i][0] ^ ~$bc[($i + 1) % 5][0] & $bc[($i + 2) % 5][0],
                            $state[$j + $i][1] ^ ~$bc[($i + 1) % 5][1] & $bc[($i + 2) % 5][1],
                        ];
                    } else {
                        $state[$j + $i] = [
                            $state[$j + $i][0] ^ ~$bc[($i + 1) % 5][0] & $bc[($i + 2) % 5][0],
                            $state[$j + $i][1] ^ ~$bc[($i + 1) % 5][1] & $bc[($i + 2) % 5][1],
                            $state[$j + $i][2] ^ ~$bc[($i + 1) % 5][2] & $bc[($i + 2) % 5][2],
                            $state[$j + $i][3] ^ ~$bc[($i + 1) % 5][3] & $bc[($i + 2) % 5][3],
                        ];
                    }
                }
            }

            // Iota
            if (self::$isX64) {
                $state[0] = [
                    $state[0][0] ^ $fKeccakRndc[$round][0],
                    $state[0][1] ^ $fKeccakRndc[$round][1],
                ];
            } else {
                $state[0] = [
                    $state[0][0] ^ $fKeccakRndc[$round][0],
                    $state[0][1] ^ $fKeccakRndc[$round][1],
                    $state[0][2] ^ $fKeccakRndc[$round][2],
                    $state[0][3] ^ $fKeccakRndc[$round][3],
                ];
            }
        }
    }

    /**
     * The internal Keccak native implementation.
     *
     * @param string|mixed $inputBytes The data for hashing.
     * @param int $outputLength The output length for the algorithm.
     * @param int $algorithmSuffix The used integer suffix for the algorithm.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output digest.
     */
    protected static function keccakAlgorithm($inputBytes, $outputLength, $algorithmSuffix, $rawOutput)
    {
        $capacity = $outputLength;

        $capacity /= 8;

        $inputLength = self::binarySafeStrLength($inputBytes);

        $rSize = 200 - 2 * $capacity;
        $rSizeWidth = $rSize / 8;

        $state = [];
        for ($i = 0; $i < 25; $i++) {
            if (self::$isX64) {
                $state[] = [0, 0];
            } else {
                $state[] = [0, 0, 0, 0];
            }
        }

        for ($inputIterator = 0; $inputLength >= $rSize; $inputLength -= $rSize, $inputIterator += $rSize) {
            for ($i = 0; $i < $rSizeWidth; $i++) {
                $tmp = unpack(
                    self::$isX64 ? 'V*' : 'v*',
                    self::binarySafeSubStr($inputBytes, $i * 8 + $inputIterator, 8)
                );

                if (self::$isX64) {
                    $state[$i] = [
                        $state[$i][0] ^ $tmp[2],
                        $state[$i][1] ^ $tmp[1],
                    ];
                } else {
                    $state[$i] = [
                        $state[$i][0] ^ $tmp[4],
                        $state[$i][1] ^ $tmp[3],
                        $state[$i][2] ^ $tmp[2],
                        $state[$i][3] ^ $tmp[1],
                    ];
                }
            }

            self::fKeccakAlgorithm($state, self::KECCAK_ROUNDS);
        }

        $tempData = self::binarySafeSubStr($inputBytes, $inputIterator, $inputLength);
        $tempData = str_pad($tempData, $rSize, "\x0", STR_PAD_RIGHT);

        // Note: mb_chr() is available only in PHP >= 7.2, so using chr() in ASCII 8-bit codes
        $tempData[$inputLength] = chr($algorithmSuffix);

        // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
        if (self::$isX64) {
            $tempData[$rSize - 1] = chr(ord($tempData[$rSize - 1]) | 0x80);
        } else {
            $tempData[$rSize - 1] = chr((int)$tempData[$rSize - 1] | 0x80);
        }

        for ($i = 0; $i < $rSizeWidth; $i++) {
            $tmp = unpack(
                self::$isX64 ? 'V*' : 'v*',
                self::binarySafeSubStr($tempData, $i * 8, 8)
            );

            if (self::$isX64) {
                $state[$i] = [
                    $state[$i][0] ^ $tmp[2],
                    $state[$i][1] ^ $tmp[1],
                ];
            } else {
                $state[$i] = [
                    $state[$i][0] ^ $tmp[4],
                    $state[$i][1] ^ $tmp[3],
                    $state[$i][2] ^ $tmp[2],
                    $state[$i][3] ^ $tmp[1],
                ];
            }
        }

        $tmp = null;

        self::fKeccakAlgorithm($state, self::KECCAK_ROUNDS);

        $output = '';

        for ($i = 0; $i < 25; $i++) {
            if (self::$isX64) {
                $output .= pack('V*', $state[$i][1], $state[$i][0]);
            } else {
                $output .= pack('v*', $state[$i][3], $state[$i][2], $state[$i][1], $state[$i][0]);
            }
        }

        $output = self::binarySafeSubStr($output, 0, $outputLength / 8);

        return ($rawOutput) ? $output : bin2hex($output);
    }

    /**
     * Get the string's length in 8-bit representation of raw bytes.
     *
     * @param string $string The string for length measuring.
     *
     * @return int The string's length.
     */
    protected static function binarySafeStrLength($string)
    {
        return self::$mbString ? mb_strlen($string, '8bit') : strlen($string);
    }

    /**
     * Return a part of a string in length via the 8-bit representation of raw bytes.
     *
     * @param string $string The input string
     * @param int $start The starting position.
     * @param int|null $length The length to take.
     *
     * @return bool|string The extracted part of string or false on failure.
     */
    protected static function binarySafeSubStr($string, $start = 0, $length = null)
    {
        return self::$mbString ? mb_substr($string, $start, $length, '8bit') : substr($string, $start, $length);
    }

    /**
     * Internal static method for single point consumption of the Keccak implementation.
     *
     * @param string|mixed $inputData The data for hashing.
     * @param int $outputLength The output length for the algorithm.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return bool|string The output digest for the given input parameters.
     * @throws \Exception Validation errors.
     */
    protected static function calculateDigest($inputData, $outputLength, $rawOutput = false)
    {
        if (self::$isX64 === null) {
            self::$isX64 = (PHP_INT_SIZE === 8);
        }

        if (self::$mbString === null) {
            self::$mbString = extension_loaded('mbstring');
        }

        if (!is_string($inputData)) {
            throw new \InvalidArgumentException('The input data parameter must be of type string.');
        }

        return self::keccakAlgorithm($inputData, $outputLength, self::KECCAK_SUFIX, $rawOutput);
    }

    /**
     * Global method for resetting internal system check.
     */
    public static function resetSystemChecks()
    {
        self::$isX64 = null;
        self::$mbString = null;
    }

    /**
     * The SHA-3-224 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output digest.
     * @throws \Exception Validation errors.
     */
    public static function digest224($inputData, $rawOutput = false)
    {
        return self::calculateDigest($inputData, 224, $rawOutput);
    }

    /**
     * The SHA-3-256 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output digest.
     * @throws \Exception Validation errors.
     */
    public static function digest256($inputData, $rawOutput = false)
    {
        return self::calculateDigest($inputData, 256, $rawOutput);
    }

    /**
     * The SHA-3-384 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output digest.
     * @throws \Exception Validation errors.
     */
    public static function digest384($inputData, $rawOutput = false)
    {
        return self::calculateDigest($inputData, 384, $rawOutput);
    }

    /**
     * The SHA-3-512 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output digest.
     * @throws \Exception Validation errors.
     */
    public static function digest512($inputData, $rawOutput = false)
    {
        return self::calculateDigest($inputData, 512, $rawOutput);
    }
}
