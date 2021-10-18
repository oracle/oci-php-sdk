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
