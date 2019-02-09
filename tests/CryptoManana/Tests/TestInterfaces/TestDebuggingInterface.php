<?php

/**
 * This file defines methods for dumping while debugging tests.
 */

namespace CryptoManana\Tests\TestInterfaces;

/**
 * Interface TestDebuggingInterface - Interface defining debugging methods.
 * @package CryptoManana\Tests\TestInterfaces
 */
interface TestDebuggingInterface
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
}
