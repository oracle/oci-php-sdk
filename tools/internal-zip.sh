#!/bin/bash

zip -r oci-php-sdk-0.0.1-`date +"%Y-%m-%dT%H-%M-%S"`.zip \
  `find . -type f \
    -not -path ./codegen/\* \
    -not -path ./vendor/\* \
    -not -path ./.git/\* \
    -not -path ./.php\*cache \
    -not -path ./tools/internal-zip.sh \
    `
