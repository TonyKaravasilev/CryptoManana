<?php

/**
 * Interface for dependency containers allowing the seeding of pseudo-random generator services.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface SeedableContainerInterface - Interface specification for seeding pseudo-random generator services.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface SeedableContainerInterface
{
    /**
     * Pass a seed value to the pseudo-randomness generator service.
     *
     * @param null|int $seed Seed value in integer format or null for auto-seeding.
     */
    public function seedRandomGenerator($seed = null);
}
