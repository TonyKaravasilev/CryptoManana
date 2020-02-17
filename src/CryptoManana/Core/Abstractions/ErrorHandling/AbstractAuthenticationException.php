<?php

/**
 * Abstraction for all authentication related exceptions.
 */

namespace CryptoManana\Core\Abstractions\ErrorHandling;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class AbstractAuthenticationException - Abstraction for all authentication related exceptions.
 *
 * @package CryptoManana\Core\Abstractions\ErrorHandling
 */
abstract class AbstractAuthenticationException extends FrameworkException
{
}
