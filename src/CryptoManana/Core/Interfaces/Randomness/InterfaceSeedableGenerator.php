<?php

/**
 * Interface allowing the seeding of a pseudo-random generator.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface InterfaceSeedableGenerator - Interface specification for seeding pseudo-random generators.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface InterfaceSeedableGenerator
{
    /**
     * Seed the pseudo-randomness generator.
     *
     * @param null|int $seed Seed value in integer format or null for auto-seeding.
     */
    public static function setSeed($seed = null);
}
