# CryptoMañana Framework
[![GitHub tag (latest SemVer)](https://img.shields.io/github/tag/tonykaravasilev/cryptomanana.svg?color=blue&label=stable&style=flat-square)](https://packagist.org/packages/karavasilev/cryptomanana)
[![PHP from Travis config](https://img.shields.io/travis/php-v/TonyKaravasilev/CryptoManana.svg?style=flat-square)](https://packagist.org/packages/karavasilev/cryptomanana)
[![PHP from Travis config](https://img.shields.io/badge/php-%3C%3D%207.4-blue.svg?style=flat-square)](https://packagist.org/packages/karavasilev/cryptomanana)
[![GitHub](https://img.shields.io/github/license/tonykaravasilev/cryptomanana.svg?color=blue&label=license&style=flat-square)](https://github.com/TonyKaravasilev/CryptoManana/blob/master/LICENSE)
[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.2604328.svg)](https://doi.org/10.5281/zenodo.2604328) <br>
[![Build Status](https://travis-ci.org/TonyKaravasilev/CryptoManana.svg?branch=master)](https://travis-ci.org/TonyKaravasilev/CryptoManana)
[![Code Coverage](https://scrutinizer-ci.com/g/TonyKaravasilev/CryptoManana/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/TonyKaravasilev/CryptoManana/?branch=master)
[![PHPDoc Coverage](https://img.shields.io/badge/PHPDoc-100%25-success.svg?style=flat)](https://cryptomanana.karavasilev.info/api/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TonyKaravasilev/CryptoManana/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TonyKaravasilev/CryptoManana/?branch=master)
[![Donate](https://img.shields.io/badge/Donate-PayPal-Success.svg?style=flat&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=BFKJXWRLFTFQA&currency_code=USD&source=url) <br>
&nbsp;[![CryptoManana Logo](http://karavasilev.info/images/CryptoMananaLogo.jpg)](https://cryptomanana.karavasilev.info)

## Project Description
CryptoMañana (CryptoManana) is a PHP cryptography framework that provides object-oriented solutions for boosting your project's security.
The code base of the project follows the S.O.L.I.D principles and implements a few popular Software Design Patterns.
It is very useful for secure hashing, encryption, random data generation and even more.
CryptoMañana is here to make your development faster and more secure!

**Developer: [Tony Karavasilev](http://karavasilev.info)**

## Project Installation
```bash
# Install the package at your project via Composer
composer require karavasilev/cryptomanana

# Optionally, check if your system is well-configured
php vendor/karavasilev/cryptomanana/check.php
```

## Project Requirements
- `PHP Version`: 5.5, 5.6, 7.0, 7.1, 7.2, 7.3 or 7.4-dev (snapshot);
- The `spl` extension (bundles with PHP >= 5.0.0, added to core since PHP >= 5.3.0);
- The `hash` extension (bundled with PHP >= 5.1.2, added to core since PHP >= 7.4.0);
- The `openssl` extension (added by default for PHP >= 5.0.0, needs the OpenSSL Library);
- The `OpenSSL Library` installed by default with many Operating Systems and LAMP servers;
- The `Composer Dependency Manager` for PHP or manual autoloading via `src/autoload.php`;
- *Optional Extensions:* `libsodium` or`sodium`, `mbstring`, `zend-opcache` and `apcu`.

## Project Documentation
- [**Framework Documentation**](https://cryptomanana.karavasilev.info/);
- [**Technical API Documentation**](https://cryptomanana.karavasilev.info/api/).

## Project Citation via DOI
- **v0.1.0** - [DOI 10.5281/zenodo.2604329](http://doi.org/10.5281/zenodo.2604329)

## Running The Tests Locally (OPTIONAL)
```bash
git clone --depth=1 https://github.com/TonyKaravasilev/CryptoManana.git
cd CryptoManana && composer install --profile
vendor/bin/phpunit --testdox --no-coverage
vendor/bin/phpcs
```

## Enable Unicode Support (OPTIONAL)
- Install and enable the `mbstring` PHP extension;
- Configure the encoding and enable CryptoManana to use it:
```php
// Autoload packages via Composer class autoloader
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Configure `mbstring` to use your favourite encoding
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

// Enable the `mbstring` support for CryptoManana components
\CryptoManana\Core\StringBuilder::useMbString(true);

// Start coding hard...
```
*Note: The framework works without the extension and does not enable the usage of it by default for performance reasons.*

## Performance And Security Tips (OPTIONAL)
- Always update your OpenSSL Library to the latest version;
- Always update your Operating System and Kernel;
- Always update your PHP and used extensions;
- Always update your Composer dependencies;
- Harvest the power of Zend OPcache;
- Use Composer APCu optimization;
- Increase the resources for PHP.
