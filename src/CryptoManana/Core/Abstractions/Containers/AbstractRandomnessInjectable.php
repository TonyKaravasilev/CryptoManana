<?php

/**
 * Abstraction container for dependency injection of pseudo-random generator services.
 */

namespace CryptoManana\Core\Abstractions\Containers;

use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;
use CryptoManana\Core\Interfaces\Randomness\SeedableContainerInterface as SeedableService;
use CryptoManana\Core\Interfaces\Randomness\SeedableGeneratorInterface as SeedableGenerator;
use CryptoManana\Core\Interfaces\Containers\RandomnessInjectableInterface as SetterInjectable;
use CryptoManana\Core\Traits\Containers\RandomnessInjectableTrait as SetterInjectableImplementation;
use CryptoManana\Randomness\CryptoRandom as DefaultRandomnessSource;

/**
 * Class AbstractRandomnessInjectable - Abstraction container for dependency injection of data generator services.
 *
 * @package CryptoManana\Core\Abstractions\Containers
 *
 * @mixin SetterInjectableImplementation
 */
abstract class AbstractRandomnessInjectable implements SetterInjectable, SeedableService
{
    /**
     * Dependency injection via a setter method implementation.
     *
     * {@internal Reusable implementation of `RandomnessInjectableInterface`. }}
     */
    use SetterInjectableImplementation;

    /**
     * The pseudo-random generator service property storage.
     *
     * @var RandomnessSource|null The pseudo-random generator service.
     */
    protected $randomnessSource = null;

    /**
     * Container constructor.
     *
     * @param RandomnessSource|null $generator The pseudo-random generator service.
     *
     * @throws \Exception Initialization validation.
     * @internal The default service is always the most secure one available.
     */
    public function __construct(RandomnessSource $generator = null)
    {
        if ($generator === null) {
            $this->randomnessSource = new DefaultRandomnessSource();
        } else {
            $this->randomnessSource = $generator;
        }
    }

    /**
     * Container destructor.
     */
    public function __destruct()
    {
        unset($this->randomnessSource);
    }

    /**
     * Container cloning via deep copy.
     */
    public function __clone()
    {
        $this->randomnessSource = clone $this->randomnessSource;
    }

    /**
     * Pass a seed value to the pseudo-randomness generator service.
     *
     * @param null|int $seed Seed value in integer format or null for auto-seeding.
     *
     * @return $this The container object.
     * @throws \Exception Validation errors and service misuse.
     */
    public function seedRandomGenerator($seed = null)
    {
        if ($this->randomnessSource instanceof SeedableGenerator) {
            /**
             * {@internal Backward compatibility way of calling a static method (`::`) via dynamic operator (`->`). }}
             */
            $this->randomnessSource->setSeed($seed);
        }

        return $this;
    }
}
