<?php

/**
 * Testing the IncompatibleException type for marking of incompatible types and specifications.
 */

namespace CryptoManana\Tests\TestSuite\Exceptions;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAlgorithmException;
use \CryptoManana\Exceptions\IncompatibleException;

/**
 * Class IncompatibleExceptionTest - Tests the 'incompatible' framework exception class.
 *
 * @package CryptoManana\Tests\TestSuite\Exceptions
 */
final class IncompatibleExceptionTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return IncompatibleException Testing instance.
     */
    private function getExceptionInstanceForTesting()
    {
        return new IncompatibleException();
    }

    /**
     * Testing if the exception has a correct default internal error code.
     */
    public function testTheExceptionInternalErrorCodeIsCorrect()
    {
        $tmp = $this->getExceptionInstanceForTesting();

        $this->assertTrue(method_exists($tmp, 'getCode'));
        $this->assertTrue($tmp->getCode() === IncompatibleException::INTERNAL_CODE);
        $this->assertTrue($tmp->getFrameworkErrorCode() === IncompatibleException::INTERNAL_CODE);
    }

    /**
     * Testing if the exception can be customizable and throwable.
     *
     * @throws \Exception Wrong usage errors.
     */
    public function testTheExceptionIsCustomizableAndThrowable()
    {
        $tmp = $this->getExceptionInstanceForTesting();

        $this->assertTrue($tmp instanceof IncompatibleException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        /**
         * @var IncompatibleException $same Self-reference from fluent interface implementation.
         */
        $same = $tmp->setMessage('Test hard!')->setCode(70)->setFile(__FILE__)->setLine(__LINE__);
        $same = $same->setMessage('Test hard')->setCode(69)->setFile(__FILE__)->setLine(__LINE__);

        $this->assertEquals($tmp, $same);

        $this->assertEquals($tmp->getCode(), $same->getCode());
        $this->assertEquals($tmp->__toString(), $same->__toString());

        $this->assertEquals($tmp->getMessage(), $same->getMessage());
        $this->assertEquals($tmp->getFile(), $same->getFile());
        $this->assertEquals($tmp->getLine(), $same->getLine());

        // Backward compatible for different versions of PHPUnit
        if (method_exists($this, 'expectException')) {
            $this->expectException(IncompatibleException::class);

            throw $same;
        } else {
            $hasThrown = null;

            try {
                throw $same;
            } catch (IncompatibleException $exception) {
                $hasThrown = !empty($exception->getMessage());
            } catch (\Exception $exception) {
                $hasThrown = $exception->getMessage();
            }

            $this->assertTrue($hasThrown);

            return;
        }
    }
}
