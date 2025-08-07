# Turahe Ledger

[![CI](https://github.com/turahe/ledger/actions/workflows/ci.yml/badge.svg)](https://github.com/turahe/ledger/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/turahe/ledger/branch/main/graph/badge.svg)](https://codecov.io/gh/turahe/ledger)
[![PHP Version](https://img.shields.io/badge/php-8.4+-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-11.x-red.svg)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A comprehensive ledger management system for handling vouchers and invoices in Laravel applications. Built with PHP 8.4 and Laravel 11.x, featuring modern PHP features and robust testing.

## 🚀 Features

- **Invoice Management**: Create, update, and manage invoices with detailed item tracking
- **Voucher System**: Handle vouchers with flexible item management
- **Payment Processing**: Track payments with multiple payment methods and gateways
- **ULID Primary Keys**: Universally Unique Lexicographically Sortable Identifiers for better performance
- **PHP 8.4 Support**: Leverages latest PHP features including typed class constants and enhanced enums
- **Comprehensive Testing**: 51 tests with 184 assertions ensuring reliability
- **Static Analysis**: PHPStan Level 8 analysis for code quality
- **Code Quality**: PHPMD and PHP CS Fixer for clean, maintainable code

## 📋 Requirements

- PHP 8.4+
- Laravel 11.x
- Composer 2.0+

## 🛠 Installation

1. **Install the package via composer:**

    ```shell
    composer require turahe/ledger
    ```

2. **Publish resources (migrations and config files):**

    ```shell
    php artisan vendor:publish --provider="Turahe\\Ledger\\Providers\\LedgerServiceProvider"
    ```

3. **Execute migrations:**

    ```shell
    php artisan migrate
    ```

4. **Done!** 🎉

## 📖 Usage

### Creating Invoices

```php
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Database\Factories\InvoiceFactory;

// Create an invoice
$invoice = InvoiceFactory::new()->create([
    'model_id' => $user->id,
    'model_type' => get_class($user),
    'code' => 'INV-001',
    'currency' => 'USD',
    'total_amount' => 1000.00,
]);

// Add items to invoice
$invoice->items()->create([
    'model_id' => $product->id,
    'model_type' => get_class($product),
    'quantity' => 2,
    'price_unit' => 500.00,
]);
```

### Creating Vouchers

```php
use Turahe\Ledger\Models\Voucher;
use Turahe\Ledger\Database\Factories\VoucherFactory;

// Create a voucher
$voucher = VoucherFactory::new()->create([
    'model_id' => $user->id,
    'model_type' => get_class($user),
    'code' => 'VOU-001',
    'total_value' => 500.00,
]);

// Add items to voucher
$voucher->items()->create([
    'model_id' => $product->id,
    'model_type' => get_class($product),
    'quantity' => 1,
    'value' => 500.00,
]);
```

### Payment Processing

```php
use Turahe\Ledger\Models\Invoice\Payment;
use Turahe\Ledger\Enums\PaymentMethods;

// Create a payment
$payment = Payment::create([
    'invoice_id' => $invoice->id,
    'receipt_id' => $voucher->id,
    'currency' => 'USD',
    'amount' => 1000.00,
    'payment_method' => PaymentMethods::CREDIT_CARD_MASTERCARD,
    'payment_gateway' => 'midtrans',
    'payment_status_code' => '200',
    'payment_status_message' => 'Payment successful',
]);
```

### Working with Enums

```php
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\Ledger\Enums\RecordEntry;
use Turahe\Ledger\Enums\Transaction;

// Get all payment methods
$methods = PaymentMethods::values();

// Get payment methods by category
$creditCards = PaymentMethods::byCategory('credit_card');

// Check record entry types
$isCredit = RecordEntry::CREDIT->isCredit(); // true
$isDebit = RecordEntry::DEBIT->isDebit();   // true

// Check transaction types
$isIncome = Transaction::INCOME->isIncome(); // true
$isExpense = Transaction::EXPENSE->isExpense(); // true
```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage/

# Run specific test file
./vendor/bin/phpunit tests/Unit/InvoiceTest.php
```

### Test Coverage

The project includes comprehensive tests covering:

- **Unit Tests**: 51 tests with 184 assertions
- **Model Relationships**: Invoice, Voucher, Item, and Payment relationships
- **Enum Functionality**: Payment methods, record entries, and transactions
- **Database Operations**: CRUD operations for all models
- **ULID Support**: Primary key generation and validation

## 🔧 Development

### Code Quality Tools

```bash
# Static analysis
./vendor/bin/phpstan analyse

# Code style check
./vendor/bin/php-cs-fixer fix --dry-run --diff

# Code style fix
./vendor/bin/php-cs-fixer fix

# Code quality analysis
./vendor/bin/phpmd src text cleancode,codesize,controversial,design,naming,unusedcode
```

### PHP 8.4 Features

This project leverages PHP 8.4 features including:

- **Typed Class Constants**: `protected const string TABLE_NAME = 'invoices';`
- **Enhanced Enums**: Improved enum functionality with helper methods
- **Better Type Inference**: Improved type deduction throughout the codebase
- **Performance Improvements**: Optimized array functions and type handling
- **New Array Functions**: Enhanced array manipulation capabilities
- **Improved Error Handling**: Better error messages and handling

## 🚀 CI/CD

The project uses GitHub Actions for continuous integration:

### CI Pipeline

1. **Tests** - PHP 8.4 + Laravel 11.x + SQLite + Code coverage
2. **Static Analysis** - PHPStan Level 8 + PHP CS Fixer
3. **Code Quality** - PHPMD analysis
4. **Build** - Final verification and optimization

### Status Badges

- [![CI](https://github.com/turahe/ledger/actions/workflows/ci.yml/badge.svg)](https://github.com/turahe/ledger/actions/workflows/ci.yml)
- [![Code Coverage](https://codecov.io/gh/turahe/ledger/branch/main/graph/badge.svg)](https://codecov.io/gh/turahe/ledger)
- [![PHP Version](https://img.shields.io/badge/php-8.4+-blue.svg)](https://php.net)
- [![Laravel Version](https://img.shields.io/badge/laravel-11.x-red.svg)](https://laravel.com)
- [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 📁 Project Structure

```
ledger/
├── src/
│   ├── Enums/
│   │   ├── PaymentMethods.php
│   │   ├── RecordEntry.php
│   │   └── Transaction.php
│   ├── Models/
│   │   ├── Invoice/
│   │   │   ├── Item.php
│   │   │   └── Payment.php
│   │   ├── Voucher/
│   │   │   └── Item.php
│   │   ├── Invoice.php
│   │   └── Voucher.php
│   └── Providers/
│       └── LedgerServiceProvider.php
├── tests/
│   ├── Unit/
│   │   ├── EnumTest.php
│   │   ├── InvoiceTest.php
│   │   ├── InvoiceItemTest.php
│   │   ├── InvoicePaymentTest.php
│   │   ├── InvoicePaymentModelTest.php
│   │   ├── VoucherTest.php
│   │   ├── VoucherItemTest.php
│   │   └── ModelRelationshipsTest.php
│   └── TestCase.php
├── database/
│   ├── migrations/
│   │   ├── create_vouchers_table.php
│   │   ├── create_invoices_table.php
│   │   └── create_invoice_payments_table.php
│   └── factories/
│       ├── InvoiceFactory.php
│       └── VoucherFactory.php
└── config/
    └── config.php
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/turahe/ledger.git
cd ledger

# Install dependencies
composer install

# Run tests
./vendor/bin/phpunit

# Run code quality checks
./vendor/bin/phpstan analyse
./vendor/bin/php-cs-fixer fix --dry-run --diff
./vendor/bin/phpmd src text cleancode,codesize,controversial,design,naming,unusedcode
```

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Nur Wachid** - [wachid@outlook.com](mailto:wachid@outlook.com)

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- PHP community for PHP 8.4 features
- All contributors and testers

---

Made with ❤️ using PHP 8.4 and Laravel 11.x
