#!/bin/bash

set -e
# set -x

gitBranch=`git rev-parse --abbrev-ref HEAD`
if [[ "master" == "$gitBranch" ]]; then
  gitBranch=""
else
  gitBranch="${gitBranch}-"
fi

gitCommit=""
if [[ `git status | head -n 2 | grep "behind"` ]]; then
  gitBranch="`git rev-parse HEAD`-"
fi
if [[ `git status | head -n 2 | grep "diverged"` ]]; then
  gitBranch="`git rev-parse HEAD`-"
fi

fileName="oci-php-sdk-0.0.1-${gitBranch}`date +"%Y-%m-%dT%H-%M-%S"`.zip"

zip -r $fileName \
  `find . -type f \
    -not -path ./codegen/\* \
    -not -path ./vendor/\* \
    -not -path ./.git/\* \
    -not -path ./.php\*cache \
    -not -path ./tools/internal-zip.sh \
    -not -path ./oci-php-sdk\*.zip \
    `

echo "Created $fileName"
