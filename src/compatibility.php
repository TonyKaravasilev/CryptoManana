<?php

/**
 * This file defines needed PHP 5.x backward compatible functions, constants, classes, etc.
 *
 * @internal Define a global constant `CRYPTO_MANANA_COMPATIBILITY_OFF` to suppress this feature.
 */

// Compatibility checks and simple mitigation
if (PHP_VERSION_ID < 70000 && !defined('CRYPTO_MANANA_COMPATIBILITY_OFF')) {
    // Set constant for minimum integer
    if (!defined('PHP_INT_MIN')) {
        /**
         * The smallest integer supported in this build of PHP.
         * Usually int(-2147483648) in 32 bit systems and int(-9223372036854775808) in 64 bit systems.
         * Available since PHP 7.0.0. Usually, PHP_INT_MIN === ~PHP_INT_MAX.
         *
         * @api define ('PHP_INT_MIN', ~PHP_INT_MAX)
         *
         * @return int The smallest supported integer.
         */
        define('PHP_INT_MIN', ~PHP_INT_MAX);
    }

    // Set a secure pseudo-random bytes generation function
    if (!function_exists('random_bytes')) {
        /**
         * Generates cryptographically secure pseudo-random bytes.
         *
         * @param int $length The length of the random string that should be returned in bytes.
         * @return string Returns a string containing the requested number of cryptographically secure random bytes.
         *
         * @throws \Exception If the input parameter is less than 1 or no source is available.
         */
        function random_bytes($length)
        {
            if (!is_int($length)) {
                throw new \Exception(
                    'random_bytes(): $length must be an integer.'
                );
            }

            if ($length < 1) {
                throw new \Exception(
                    'Length must be greater than 0.'
                );
            }

            // If the libsodium PHP 5.x extension is installed and available
            if (function_exists("\\Sodium\\randombytes_buf")) {
                /**
                 * Function does not allow more than 2147483647 bytes to be generated in one invocation.
                 *
                 * @api The 32-bit systems limitation constrain.
                 */
                if ($length > 2147483647) {
                    $buffer = '';

                    for ($i = 0; $i < $length; $i += 1073741824) {
                        $n = ($length - $i) > 1073741824 ? 1073741824 : $length - $i;

                        $buffer .= \Sodium\randombytes_buf($n);
                    }
                } else {
                    $buffer = \Sodium\randombytes_buf($length);
                }

                // Validate the generated output string
                if (is_string($buffer)) {
                    if (function_exists('mb_strlen') && (int)mb_strlen($buffer, '8bit') === $length) {
                        return $buffer;
                    } elseif ((int)strlen($buffer) === $length) {
                        return $buffer;
                    } else // Fallback case [really rare]
                    {
                        if (function_exists('mb_strlen')) {
                            $tmpLength = (int)mb_strlen($buffer, '8bit');
                        } else {
                            $tmpLength = (int)strlen($buffer);
                        }

                        if ($length > $tmpLength) {
                            $tmpLength = $length - $tmpLength;
                            return $buffer . \Sodium\randombytes_buf($tmpLength);
                        } else { // < case
                            if (function_exists('mb_substr')) {
                                return mb_substr($buffer, 0, $length, '8bit');
                            } else {
                                return substr($buffer, 0, $length);
                            }
                        }
                    }
                } else {
                    // Function generated invalid output
                    trigger_error(
                        'The \Sodium\randombytes_buf() did not function correctly' .
                        ' and generated, a return value of type "' . gettype($buffer) . '", ' .
                        'switching to the openssl_random_pseudo_bytes() functions instead.',
                        E_USER_NOTICE
                    );
                }
            }

            // Will always exists because of the composer.json require constrain
            if (function_exists("openssl_random_pseudo_bytes")) {
                return openssl_random_pseudo_bytes($length);
            }

            // Still, if someone needs to autoload this manually, the openssl extensions might not be available
            throw new \Exception(
                'Could not gather from a sufficient random data source.'
            );
        }
    }

    // Set a secure pseudo-random integer generation function
    if (!function_exists('random_int')) {
        /**
         * Generates cryptographically secure pseudo-random integers.
         *
         * @param int $min The lowest value to be returned, which must be PHP_INT_MIN or higher.
         * @param int $max The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
         * @return int Returns a cryptographically secure random integer in the range min to max, inclusive.
         *
         * @throws \Exception If the input parameters are invalid, max is less than min or the random source is broken.
         */
        function random_int($min, $max)
        {
            if (!is_int($min)) {
                throw new \Exception(
                    'random_int(): $min must be an integer.'
                );
            }

            if (!is_int($max)) {
                throw new \Exception(
                    'random_int(): $max must be an integer.'
                );
            }

            if ($min > $max) {
                throw new \Exception(
                    'Minimum value must be less than or equal to the maximum value.'
                );
            }

            if ($max === $min) {
                return (int)$min;
            }

            $retries = 0;
            $bits = 0;
            $bytes = 0;
            $mask = 0;
            $shift = 0;

            $generationRange = $max - $min;

            if (!is_int($generationRange)) {
                $bytes = PHP_INT_SIZE;
                $mask = ~0;
            } else {
                while ($generationRange > 0) {
                    if ($bits % 8 === 0) {
                        $bytes++;
                    }

                    $bits++;
                    $generationRange >>= 1;
                    $mask = $mask << 1 | 1;
                }

                $shift = $min;
            }

            $value = 0;

            do {
                $randomByteString = random_bytes($bytes);

                $value &= 0;

                for ($i = 0; $i < $bytes; ++$i) {
                    // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
                    $value |= ord($randomByteString[$i]) << ($i * 8);
                }

                $value &= $mask;
                $value += $shift;

                $retries++;

                if ($retries > 128) {
                    throw new Exception(
                        'random_int(): The secure pseudo-randomness source is broken (too many rejections).'
                    );
                }
            } while (!is_int($value) || $value > $max || $value < $min);

            return (int)$value;
        }
    }

    // Optional, used only if someone is manually attempting to run the project at PHP <= 5.6.x
    if (!function_exists('hash_equals')) {
        /**
         * Timing attack safe string comparison.
         *
         * @param string $known_string The string of known length to compare against
         * @param string $user_input The user-supplied string
         * @return bool Returns TRUE when the two strings are equal, FALSE otherwise.
         */
        function hash_equals($known_string, $user_input)
        {
            if (!is_string($known_string)) {
                trigger_error(
                    'Expected the $known_string parameter to be a string, ' .
                    gettype($known_string) . ' given instead.',
                    E_USER_WARNING
                );

                return false;
            }

            if (!is_string($user_input)) {
                trigger_error(
                    'Expected the $user_input parameter to be a string, ' . gettype($user_input) . ' given instead. ',
                    E_USER_WARNING
                );

                return false;
            }

            $knownLength = function_exists('mb_strlen') ? mb_strlen($known_string, '8bit') : strlen($known_string);
            $userLength = function_exists('mb_strlen') ? mb_strlen($user_input, '8bit') : strlen($user_input);

            if ($knownLength !== $userLength) {
                return false;
            }

            $areEqual = 0;

            for ($i = 0; $i < $knownLength; ++$i) {
                // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
                $areEqual |= ord($known_string[$i]) ^ ord($user_input[$i]);
            }

            return 0 === $areEqual;
        }
    }
}
