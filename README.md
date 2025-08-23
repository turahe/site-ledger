# Turahe Ledger Package

A comprehensive Laravel package for managing financial ledgers, invoices, vouchers, and payment tracking with PHP 8.4 support.

## 🚀 Features

- **Invoice Management** - Create, update, and track invoices with items and payments
- **Voucher System** - Manage vouchers with expiration dates and status tracking
- **Payment Processing** - Support for multiple payment methods including QRIS, e-wallets, and traditional methods
- **Multi-currency Support** - Built-in currency handling with configurable defaults
- **User Tracking** - Comprehensive audit trail with user stamps
- **Business Logic** - Built-in methods for financial calculations and status checks
- **Modern PHP** - Full PHP 8.4 compatibility with enums and modern syntax
- **Laravel Integration** - Seamless integration with Laravel's ecosystem

## 📋 Requirements

- PHP 8.4+
- Laravel 12+
- MySQL/PostgreSQL/SQLite

## 🔧 Installation

1. Install the package via composer:

    ```shell
    composer require turahe/ledger
    ```

2. Publish resources (migrations and config files):

    ```shell
    php artisan vendor:publish --provider="Turahe\\Ledger\\Providers\\LedgerServiceProvider"
    ```

3. Execute migrations via the following command:

    ```shell
    php artisan migrate
    ```

4. Done!

## ⚙️ Configuration

The package configuration is located in `config/ledger.php`. You can customize:

- Default currency
- Payment method settings
- Model class mappings
- Database table names

## 📚 Usage

### Creating an Invoice

```php
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Services\LedgerService;

// Using the model directly
$invoice = Invoice::create([
    'model_id' => $user->id,
    'model_type' => User::class,
    'code' => 'INV-001',
    'total_invoice' => 1000.00,
    'due_date' => now()->addDays(30),
    'status' => 'pending',
    'currency' => 'IDR'
]);

// Using the service layer
$ledgerService = app(LedgerService::class);
$invoice = $ledgerService->createInvoice([
    'model_id' => $user->id,
    'model_type' => User::class,
    'code' => 'INV-001',
    'total_invoice' => 1000.00,
    'due_date' => now()->addDays(30)
]);
```

### Adding Invoice Items

```php
use Turahe\Ledger\Models\Invoice\Item;

$item = $invoice->items()->create([
    'name' => 'Product Name',
    'quantity' => 2,
    'unit_price' => 500.00,
    'total_price' => 1000.00,
    'description' => 'Product description'
]);

// Business logic methods
$subtotal = $item->getSubtotal();
$totalAmount = $item->getTotalAmount();
$discountPercentage = $item->getDiscountPercentage();
```

### Processing Payments

```php
use Turahe\Ledger\Models\Invoice\Payment;
use Turahe\Ledger\Enums\PaymentMethods;

$payment = $invoice->payments()->create([
    'amount' => 500.00,
    'payment_method' => PaymentMethods::E_WALLET_GOPAY,
    'payment_date' => now(),
    'status' => 'completed'
]);

// Check payment status
if ($payment->isCompleted()) {
    echo "Payment processed successfully";
}
```

### Managing Vouchers

```php
use Turahe\Ledger\Models\Voucher;

$voucher = Voucher::create([
    'model_id' => $user->id,
    'model_type' => User::class,
    'code' => 'VOUCHER-001',
    'total_value' => 500.00,
    'due_date' => now()->addDays(15),
    'status' => 'active',
    'currency' => 'IDR'
]);

// Business logic methods
if ($voucher->isExpired()) {
    echo "Voucher has expired";
}

$daysUntilExpiry = $voucher->getDaysUntilExpiry();
$formattedValue = $voucher->getTotalValueFormatted();
```

### Using Query Scopes

```php
use Turahe\Ledger\Models\Invoice;

// Find by code
$invoice = Invoice::byCode('INV-001')->first();

// Find by date range
$recentInvoices = Invoice::byDateRange(
    now()->subDays(30), 
    now()
)->get();

// Find by status
$pendingInvoices = Invoice::byStatus('pending')->get();

// Find by model
$userInvoices = Invoice::byModelType(User::class)
    ->byModelId($user->id)
    ->get();
```

### Payment Methods

```php
use Turahe\Ledger\Enums\PaymentMethods;

// Get methods by category
$qrisMethods = PaymentMethods::getByCategory('qris');
$eWalletMethods = PaymentMethods::getByCategory('e_wallet');

// Check method properties
if (PaymentMethods::E_WALLET_GOPAY->isDigital()) {
    echo "This is a digital payment method";
}

if (PaymentMethods::CASH->isInstant()) {
    echo "This is an instant payment method";
}
```

## 🧪 Testing

The package includes comprehensive test coverage. To run the tests:

```shell
# Run all tests
./vendor/bin/phpunit --testdox

# Run specific test suite
./vendor/bin/phpunit --testdox --filter=InvoiceTest

# Run with coverage
./vendor/bin/phpunit --coverage-text
```

### Test Coverage

- **Unit Tests** - Model methods, business logic, and enums
- **Feature Tests** - Complete workflow testing
- **Business Logic Tests** - Financial calculations and validations

## 🔄 Recent Optimizations

The package has been recently optimized with:

- **PHP 8.4 Enum Support** - Modern enum syntax and utility methods
- **Enhanced Models** - Improved relationships, casting, and business logic
- **Service Layer** - Centralized business logic in `LedgerService`
- **Trait System** - Reusable `HasLedgerAttributes` trait for common functionality
- **Interface Contracts** - `LedgerModelInterface` for consistent model behavior
- **Custom Exceptions** - Specific error handling for ledger operations
- **Improved Migrations** - Fixed schema issues and added missing columns
- **Better Testing** - Comprehensive test coverage with proper setup

## 📁 Package Structure

```
src/
├── Enums/                 # Payment methods and transaction types
├── Exceptions/            # Custom exception classes
├── Models/                # Eloquent models
│   ├── Concerns/          # Reusable traits
│   ├── Contracts/         # Interface definitions
│   ├── Invoice/           # Invoice-related models
│   └── Voucher/           # Voucher-related models
├── Providers/             # Service providers
└── Services/              # Business logic services
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

If you encounter any issues or have questions:

- Check the [issues page](https://github.com/turahe/ledger/issues)
- Create a new issue with detailed information
- Ensure you're using the latest version

## 🔗 Related Packages

- [UserStamps](https://github.com/turahe/userstamps) - User tracking for models
- [ULID](https://github.com/robsontenorio/laravel-ulid) - ULID support for Laravel
