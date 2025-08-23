<?php

namespace Turahe\Ledger\Services;

use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Invoice\Payment;
use Turahe\Ledger\Models\Voucher;

/**
 * Ledger Service
 *
 * Core business logic service for ledger operations including invoice management,
 * voucher handling, payment processing, and financial calculations.
 *
 * Features:
 * - Invoice creation and management
 * - Voucher creation and validation
 * - Payment processing and validation
 * - Financial calculations and updates
 * - Status management and business rules
 * - Query methods for common operations
 *
 * This service encapsulates all business logic to ensure consistency
 * and maintainability across the ledger system.
 *
 * @author  Nur Wachid <wachid@outlook.com>
 *
 * @since   1.0.0
 */
class LedgerService
{
    /**
     * Create a new invoice
     *
     * Creates an invoice with the provided data and sets a default status
     * if none is specified.
     *
     * @param  array  $data  Invoice data including model_id, model_type, code, amounts, etc.
     * @return Invoice The created invoice instance
     *
     * @example
     * ```php
     * $invoice = $ledgerService->createInvoice([
     *     'model_id' => $user->id,
     *     'model_type' => User::class,
     *     'code' => 'INV-001',
     *     'total_invoice' => 1000.00,
     *     'currency' => 'IDR'
     * ]);
     * ```
     */
    public function createInvoice(array $data): Invoice
    {
        $invoice = Invoice::create($data);

        // Set default status if not provided
        if (! isset($data['status'])) {
            $invoice->update(['status' => 'draft']);
        }

        return $invoice;
    }

    /**
     * Create a new voucher
     */
    public function createVoucher(array $data): Voucher
    {
        $voucher = Voucher::create($data);

        // Set default status if not provided
        if (! isset($data['status'])) {
            $voucher->update(['status' => 'active']);
        }

        return $voucher;
    }

    /**
     * Process payment for an invoice
     */
    public function processPayment(Invoice $invoice, Voucher $voucher, array $paymentData): Payment
    {
        // Validate payment amount
        if ($paymentData['amount'] > $invoice->getRemainingAmount()) {
            throw new \InvalidArgumentException('Payment amount exceeds remaining invoice amount');
        }

        // Create payment record
        $payment = $invoice->payments()->attach($voucher->id, $paymentData);

        // Update invoice totals
        $this->updateInvoiceTotals($invoice);

        return $payment;
    }

    /**
     * Update the total payment and unpaid amount for the given invoice.
     *
     * This method recalculates the total amount paid towards the invoice by summing all related payment records,
     * and updates the invoice's 'total_payment' and 'total_unpaid' fields accordingly.
     * If the total payments exceed the invoice amount, the unpaid amount is set to zero.
     *
     * @param  Invoice  $invoice  The invoice instance to update
     *
     * @example
     * ```php
     * $ledgerService->updateInvoiceTotals($invoice);
     * ```
     */
    public function updateInvoiceTotals(Invoice $invoice): void
    {
        $totalPayment = $invoice->payments()->sum('amount');
        $totalUnpaid = max(0, $invoice->total_invoice - $totalPayment);

        $invoice->update([
            'total_payment' => $totalPayment,
            'total_unpaid' => $totalUnpaid,
        ]);
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices(): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::where('due_date', '<', now())
            ->where('total_unpaid', '>', 0)
            ->where('status', '!=', 'cancelled')
            ->get();
    }

    /**
     * Get invoices by status
     */
    public function getInvoicesByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::where('status', $status)->get();
    }

    /**
     * Get vouchers by status
     */
    public function getVouchersByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Voucher::where('status', $status)->get();
    }

    /**
     * Get ledger summary statistics
     */
    public function getLedgerSummary(): array
    {
        $totalInvoices = Invoice::count();
        $totalVouchers = Voucher::count();
        $totalOverdue = $this->getOverdueInvoices()->count();
        $totalActiveVouchers = Voucher::where('status', 'active')->count();

        return [
            'total_invoices' => $totalInvoices,
            'total_vouchers' => $totalVouchers,
            'overdue_invoices' => $totalOverdue,
            'active_vouchers' => $totalActiveVouchers,
        ];
    }

    /**
     * Cancel an invoice
     */
    public function cancelInvoice(Invoice $invoice, ?string $reason = null): void
    {
        $invoice->update(['status' => 'cancelled']);

        if ($reason) {
            $invoice->setMetadataValue('cancellation_reason', $reason);
            $invoice->save();
        }
    }

    /**
     * Cancel a voucher
     */
    public function cancelVoucher(Voucher $voucher, ?string $reason = null): void
    {
        $voucher->update(['status' => 'cancelled']);

        if ($reason) {
            $voucher->setMetadataValue('cancellation_reason', $reason);
            $voucher->save();
        }
    }
}
