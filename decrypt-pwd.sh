#!/bin/sh

openssl aes-256-cbc -d \
        -md sha512 \
        -iter 100000 \
        $(test -f /root/pwd/meta && echo '-pass file:/root/pwd/meta') \
        -in /root/repo/pwd.enc \
        -out - \
  | (cd "$HOME" && tar -xzf -)
