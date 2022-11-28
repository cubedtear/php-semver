cubedtear/semver
================

Simple PHP library to parse and compare [Semantic Versioning](https://semver.org/) versions.

Installation
------------

Install the latest version with:

```bash
$ composer require cubedtear/semver
```

Requirements
------------

- PHP 8.1 is required but using the latest version of PHP is highly recommended.

Basic usage
-----------

```php
use Cubedtear\Semver\Version;

$v1 = Version::parse("0.1.1");
$v2 = Version::parse("0.1.1-alpha");
$v3 = Version::parse("1.0.12-beta+ff12b4d8");

if (Version::compare($v1, $v2)) {
    ...
}

if (Version::compare("0.1.1", "0.1.0")) {
    ...
}

```
