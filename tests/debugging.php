<?php

/**
 * File defining debugging functions for development and testing purposes.
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
                    $colour = "1;37m";
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
