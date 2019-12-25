<?php

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
