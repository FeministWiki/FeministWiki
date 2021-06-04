<?php

$clientID = md5($_SERVER['REMOTE_ADDR']);
$riddleInput = $_POST['riddleInput'];

$clientSeedFile = __DIR__ . "/clients/$clientID";

if (!file_exists($clientSeedFile)) {
    printAndExit('No security token on server.');
}

$data = unserialize(file_get_contents($clientSeedFile));

if ($data === FALSE) {
    printAndExit('Couldn\'t parse client seed file.');
}

$securityToken = json_decode($riddleInput, true);

$clientSeed = $data['seed'];
$seedTime = $data['time'];

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
