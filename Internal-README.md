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

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```