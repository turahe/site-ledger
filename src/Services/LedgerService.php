<?php

namespace Turahe\Ledger\Services;

use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Invoice\Payment;
use Turahe\Ledger\Models\Voucher;

class LedgerService
{
    /**
     * Create a new invoice
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
     * Update invoice totals based on payments
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
