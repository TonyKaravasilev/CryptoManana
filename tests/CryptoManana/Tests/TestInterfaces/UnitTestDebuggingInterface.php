<?php

/**
 * This file defines methods for dumping while debugging tests.
 */

namespace CryptoManana\Tests\TestInterfaces;

/**
 * Interface UnitTestDebuggingInterface - Interface defining debugging methods.
 *
 * @package CryptoManana\Tests\TestInterfaces
 */
interface UnitTestDebuggingInterface
{
    /**
     * Dump the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function dump($data);

    /**
     * Stop execution after dumping the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function stop($data);

    /**
     * Throw an exception with error message.
     *
     * @param mixed $message The error message.
     *
     * @throws \RuntimeException Custom error happened.
     */
    public function error($message = 'Oops');

    /**
     * Sleep execution for some time (1 second = 1000 milliseconds).
     *
     * @param int $milliSeconds Sleep time in milliseconds.
     */
    public function sleep($milliSeconds = 1000);
}
