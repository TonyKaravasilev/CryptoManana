<?php

/**
 * This file defines needed PHP 5.x/7.x backward compatible (polyfill) functions, constants, classes, etc.
 *
 * {@internal Define a global constant `CRYPTO_MANANA_COMPATIBILITY_OFF` to suppress this feature. }}
 */

// Check if override global constant is defined
$disableCompatibilityScript = (
    defined('CRYPTO_MANANA_COMPATIBILITY_OFF') && CRYPTO_MANANA_COMPATIBILITY_OFF == true
);

// Compatibility checks and simple mitigation for PHP < 7.0.0
if (PHP_VERSION_ID < 70000 && !$disableCompatibilityScript) {
    // Set constant for minimum integer
    if (!defined('PHP_INT_MIN')) {
        /**
         * The smallest integer supported in this build of PHP.
         * Usually int(-2147483648) in 32 bit systems and int(-9223372036854775808) in 64 bit systems.
         * Available since PHP 7.0.0. Usually, PHP_INT_MIN === ~PHP_INT_MAX.
         *
         * @return int The smallest supported integer.
         * @internal define ('PHP_INT_MIN', ~PHP_INT_MAX)
         *
         */
        define('PHP_INT_MIN', ~PHP_INT_MAX);
    }

    // Set a secure pseudo-random bytes generation function
    if (!function_exists('random_bytes')) {
        /**
         * Generates cryptographically secure pseudo-random bytes.
         *
         * @param int $length The length of the random string that should be returned in bytes.
         *
         * @return string Returns a string containing the requested number of cryptographically secure random bytes.
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
                    'random_bytes(): Length must be greater than 0.'
                );
            }

            // If the libsodium PHP 5.x extension is installed and available
            if (function_exists("\\Sodium\\randombytes_buf")) {
                /**
                 * Function does not allow more than 2147483647 bytes to be generated in one invocation.
                 *
                 * {@internal The 32-bit systems limitation constrain. }}
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
         *
         * @return int Returns a cryptographically secure random integer in the range min to max, inclusive.
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
                    'random_int(): Minimum value must be less than or equal to the maximum value.'
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
                    throw new \Exception(
                        'random_int(): The secure pseudo-randomness source is broken (too many rejections).'
                    );
                }
            } while (!is_int($value) || $value > $max || $value < $min);

            return (int)$value;
        }
    }

    // Used only if someone is attempting to run the project at PHP <= 5.6.x
    if (!function_exists('hash_equals')) {
        /**
         * Timing attack safe string comparison.
         *
         * @param string $known_string The string of known length to compare against
         * @param string $user_input The user-supplied string
         *
         * @return bool Returns TRUE when the two strings are equal, FALSE otherwise.
         */
        function hash_equals($known_string, $user_input)
        {
            if (!is_string($known_string)) {
                trigger_error(
                    'hash_equals: Expected the $known_string parameter to be a string, ' .
                    gettype($known_string) . ' given instead.',
                    E_USER_WARNING
                );

                return false;
            }

            if (!is_string($user_input)) {
                trigger_error(
                    'hash_equals: Expected the $user_input parameter to be a string, ' .
                    gettype($user_input) . ' given instead. ',
                    E_USER_WARNING
                );

                return false;
            }

            $hasMb = function_exists('mb_strlen');

            $knownLength = $hasMb ? mb_strlen($known_string, '8bit') : strlen($known_string);
            $userLength = $hasMb ? mb_strlen($user_input, '8bit') : strlen($user_input);

            if ($knownLength !== $userLength) {
                return false;
            }

            $areEqual = 0;

            for ($i = 0; $i < $knownLength; ++$i) {
                // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
                $areEqual |= ord($known_string[$i]) ^ ord($user_input[$i]);
            }

            return (0 === $areEqual);
        }
    }

    // Used only if someone is attempting to run the project at PHP <= 5.6.x (compilation bug for some builds)
    if (!function_exists('hash_pbkdf2')) {
        /**
         * Generate a PBKDF2 key derivation of a supplied password.
         *
         * @param string $algo Name of selected hashing algorithm. See hash_algos() for a list of supported algorithms.
         * @param string $password The password to use for the derivation.
         * @param string $salt The salt to use for the derivation. This value should be generated randomly.
         * @param int $iterations The number of internal iterations to perform for the derivation.
         * @param int $length The length of the output string.
         *                    If raw_output is TRUE this corresponds to the byte-length of the derived key,
         *                    if raw_output is FALSE this corresponds to twice the byte-length of the derived key
         *                    (as every byte of the key is returned as two hexits).
         *                    If 0 is passed, the entire output of the supplied algorithm is used.
         * @param bool $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
         *
         * @return string|false Returns a string containing the derived key as lowercase hexits unless raw_output is
         *                      set to TRUE in which case the raw binary representation of the derived key is returned.
         */
        function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
        {
            if (!in_array($algo, hash_algos(), true)) {
                trigger_error(
                    "hash_pbkdf2(): The internal algorithm is not found: {$algo}.",
                    E_WARNING
                );

                return false;
            }

            if ($iterations <= 0) {
                trigger_error(
                    'hash_pbkdf2(): The iteration count must be greater than 0.',
                    E_WARNING
                );

                return false;
            }

            if ($length < 0) {
                trigger_error(
                    'hash_pbkdf2(): The key length must be greater or equal 0.',
                    E_WARNING
                );

                return false;
            }

            $hasMb = function_exists('mb_strlen');

            $saltLength = $hasMb ? mb_strlen($salt, '8bit') : strlen($salt);

            if (strlen($saltLength) > PHP_INT_MAX - 4) {
                trigger_error(
                    'hash_pbkdf2(): The salt string is too long.',
                    E_WARNING
                );

                return false;
            }

            $hashLength = hash($algo, '', true);
            $hashLength = $hasMb ? mb_strlen($hashLength, '8bit') : strlen($hashLength);

            if (0 === $length) {
                $length = $hashLength;
            }

            $blockCount = ceil($length / $hashLength);
            $output = '';

            for ($i = 1; $i <= $blockCount; ++$i) {
                $last = $salt . pack('N', $i);

                $last = $xorSum = hash_hmac($algo, $last, $password, true);

                for ($j = 1; $j < $iterations; ++$j) {
                    $xorSum ^= ($last = hash_hmac($algo, $last, $password, true));
                }

                $output .= $xorSum;
            }

            if (!$raw_output) {
                $output = bin2hex($output);
            }

            $hasMb = function_exists('mb_substr');

            return $hasMb ? mb_substr($output, 0, $length, '8bit') : substr($output, 0, $length);
        }
    }
}

// Compatibility checks and simple mitigation for PHP < 7.2.0
if (PHP_VERSION_ID < 70200 && !$disableCompatibilityScript) {
    // Set the HMAC supported hashing list
    if (!function_exists('hash_hmac_algos')) {
        /**
         * Return a list of registered hashing algorithms suitable for hash_hmac.
         *
         * @return array Returns a numerically indexed array containing the list of supported
         *               hashing algorithms suitable for hash_hmac().
         */
        function hash_hmac_algos()
        {
            return hash_algos(); // The older versions used this directly
        }
    }

    // Set the HKDF hashing algorithm
    if (!function_exists('hash_hkdf')) {
        /**
         * Generate a HKDF key derivation of a supplied key input.
         *
         * @param string $algo Name of selected hashing algorithm. See hash_algos() for a list of supported algorithms.
         * @param string $ikm Input keying material (raw binary). Cannot be empty.
         * @param int $length Desired output length in bytes. Cannot be greater than 255 times the chosen hash function
         *                    size. If length is 0, the output length will default to the chosen hash function size.
         * @param string $info Application/context-specific info string.
         * @param string $salt Salt to use during derivation. While optional, adding random salt significantly
         *                     improves the strength of HKDF.
         *
         * @return string|false Returns a string containing a raw binary representation of the derived key
         *                      (also known as output keying material - OKM); or FALSE on failure.
         */
        function hash_hkdf($algo, $ikm, $length = 0, $info = '', $salt = '')
        {
            if (!is_string($algo)) {
                trigger_error(
                    'hash_hkdf(): Expects parameter $algo to be string, ' . gettype($algo) . " given",
                    E_USER_WARNING
                );

                return false;
            }

            if (!is_string($ikm)) {
                trigger_error(
                    'hash_hkdf(): Expects parameter $ikm to be string, ' . gettype($ikm) . " given",
                    E_USER_WARNING
                );

                return false;
            }

            if (!is_string($info)) {
                trigger_error(
                    'hash_hkdf(): Expects parameter $info to be string, ' . gettype($info) . " given",
                    E_USER_WARNING
                );

                return false;
            }

            if (!is_string($salt)) {
                trigger_error(
                    'hash_hkdf(): Expects parameter $salt to be string, ' . gettype($salt) . " given",
                    E_USER_WARNING
                );

                return false;
            }

            $sizes = array_intersect_key(
                array(
                    'md2' => 16,
                    'md4' => 16,
                    'md5' => 16,
                    'sha1' => 20,
                    'sha224' => 28,
                    'sha256' => 32,
                    'sha384' => 48,
                    'sha512/224' => 28,
                    'sha512/256' => 32,
                    'sha512' => 64,
                    'ripemd128' => 16,
                    'ripemd160' => 20,
                    'ripemd256' => 32,
                    'ripemd320' => 40,
                    'whirlpool' => 64,
                    'tiger128,3' => 16,
                    'tiger160,3' => 20,
                    'tiger192,3' => 24,
                    'tiger128,4' => 16,
                    'tiger160,4' => 20,
                    'tiger192,4' => 24,
                    'snefru' => 32,
                    'snefru256' => 32,
                    'gost' => 32,
                    'gost-crypto' => 32,
                    'haval128,3' => 16,
                    'haval160,3' => 20,
                    'haval192,3' => 24,
                    'haval224,3' => 28,
                    'haval256,3' => 32,
                    'haval128,4' => 16,
                    'haval160,4' => 20,
                    'haval192,4' => 24,
                    'haval224,4' => 28,
                    'haval256,4' => 32,
                    'haval128,5' => 16,
                    'haval160,5' => 20,
                    'haval192,5' => 24,
                    'haval224,5' => 28,
                    'haval256,5' => 32,
                ),
                array_flip(hash_hmac_algos())
            );

            if (PHP_VERSION_ID >= 70100) {
                $sizes = array_merge(
                    $sizes,
                    [
                        'sha3-224' => 28,
                        'sha3-256' => 32,
                        'sha3-384' => 48,
                        'sha3-512' => 64,
                    ]
                );
            }

            if (!array_key_exists($algo, $sizes)) {
                trigger_error(
                    "hash_hkdf(): The internal algorithm is not found {$algo}.",
                    E_USER_WARNING
                );

                return false;
            }

            if (empty($ikm)) {
                trigger_error(
                    "hash_hkdf(): Input keying material cannot be empty.",
                    E_USER_WARNING
                );

                return false;
            }

            $length = filter_var($length, FILTER_VALIDATE_INT);

            if ($length === false) {
                trigger_error(
                    'hash_hkdf(): Expects parameter $length to be integer.',
                    E_USER_WARNING
                );

                return false;
            } elseif ($length < 0) {
                trigger_error(
                    "hash_hkdf(): Length must be greater than or equal to 0.",
                    E_USER_WARNING
                );

                return false;
            } elseif ($length > (255 * $sizes[$algo])) {
                trigger_error(
                    sprintf(
                        "hash_hkdf(): Length must be less than or equal to %d: %d.",
                        255 * $sizes[$algo],
                        $length
                    ),
                    E_USER_WARNING
                );

                return false;
            } elseif ($length === 0) {
                $length = $sizes[$algo];
            }

            if (empty($salt)) {
                $salt = str_repeat("\x0", $sizes[$algo]);
            }

            $prk = hash_hmac($algo, $ikm, $salt, true);
            $okm = '';

            for ($keyBlock = '', $blockIndex = 1; !isset($okm[$length - 1]); $blockIndex++) {
                // Note: mb_chr() is available only in PHP >= 7.2, so using chr() in ASCII 8-bit codes
                $keyBlock = hash_hmac($algo, $keyBlock . $info . chr($blockIndex), $prk, true);

                $okm .= $keyBlock;
            }

            $hasMb = function_exists('mb_substr');

            return $hasMb ? mb_substr($okm, 0, $length, '8bit') : substr($okm, 0, $length);
        }
    }
}

// Clear global variable for polyfill compatibility status
unset($disableCompatibilityScript);
