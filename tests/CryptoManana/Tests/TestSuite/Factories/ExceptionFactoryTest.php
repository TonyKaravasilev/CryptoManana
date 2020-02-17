<?php

/**
 * Testing the internal ExceptionFactory component used for easier framework exception throwing and instancing.
 */

namespace CryptoManana\Tests\TestSuite\Factories;

use \CryptoManana\Tests\TestTypes\AbstractUnitTest;
use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractFactory;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractCryptologyException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAlgorithmException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractIdentificationException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAuthenticationException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAuthorizationException;
use \CryptoManana\Exceptions\BadPracticeException;
use \CryptoManana\Exceptions\CryptographyException;
use \CryptoManana\Exceptions\IncompatibleException;
use \CryptoManana\Exceptions\UnsupportedException;
use \CryptoManana\Factories\ExceptionFactory;
use \CryptoManana\Exceptions\AccessDeniedException;
use \CryptoManana\Exceptions\BreachAttemptException;
use \CryptoManana\Exceptions\MaliciousPayloadException;
use \CryptoManana\Exceptions\BotDetectedException;
use \CryptoManana\Exceptions\IdentificationFailureException;
use \CryptoManana\Exceptions\AuthenticationFailureException;
use \CryptoManana\Exceptions\AuthorizationFailureException;
use \CryptoManana\Exceptions\SessionExpiredException;
use \CryptoManana\Exceptions\TokenExpiredException;
use \CryptoManana\Exceptions\WrongConfigurationException;
use \CryptoManana\Exceptions\InsecureUsageException;

/**
 * Class ExceptionFactoryTest - Tests the framework exception factory class.
 *
 * @package CryptoManana\Tests\TestSuite\Factories
 */
final class ExceptionFactoryTest extends AbstractUnitTest
{
    /**
     * Creates new instances for testing.
     *
     * @return ExceptionFactory Testing instance.
     */
    private function getExceptionFactoryForTesting()
    {
        return new ExceptionFactory();
    }

    /**
     * Testing the cloning of an instance.
     */
    public function testCloningCapabilities()
    {
        $factory = $this->getExceptionFactoryForTesting();

        $tmp = clone $factory;

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::BAD_PRACTICE));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the serialization of an instance.
     */
    public function testSerializationCapabilities()
    {
        $factory = $this->getExceptionFactoryForTesting();

        $tmp = serialize($factory);
        $tmp = unserialize($tmp);

        $this->assertEquals($factory, $tmp);
        $this->assertNotEmpty($tmp->create($tmp::BAD_PRACTICE));

        unset($tmp);
        $this->assertNotNull($factory);
    }

    /**
     * Testing the object dumping for debugging.
     *
     * @throws \ReflectionException If the tested class or method does not exist.
     */
    public function testDebugCapabilities()
    {
        $factory = $this->getExceptionFactoryForTesting();

        $this->assertNotEmpty(var_export($factory, true));

        $reflection = new \ReflectionClass($factory);
        $method = $reflection->getMethod('__debugInfo');

        $result = $method->invoke($factory);
        $this->assertNotEmpty($result);
    }

    /**
     * Testing the dynamic instancing calls.
     */
    public function testDynamicInstancingCalls()
    {
        $factory = $this->getExceptionFactoryForTesting();

        $this->assertTrue($factory instanceof ExceptionFactory);
        $this->assertTrue($factory instanceof AbstractFactory);

        $tmp = $factory->create(ExceptionFactory::BAD_PRACTICE);
        $this->assertTrue($tmp instanceof BadPracticeException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::CRYPTOGRAPHY_PROBLEM);
        $this->assertTrue($tmp instanceof CryptographyException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::BACKWARD_INCOMPATIBLE);
        $this->assertTrue($tmp instanceof IncompatibleException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::UNSUPPORTED_ALGORITHM);
        $this->assertTrue($tmp instanceof UnsupportedException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::ACCESS_DENIED);
        $this->assertTrue($tmp instanceof AccessDeniedException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::BREACH_ATTEMPT);
        $this->assertTrue($tmp instanceof BreachAttemptException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::MALICIOUS_PAYLOAD);
        $this->assertTrue($tmp instanceof MaliciousPayloadException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::BOT_DETECTED);
        $this->assertTrue($tmp instanceof BotDetectedException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::IDENTIFICATION_FAILURE);
        $this->assertTrue($tmp instanceof IdentificationFailureException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::AUTHENTICATION_FAILURE);
        $this->assertTrue($tmp instanceof AuthenticationFailureException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::AUTHORIZATION_FAILURE);
        $this->assertTrue($tmp instanceof AuthorizationFailureException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::SESSION_EXPIRED);
        $this->assertTrue($tmp instanceof SessionExpiredException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::TOKEN_EXPIRED);
        $this->assertTrue($tmp instanceof TokenExpiredException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::WRONG_CONFIGURATION);
        $this->assertTrue($tmp instanceof WrongConfigurationException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = $factory->create(ExceptionFactory::INSECURE_USAGE);
        $this->assertTrue($tmp instanceof InsecureUsageException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $this->assertNull($factory->create(\DomainException::class));
    }

    /**
     * Testing the static instancing calls.
     */
    public function testStaticInstancingCalls()
    {
        $tmp = ExceptionFactory::createInstance(ExceptionFactory::BAD_PRACTICE);
        $this->assertTrue($tmp instanceof BadPracticeException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::CRYPTOGRAPHY_PROBLEM);
        $this->assertTrue($tmp instanceof CryptographyException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::BACKWARD_INCOMPATIBLE);
        $this->assertTrue($tmp instanceof IncompatibleException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::UNSUPPORTED_ALGORITHM);
        $this->assertTrue($tmp instanceof UnsupportedException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::ACCESS_DENIED);
        $this->assertTrue($tmp instanceof AccessDeniedException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::BREACH_ATTEMPT);
        $this->assertTrue($tmp instanceof BreachAttemptException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::MALICIOUS_PAYLOAD);
        $this->assertTrue($tmp instanceof MaliciousPayloadException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::BOT_DETECTED);
        $this->assertTrue($tmp instanceof BotDetectedException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::IDENTIFICATION_FAILURE);
        $this->assertTrue($tmp instanceof IdentificationFailureException);
        $this->assertTrue($tmp instanceof AbstractIdentificationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::AUTHENTICATION_FAILURE);
        $this->assertTrue($tmp instanceof AuthenticationFailureException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::AUTHORIZATION_FAILURE);
        $this->assertTrue($tmp instanceof AuthorizationFailureException);
        $this->assertTrue($tmp instanceof AbstractAuthorizationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::SESSION_EXPIRED);
        $this->assertTrue($tmp instanceof SessionExpiredException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::TOKEN_EXPIRED);
        $this->assertTrue($tmp instanceof TokenExpiredException);
        $this->assertTrue($tmp instanceof AbstractAuthenticationException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::WRONG_CONFIGURATION);
        $this->assertTrue($tmp instanceof WrongConfigurationException);
        $this->assertTrue($tmp instanceof AbstractAlgorithmException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $tmp = ExceptionFactory::createInstance(ExceptionFactory::INSECURE_USAGE);
        $this->assertTrue($tmp instanceof InsecureUsageException);
        $this->assertTrue($tmp instanceof AbstractCryptologyException);
        $this->assertTrue($tmp instanceof AbstractException);
        $this->assertTrue($tmp instanceof \Exception);

        $this->assertNull(ExceptionFactory::createInstance(\DomainException::class));
    }
}
