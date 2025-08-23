<?php

/**
 * Turahe Ledger Package Configuration
 *
 * Configuration file for the Turahe Ledger package.
 * Defines default model classes for shipping and insurance providers.
 *
 * @package Turahe\Ledger\Config
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */

return [
    /**
     * Default shipping provider model class
     * 
     * The model class to use for shipping providers.
     * This should implement the necessary shipping-related methods.
     * 
     * @var string
     */
    'shipping_provider' => \App\Models\Organization::class,

    /**
     * Default insurance provider model class
     * 
     * The model class to use for insurance providers.
     * This should implement the necessary insurance-related methods.
     * 
     * @var string
     */
    'insurance_provider' => \App\Models\Organization::class,
];
