<?php

namespace Turahe\Ledger\Enums;

/**
 * Payment Methods Enum
 *
 * Groups payment methods by category for better organization and maintenance
 */
enum PaymentMethods: string
{
    // Traditional Payment Methods
    case CASH = 'CASH';
    case CHEQUE = 'CHEQUE';
    case GIRO = 'GIRO';
    case BILYET_GIRO = 'BILYET_GIRO';

    // QRIS Payment Methods
    case QRIS_BCA = 'QRIS_BCA';
    case QRIS_BRI = 'QRIS_BRI';
    case QRIS_GPN = 'QRIS_GPN';
    case QRIS_SHOOPEPAY = 'QRIS_SHOOPEPAY';
    case QRIS_GOPAY = 'QRIS_GOPAY';
    case QRIS_OVO = 'QRIS_OVO';
    case QRIS_DANA = 'QRIS_DANA';
    case QRIS_JENIUS = 'QRIS_JENIUS';
    case QRIS_LINKAJA = 'QRIS_LINKAJA';

    // Pay Later Services
    case PAY_LATER_AKULAKU = 'PAY_LATER_AKULAKU';
    case PAY_LATER_KREDIVO = 'PAY_LATER_KREDIVO';

    // Virtual Account Methods
    case VIRTUAL_ACCOUNT_BCA = 'VIRTUAL_ACCOUNT_BCA';
    case VIRTUAL_ACCOUNT_MANDIRI = 'VIRTUAL_ACCOUNT_MANDIRI';
    case VIRTUAL_ACCOUNT_PERMATA = 'VIRTUAL_ACCOUNT_PERMATA';
    case VIRTUAL_ACCOUNT_BPD_BALI = 'VIRTUAL_ACCOUNT_BPD_BALI';
    case VIRTUAL_ACCOUNT_ARTHAGRAHA = 'VIRTUAL_ACCOUNT_ARTHAGRAHA';
    case VIRTUAL_ACCOUNT_BNI = 'VIRTUAL_ACCOUNT_BNI';
    case VIRTUAL_ACCOUNT_BRI = 'VIRTUAL_ACCOUNT_BRI';
    case VIRTUAL_ACCOUNT_MUAMALAT = 'VIRTUAL_ACCOUNT_MUAMALAT';
    case VIRTUAL_ACCOUNT_BSI = 'VIRTUAL_ACCOUNT_BSI';

    // Bank Transfer Methods
    case BANK_TRANSFER_BCA = 'BANK_TRANSFER_BCA';
    case BANK_TRANSFER_MANDIRI = 'BANK_TRANSFER_MANDIRI';
    case BANK_TRANSFER_ANZ = 'BANK_TRANSFER_ANZ';
    case BANK_TRANSFER_STANDARDCARTERED = 'BANK_TRANSFER_STANDARDCARTERED';

    // Card Payment Methods
    case CREDIT_CARD_MASTERCARD = 'CREDIT_CARD_MASTERCARD';
    case CREDIT_CARD_VISA = 'CREDIT_CARD_VISA';
    case CREDIT_CARD_JCB = 'CREDIT_CARD_JCB';
    case DEBIT_CARD = 'DEBIT_CARD';

    // Direct Debit Methods
    case DIRECT_DEBIT_BCA = 'DIRECT_DEBIT_BCA';
    case DIRECT_DEBIT_BRI = 'DIRECT_DEBIT_BRI';
    case DIRECT_DEBIT_BNI = 'DIRECT_DEBIT_BNI';
    case DIRECT_DEBIT_CIMB = 'DIRECT_DEBIT_CIMB';
    case DIRECT_DEBIT_BTN = 'DIRECT_DEBIT_BTN';
    case DIRECT_DEBIT_GPN = 'DIRECT_DEBIT_GPN';

    // E-Wallet Methods
    case E_WALLET_LINKAJA = 'E_WALLET_LINKAJA';
    case E_WALLET_GOPAY = 'E_WALLET_GOPAY';
    case E_WALLET_OVO = 'E_WALLET_OVO';
    case E_WALLET_DANA = 'E_WALLET_DANA';
    case E_WALLET_WECHATPAY = 'E_WALLET_WECHATPAY';
    case E_WALLET_SHOOPEPAY = 'E_WALLET_SHOOPEPAY';
    case E_WALLET_ALIPAY = 'E_WALLET_ALIPAY';
    case E_WALLET_DOKU = 'E_WALLET_DOKU';
    case E_WALLET_INSTAPAY = 'E_WALLET_INSTAPAY';
    case E_WALLET_PAYNOW = 'E_WALLET_PAYNOW';
    case E_WALLET_DUITNOW = 'E_WALLET_DUITNOW';
    case E_WALLET_PROMPTPAY = 'E_WALLET_PROMPTPAY';
    case E_WALLET_FPS = 'E_WALLET_FPS';
    case E_WALLET_VIETTEL_MONEY = 'E_WALLET_VIETTEL_MONEY';
    case E_WALLET_TOSS = 'E_WALLET_TOSS';

    // International Payment Methods
    case PAYPAL = 'PAYPAL';
    case PAYONER = 'PAYONER';

    // Convenience Store Methods
    case CONVINIENT_STORE_INDOMARET = 'CONVINIENT_STORE_INDOMARET';
    case CONVINIENT_STORE_ALFAMART = 'CONVINIENT_STORE_ALFAMART';
    case CONVINIENT_STORE_DANDAN = 'CONVINIENT_STORE_DANDAN';
    case CONVINIENT_STORE_LAWSON = 'CONVINIENT_STORE_LAWSON';

    /**
     * Get payment methods by category
     */
    public static function getByCategory(string $category): array
    {
        return match ($category) {
            'qris' => [
                self::QRIS_BCA, self::QRIS_BRI, self::QRIS_GPN, self::QRIS_SHOOPEPAY,
                self::QRIS_GOPAY, self::QRIS_OVO, self::QRIS_DANA, self::QRIS_JENIUS,
                self::QRIS_LINKAJA,
            ],
            'virtual_account' => [
                self::VIRTUAL_ACCOUNT_BCA, self::VIRTUAL_ACCOUNT_MANDIRI, self::VIRTUAL_ACCOUNT_PERMATA,
                self::VIRTUAL_ACCOUNT_BPD_BALI, self::VIRTUAL_ACCOUNT_ARTHAGRAHA, self::VIRTUAL_ACCOUNT_BNI,
                self::VIRTUAL_ACCOUNT_BRI, self::VIRTUAL_ACCOUNT_MUAMALAT, self::VIRTUAL_ACCOUNT_BSI,
            ],
            'e_wallet' => [
                self::E_WALLET_LINKAJA, self::E_WALLET_GOPAY, self::E_WALLET_OVO, self::E_WALLET_DANA,
                self::E_WALLET_WECHATPAY, self::E_WALLET_SHOOPEPAY, self::E_WALLET_ALIPAY,
                self::E_WALLET_DOKU, self::E_WALLET_INSTAPAY, self::E_WALLET_PAYNOW,
                self::E_WALLET_DUITNOW, self::E_WALLET_PROMPTPAY, self::E_WALLET_FPS,
                self::E_WALLET_VIETTEL_MONEY, self::E_WALLET_TOSS,
            ],
            'bank_transfer' => [
                self::BANK_TRANSFER_BCA, self::BANK_TRANSFER_MANDIRI, self::BANK_TRANSFER_ANZ,
                self::BANK_TRANSFER_STANDARDCARTERED,
            ],
            'credit_card' => [
                self::CREDIT_CARD_MASTERCARD, self::CREDIT_CARD_VISA, self::CREDIT_CARD_JCB,
            ],
            'convenience_store' => [
                self::CONVINIENT_STORE_INDOMARET, self::CONVINIENT_STORE_ALFAMART,
                self::CONVINIENT_STORE_DANDAN, self::CONVINIENT_STORE_LAWSON,
            ],
            default => []
        };
    }

    /**
     * Check if payment method is digital
     */
    public function isDigital(): bool
    {
        return ! in_array($this, [self::CASH, self::CHEQUE, self::GIRO, self::BILYET_GIRO]);
    }

    /**
     * Check if payment method is instant
     */
    public function isInstant(): bool
    {
        return in_array($this, [
            self::CASH, self::E_WALLET_LINKAJA, self::E_WALLET_GOPAY, self::E_WALLET_OVO,
            self::E_WALLET_DANA, self::DEBIT_CARD,
        ]);
    }
}
