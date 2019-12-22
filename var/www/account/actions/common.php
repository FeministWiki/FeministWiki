<?php

header('Content-Type: text/plain');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

if (PHP_SAPI == 'cli') {
    parse_str(implode('&', array_slice($argv, 1)), $_POST);
}

function println($line) {
    printf("%s\n", $line);
}

function printAndExit() {
    foreach (func_get_args() as $line) {
        println($line);
    }
    println('');
    println('Use the "back" button of your web browser to return to the previous page.');
    exit;
}

function composeEmailHeaders() {
    $result = '';
    foreach (func_get_args() as $header) {
        $result .= "$header\r\n";
    }
    return $result;
}

function composeEmailBody() {
    $result = "<html><body>\n";
    foreach (func_get_args() as $line) {
        $result .= "<p>$line</p>\n";
    }
    return $result . '</body></html>';
}

function generatePassword() {
    # NOTE: The dictionary MUST be sorted by line length!
    $words = explode("\n", file_get_contents('.dict'));
    $count = count($words);
    $result = '';
    while (strlen($result) < 12) {
        $word = $words[random_int(0, $count)];
        $result .= ucfirst($word);
    }
    # Maybe add another short word
    if (strlen($result) < 16) {
        $idx = 0;
        while (++$idx) {
            if (strlen($words[$idx]) == 5) {
                break;
            }
        }
        $word = $words[random_int(0, $idx)];
        $result .= ucfirst($word);
    }
    $suffix = random_int(1000, 9999);
    return $result . $suffix;
}

if ($skipSecurityCheck === TRUE) {
    return;
}

$clientIP = $_SERVER['REMOTE_ADDR'];

$clientSeedFile = "riddle/ipdb/$clientIP";

if (!file_exists($clientSeedFile)) {
    printAndExit('No security token on server.');
}

$data = unserialize(file_get_contents($clientSeedFile));

$clientSeed = $data['seed'];
$seedTime = $data['time'];

$securityToken = json_decode($_POST['riddleInput'], true);

if ($securityToken === NULL) {
    printAndExit('No security token received.');
}

if ($securityToken['y'] != $clientSeed) {
    printAndExit('Invalid security token.');
}

if (time() < $seedTime + 5) {
    printAndExit('That was a bit too fast!');
}

# Test passed so remove seed file
unlink($clientSeedFile);

?>
