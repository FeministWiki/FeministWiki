#!/bin/sh

languages='de en es fr it pt'

for dblang in $languages
do
    if [ $dblang = en ]
    then
        db=feministwiki
    else
        db=feministwiki_$dblang
    fi

    for lang in $languages
    do
        if [ $lang = en ]
        then
            url=https://feministwiki.org/wiki/\$1
        else
            url=https://feministwiki.org/$lang/wiki/\$1
        fi
        echo "
        insert into $db.interwiki (
          iw_prefix, iw_url, iw_api, iw_wikiid, iw_local
        ) values (
          '$lang',   '$url', '',     '',        1
        );
        " | sql
    done
done
