<?php

/**
 * Autoloading snippet for projects that do not use Composer and for development purposes also.
 */

/**
 * Requires the PHP 5.x/7.x compatibility polyfill snippet before setting the autoloader.
 */
require 'compatibility.php';

/**
 * Simple project autoloader implementation for the PSR-4 PHP standard.
 *
 * @param string $className The fully-qualified class name.
 *
 * @return void
 * @link https://github.com/php-fig/fig-standards/tree/master/accepted
 *
 * @internal Code inspired by PHP Framework Interop Group PHP Standard Recommendation.
 *
 * @link https://www.php-fig.org/psr/psr-4/examples/#closure-example
 */
spl_autoload_register(
    function ($className) {
        // The project-specific namespace prefix
        $prefix = 'CryptoManana\\';

        // Base directory for the namespace prefix recursive lookup
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'CryptoManana' . DIRECTORY_SEPARATOR;

        // Does the class use the namespace prefix?
        $len = strlen($prefix);

        // If no then move to the next registered autoloader
        if (strncmp($prefix, $className, $len) !== 0) {
            return;
        }

        // Get the relative class name
        $relativeClass = substr($className, $len);

        // Build the autoloading path to file
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        // If the file exists then require it
        if (file_exists($file)) {
            require $file; // Fastest
        }
    }
);
