<?php

/**
 * Interface for the digital envelope manipulations.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\DataStructures\EnvelopeData as EnvelopeStructure;

/**
 * Interface DigitalEnvelopeInterface - Interface specification for digital envelope manipulations.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface DigitalEnvelopeInterface
{
    /**
     * Seals the envelope with the secured information inside.
     *
     * @param string $plainData The plain message information.
     *
     * @return EnvelopeStructure The sealed envelope object.
     * @throws \Exception Validation errors.
     */
    public function sealEnvelope($plainData);

    /**
     * Opens the envelope and extracts secured information from it.
     *
     * @param EnvelopeStructure $envelopeData The sealed envelope object.
     *
     * @return string The plain message information.
     * @throws \Exception Validation errors.
     */
    public function openEnvelope(EnvelopeStructure $envelopeData);
}
