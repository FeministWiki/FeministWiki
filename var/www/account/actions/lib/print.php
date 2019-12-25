<?php

function println($line) {
    printf("%s\n", $line);
}

function printAndExit(...$lines) {
    foreach ($lines as $line) {
        println($line);
    }
    println('');
    println('Use the "back" button of your web browser to return to the previous page.');
    exit;
}

function adminError($message) {
    printAndExit(
        "Error: $message",
        '       Please contact admin@feministwiki.org'
    );
}

?>
