#!/bin/bash

scriptDir=`dirname $0`

if $scriptDir/php-cs-fixer fix | grep -E "^ *[1-9][0-9]*) (src|test)"; then
    echo "Formatting changes found, aborting commit..."
    exit 1
fi