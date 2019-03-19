<?php

/**
 * File defining local settings and debugging functions for unit tests execution.
 */

/**
 * Requires the debug functions implementation for Web and CLI.
 */
require 'debugging.php';

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
         * All test cases are written for UTF-8 (ASCII compatible).
         *
         * {@internal The developer can use the framework with another encoding. }}
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
