<?php

namespace Turahe\Ledger\Models\Contracts;

interface LedgerModelInterface
{
    /**
     * Get the model's unique identifier
     */
    public function getId(): string;

    /**
     * Get the model's code
     */
    public function getCode(): string;

    /**
     * Get the model's status
     */
    public function getStatus(): string;

    /**
     * Check if the model is active
     */
    public function isActive(): bool;

    /**
     * Get the model's total amount
     */
    public function getTotalAmount(): float;

    /**
     * Get the model's currency
     */
    public function getCurrency(): string;

    /**
     * Get the model's metadata
     */
    public function getMetadata(): ?object;

    /**
     * Set the model's metadata
     */
    public function setMetadata(object $metadata): void;

    /**
     * Get the model's created date
     */
    public function getCreatedAt(): ?\Carbon\Carbon;

    /**
     * Get the model's updated date
     */
    public function getUpdatedAt(): ?\Carbon\Carbon;
}
