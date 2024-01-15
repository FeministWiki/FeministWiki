#!/bin/sh

if ! [ "$(pwd)" = /root/repo/fw ];
then
    echo >&2 'Run this from /root/repo/fw!'
    exit 1
fi

for arg
do
    case $arg in
        (-np)
            no_pwd=yes
            ;;
        (*)
            echo >&2 'Invalid arguments.'
            exit 1
    esac
done

find etc root var -type f -exec sh -c 'for file; do cp -a /"$file" "$file"; done' {} +

sed -ri 's/(ldap_password): "[^"]+"/\1: "[REDACTED]"/' etc/ejabberd/ejabberd.yml
sed -ri 's/(key|password|bindauth)="[^"]+"/\1="[REDACTED]"/' etc/inspircd/inspircd.conf

if ! [ "$no_pwd" ]
then
    (cd /root; tar -czf- pwd) \
        | openssl aes-256-cbc \
                  -md sha512 \
                  -pbkdf2 \
                  -iter 100000 \
                  -pass file:/root/pwd/meta \
                  -in - \
                  -out pwd.enc
fi

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
