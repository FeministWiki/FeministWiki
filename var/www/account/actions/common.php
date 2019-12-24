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
    $words = explode("\n", file_get_contents('.dict'));
    $count = count($words);

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

# Returns a unix-style exit code, i.e. 0 = success.
function addMember($username, $password, $email = '', $recoveryMail = '', $manager = '') {
    $command = array('./bin/add-member');

    if ($email != '') {
        $command[] = "-e";
        $command[] = $email;
    }

    if ($recoveryMail != '') {
        $command[] = "-r";
        $command[] = $recoveryMail;
    }

    if ($manager != '') {
        $command[] = '-m';
        $command[] = $manager;
    }

    $command[] = escapeshellarg($username);
    $command[] = escapeshellarg($password);

    $commandLine = implode(' ', $command);

    // Use the 'script()' utility to get the same output we would get in a tty.
    $scriptCommand = implode(' ', array(
        'script -qefc', escapeshellarg($commandLine), '/dev/null'
    ));

    $retval = 0;

    passthru($scriptCommand, $retval);

    return $retval;
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

function sendWelcomeEmail($address, $username, $password) {
    $subject = 'Your FeministWiki account has been created!';
    $headers = composeEmailHeaders(
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: FeministWiki <admin@feministwiki.org>',
        'Reply-To: FeministWiki Technician <admin@feministwiki.org>'
    );
    $body = composeEmailBody(
        "Welcome to the FeministWiki, $username!",
        '',
        'A password of random English words was automatically '
        . 'generated for your account:',
        '',
        $password,
        '',
        'You can change your password and other settings here:',
        '',
        '<a href="https://account.feministwiki.org/settings.html">'
        . 'FeministWiki Account Settings'
        . '</a>',
        '',
        'If you would like to learn more about the FeministWiki, '
        . 'please refer to the Welcome Page:',
        '',
        '<a href="https://feministwiki.org/wiki/FW:Welcome">'
        . 'Welcome to the FeministWiki!'
        . '</a>',
        '',
        'If you have any questions, don\'t be shy to ask! '
        . 'Replies to this email will be forwarded to the technician.'
    );

    return mail($address, $subject, $body, $headers);
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
