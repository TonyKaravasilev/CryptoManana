<?php

/**
 * This file defines the main unit test case abstraction class.
 */

namespace CryptoManana\Tests\TestTypes;

use \PHPUnit\Framework\TestCase as FrameworkUnitTest;
use \CryptoManana\Tests\TestInterfaces\TestDebuggingInterface as Dumping;

/**
 * Class AbstractUnitTest - Main class for unit test case creation.
 *
 * @package CryptoManana\Tests\TestTypes
 */
abstract class AbstractUnitTest extends FrameworkUnitTest implements Dumping
{
    /**
     * Dump the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function dump($data)
    {
        dump($data);
    }

    /**
     * Stop execution after dumping the data and its information.
     *
     * @param mixed $data The data for dumping.
     */
    public function stop($data)
    {
        dd($data);
    }
}
