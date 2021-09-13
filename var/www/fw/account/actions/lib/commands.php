<?php

function getentPasswd($name) {
    $retval = -1;
    $arg = escapeshellarg($name);
    system("getent passwd $arg > /dev/null 2>&1", $retval);
    return $retval;
}

function isUsernameForbidden($name) {
    $file = '/etc/feministwiki/forbidden-names';
    $flags = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
    foreach (file($file, $flags) as $line) {
        if ($line == $name) {
            return true;
        }
    }
    return false;
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

    $command[] = $username;
    $command[] = $password;

    return execShellCommand(...$command);
}

function setPassword($username, $password) {
    return execShellCommand(
        './bin/reset-password',
        $username,
        $password
    );
}

function execShellCommand(...$argv) {
    $commandLine = implode(' ', array_map('escapeshellarg', $argv));

    // Use the 'script' utility to get the same output we would get in a tty.
    $scriptCommand = implode(' ', array(
        'script -qefc', escapeshellarg($commandLine), '/dev/null'
    ));

    $retval = -1;

    passthru($scriptCommand, $retval);

    return $retval;
}

?>
