<?php

namespace Turahe\Ledger\Models\Contracts;

/**
 * Ledger Model Interface
 *
 * Defines the contract that all ledger models must implement.
 * Ensures consistency across Invoice, Voucher, and other ledger entities.
 * 
 * This interface standardizes the basic methods that all ledger models should provide,
 * enabling polymorphic behavior and consistent API usage across the package.
 *
 * @package Turahe\Ledger\Models\Contracts
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
interface LedgerModelInterface
{
    /**
     * Get the model's unique identifier
     *
     * @return string The model's primary key value
     */
    public function getId(): string;

    /**
     * Get the model's code
     *
     * @return string The model's unique business code
     */
    public function getCode(): string;

    /**
     * Get the model's status
     *
     * @return string The current status of the model
     */
    public function getStatus(): string;

    /**
     * Check if the model is active
     *
     * @return bool True if the model is active, false otherwise
     */
    public function isActive(): bool;

    /**
     * Get the model's total amount
     *
     * @return float The total monetary amount associated with the model
     */
    public function getTotalAmount(): float;

    /**
     * Get the model's currency
     *
     * @return string The currency code (e.g., 'IDR', 'USD')
     */
    public function getCurrency(): string;

    /**
     * Get the model's metadata
     *
     * @return object|null The metadata object or null if not set
     */
    public function getMetadata(): ?object;

    /**
     * Set the model's metadata
     *
     * @param object $metadata The metadata object to set
     * @return void
     */
    public function setMetadata(object $metadata): void;

    /**
     * Get the model's created date
     *
     * @return \Carbon\Carbon|null The creation timestamp or null
     */
    public function getCreatedAt(): ?\Carbon\Carbon;

    /**
     * Get the model's updated date
     *
     * @return \Carbon\Carbon|null The last update timestamp or null
     */
    public function getUpdatedAt(): ?\Carbon\Carbon;
}
