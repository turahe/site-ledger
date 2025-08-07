# Continuous Integration (CI)

This project uses GitHub Actions for continuous integration to ensure code quality, security, and reliability.

## CI Workflow

The CI pipeline runs on every push to `main` and `develop` branches, as well as on pull requests to these branches.

### Jobs

#### 1. Tests
- **PHP Version**: 8.4
- **Laravel Version**: 11.x
- **Database**: SQLite (in-memory)
- **Coverage**: Xdebug with Codecov integration
- **Runs**: All PHPUnit tests with coverage reporting

#### 2. Static Analysis
- **PHPStan**: Level 8 static analysis
- **PHP CS Fixer**: Code style checking (PSR-12)
- **Configuration**: See `phpstan.neon` and `.php-cs-fixer.php`

#### 3. Code Quality
- **PHPMD**: Mess detection (clean code, code size, design, naming, unused code)
- **Configuration**: See `phpmd.xml`

#### 4. Build
- **Dependencies**: All previous jobs must pass
- **Optimization**: Composer autoloader optimization
- **Success**: Final build verification

## Local Development

### Running Tests
```bash
# Run all tests
./vendor/bin/phpunit

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage/

# Run specific test file
./vendor/bin/phpunit tests/Unit/InvoiceTest.php
```

### Static Analysis
```bash
# PHPStan analysis
./vendor/bin/phpstan analyse

# PHP CS Fixer (check only)
./vendor/bin/php-cs-fixer fix --dry-run --diff

# PHP CS Fixer (fix issues)
./vendor/bin/php-cs-fixer fix
```

### Code Quality
```bash
# PHPMD analysis
./vendor/bin/phpmd src text cleancode,codesize,controversial,design,naming,unusedcode
```

## Configuration Files

- **`.github/workflows/ci.yml`**: GitHub Actions workflow
- **`phpstan.neon`**: PHPStan configuration
- **`.php-cs-fixer.php`**: PHP CS Fixer configuration
- **`phpmd.xml`**: PHPMD configuration

## Requirements

- PHP 8.4+
- Composer 2.0+
- SQLite (for testing)
- Xdebug (for coverage)

## Dependencies

The following development dependencies are required for CI:

```json
{
    "phpstan/phpstan": "^1.10",
    "friendsofphp/php-cs-fixer": "^3.35",
    "phpmd/phpmd": "^2.15"
}
```

## PHP 8.4 Features

This project leverages PHP 8.4 features including:

- **Typed Class Constants**: `protected const string TABLE_NAME = 'invoices';`
- **Enhanced Enums**: Improved enum functionality with helper methods
- **Better Type Inference**: Improved type deduction throughout the codebase
- **Performance Improvements**: Optimized array functions and type handling
- **New Array Functions**: Enhanced array manipulation capabilities
- **Improved Error Handling**: Better error messages and handling

## Coverage

Code coverage is automatically generated and uploaded to Codecov on every CI run. The coverage report includes:

- Unit tests
- Feature tests
- Model relationships
- Enum functionality
- Database operations

## Status Badges

Add these badges to your README.md:

```markdown
[![CI](https://github.com/username/repo/actions/workflows/ci.yml/badge.svg)](https://github.com/username/repo/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/username/repo/branch/main/graph/badge.svg)](https://codecov.io/gh/username/repo)
[![PHP Version](https://img.shields.io/badge/php-8.4+-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-11.x-red.svg)](https://laravel.com)
```
