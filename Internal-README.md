# This is the public PHP SDK for Oracle Cloud Infrastructure.

# Codegen

You can run the codegen using:

```
mvn clean install
```

You can run the codegen for a single spec using:

```
mvn clean install --projects codegen/objectstorage
```


# Testing

You can run the unit tests using:

```
vendor/bin/phpunit --bootstrap vendor/autoload.php tests
```


# Running Examples

You can run the examples using:

```
php src/Oracle/Oci/Examples/ObjectStorageExample.php
```

# Development Requirements

## PHP Versions

PHP 5.6 is EOL. You can still install it using:

```
brew tap shivammathur/php
brew install shivammathur/php/php@5.6
brew unlink php && brew link --overwrite --force php@5.6
php -v
```

[Source](https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions)

## Composer

Composer is a package manager for PHP.

[Downloading and Installing Composer](https://getcomposer.org/download/)

