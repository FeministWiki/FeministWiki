<?php

function generatePassword() {
    # NOTE: The dictionary MUST be sorted by line length!
    $words = explode("\n", file_get_contents(__DIR__ . '/.pwd-dict'));
    $count = count($words);

    if ($count < 40000) {
        exit('Less than 40k entries in password generation dictionary.');
    }

    $result = '';

    # Our longest words are 10 chars, so this ensures the length will be
    # between 12 and 21 characters.
    while (strlen($result) < 12) {
        $index = random_int(0, $count - 1);
        $result .= ucfirst($words[$index]);
    }

    return $result . random_int(1000, 9999);
}

?>
