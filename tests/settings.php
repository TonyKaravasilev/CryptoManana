<?php

/**
 * File defining local settings and debugging functions for unit tests execution.
 */

// Setup debug functions for Web and CLI
if (!function_exists('dump') && !function_exists('dd')) {
    /**
     * Dump the data and its information in a Web Browser or a CLI script.
     *
     * @param mixed $data The data for dumping.
     * @param string $colour Colour used for CLI outputting.
     */
    function dump($data = PHP_EOL, $colour = 'green')
    {
        // Is this a CLI call?
        if (PHP_SAPI === 'cli') {
            // Dump newline case
            if ($data === PHP_EOL) {
                echo PHP_EOL;

                return;
            }

            switch ($colour) {
                case 'black':
                    $colour = "1;30m";
                    break;
                case 'red':
                    $colour = "1;31m";
                    break;
                case 'green':
                    $colour = "1;32m";
                    break;
                case 'yellow':
                    $colour = "1;33m";
                    break;
                case 'blue':
                    $colour = "1;34m";
                    break;
                case 'purple':
                    $colour = "1;35m";
                    break;
                case 'cyan':
                    $colour = "1;36m";
                    break;
                case 'white':
                    $colour = "1;36m";
                    break;
                default:
                    $colour = "0m";
                    break;
            }

            if (!is_string($data)) {
                $data = var_export($data, true);
            }

            echo PHP_EOL . "\033[" . $colour . $data . "\033[0m" . PHP_EOL . PHP_EOL;
        } else {
            // Dump newline case
            if ($data === PHP_EOL) {
                echo "<br/>";

                return;
            }

            // More colorful with xDebug enabled
            var_dump($data);
        }
    }

    /**
     * Stop execution after dumping the data and its information in a Web Browser or a CLI script.
     *
     * @param mixed $data The data for dumping.
     * @param string $colour Colour used for CLI outputting.
     */
    function dd($data = PHP_EOL, $colour = 'red')
    {
        dump($data, $colour); // Dump information

        usleep(1); // process hogging

        exit(0); // Exit process with success
    }
}

// If this is a CLI request, than we are starting unit tests, code analyses, etc
if (PHP_SAPI === 'cli') {
    // Local settings that were not included at phpunit.xml.dist/phpunit.xml file
    date_default_timezone_set('UTC');
    ini_set('serialize_precision', '17');
    ini_set('precision', '14');
    clearstatcache();

    // Check if the mbstring extension is loaded
    if (extension_loaded('mbstring')) {
        /**
         * @api All test cases are written for UTF-8 (ASCII compatible).
         * @internal The developer can use the framework with another encoding.
         */
        mb_regex_encoding('UTF-8');
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
    } else {
        echo 'Please enable the "mbstring" extension to run CLI unit test and code analyses tools.' . PHP_EOL;

        exit(1);
    }

    // Optional local configuration file
    $localFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
    clearstatcache(true, $localFilePath);

    if (realpath($localFilePath) !== false && file_exists($localFilePath) && is_readable($localFilePath)) {
        include_once $localFilePath; // Is ignored via version control system
    }

    unset($localFilePath);
}
