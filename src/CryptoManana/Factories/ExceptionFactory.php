<?php

/**
 * Factory object for framework exception instancing.
 */

namespace CryptoManana\Factories;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory as FactoryPattern;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;
use \CryptoManana\Exceptions\BadPracticeException as BadPractice;
use \CryptoManana\Exceptions\CryptographyException as CryptographyProblem;
use \CryptoManana\Exceptions\IncompatibleException as BackwardIncompatible;
use \CryptoManana\Exceptions\UnsupportedException as UnsupportedAlgorithm;

/**
 * Class ExceptionFactory - Factory object for framework exception instancing.
 *
 * @package CryptoManana\Factories
 */
class ExceptionFactory extends FactoryPattern
{
    /**
     * The `cryptography` exception type.
     */
    const CRYPTOGRAPHY_PROBLEM = CryptographyProblem::class;

    /**
     * The `bad practice` exception type.
     */
    const BAD_PRACTICE = BadPractice::class;

    /**
     * The `unsupported algorithm` exception type.
     */
    const UNSUPPORTED_ALGORITHM = UnsupportedAlgorithm::class;

    /**
     * The `backward incompatible` exception type.
     */
    const BACKWARD_INCOMPATIBLE = BackwardIncompatible::class;

    /**
     * Create a framework exception.
     *
     * @param string|null $type The exception class name as type for creation.
     *
     * @return FrameworkException|object|null An exception object or null.
     */
    public function create($type)
    {
        return self::createInstance($type);
    }

    /**
     * Create a framework exception.
     *
     * @param string|null $type The exception class name as type for creation.
     *
     * @return FrameworkException|object|null An exception object or null.
     */
    public static function createInstance($type)
    {
        /**
         * Check if class exists and has a correct base class
         *
         * @var \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException|null $exception Object instance.
         */
        if (class_exists($type) && is_subclass_of($type, FrameworkException::class)) {
            $exception = new $type();
        } else {
            $exception = null; // Invalid exception type given
        }

        return $exception;
    }

    /**
     * Get debug information for the class instance.
     *
     * @return array Debug information.
     */
    public function __debugInfo()
    {
        return [
            self::class . '::BAD_PRACTICE' => self::BAD_PRACTICE,
            self::class . '::CRYPTOGRAPHY_PROBLEM' => self::CRYPTOGRAPHY_PROBLEM,
            self::class . '::BACKWARD_INCOMPATIBLE' => self::BACKWARD_INCOMPATIBLE,
            self::class . '::UNSUPPORTED_ALGORITHM' => self::UNSUPPORTED_ALGORITHM,
        ];
    }
}
