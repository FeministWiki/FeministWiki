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
