#!/bin/sh

find etc root var -type f -exec sh -c 'for file; do cp /"$file" "$file"; done' {} +

echo
echo '~~~~~~~~~~~~~~~~~~~~~'
echo '~~ !!! WARNING !!! ~~'
echo '~~~~~~~~~~~~~~~~~~~~~'
echo
echo 'Do not forget to redact sensitive information from the repo.'
echo
echo 'Use "git diff" and go through all new content to ensure that'
echo 'no passwords, hash salts, or other secret tokens are included.'
echo
