<?php

function indexOfFirstLengthN($words, $length) {
    $lower = 0;
    $upper = count($words);
    $index = $upper / 2;
    while ($upper > $lower + 1) {
        $l = strlen($words[$index]);
        if ($l < $length) {
            $lower = $index;
            $index = $lower + ($upper - $lower) / 2;
        } else if ($l >= $length) {
            $upper = $index;
            $index = $lower + ($upper - $lower) / 2;
        }
    }
    return $upper;
}

function generatePassword() {
    # NOTE: The dictionary MUST be sorted by line length!
    $words = explode("\n", file_get_contents(__DIR__ . '/.pwd-dict'));
    $count = count($words);

    if ($count < 40000) {
        exit('Less than 40k entries in password generation dictionary.');
    }

    $result = '';

    # Our longest words are 10 chars, so this
    # ensures that max length is 15 + 10 = 25.
    while (strlen($result) < 16) {
        $word = $words[random_int(0, $count - 1)];
        $result .= ucfirst($word);
    }

    # Make sure that the minimum length is 20.
    if (strlen($result) < 20) {
        # Max length 6, so 19 + 6 = 25.
        $index = indexOfFirstLengthN($words, 7);
        $word = $words[random_int(0, $index - 1)];
        $result .= ucfirst($word);
    }

    return $result . random_int(1000, 9999);
}

?>
