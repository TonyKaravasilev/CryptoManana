<?php

/**
 * Proof of unit test conception.
 */

namespace CryptoManana\Tests\TestSuite;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;

/**
 * Class FirstTest - First unit test.
 * @package CryptoManana\Tests\TestSuite
 */
final class FirstTest extends AbstractUnitTest
{
    /**
     * Testing first case ever.
     */
    public function testFirstCase()
    {
        $this->assertTrue(true);
    }
}
