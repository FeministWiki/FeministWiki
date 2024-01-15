<?php

header("Cache-Control: no-store");

if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
	exit('Forbidden.');
}

function println($line) {
    printf("%s\n", $line);
}

$commands = [

'opcache-stats' => function () {
	$stats = opcache_get_status(false);
	print_r($stats);
},

'opcache-reset' => function () {
	opcache_reset();
	println('Done.');
},

'apcu-stats' => function () {
	$stats = [];
	$stats['cache'] = apcu_cache_info(true);
	$stats['sma'] = apcu_sma_info(true);
	print_r($stats);
},

'reload' => function () {
	opcache_invalidate(__FILE__, true);
	println('Done.');
},

'ping' => function () {
	println('Pong.');
},

];

$command = $commands[$_SERVER['QUERY_STRING']];
if ($command) {
	$command();
} else {
	println('Unrecognized command.');
}
