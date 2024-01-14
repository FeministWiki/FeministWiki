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
	$stats = opcache_get_status();
	unset($stats['scripts']);
	print_r($stats);
},

'apcu-stats' => function () {
	$stats = [];
	$stats['cache'] = apcu_cache_info();
	unset($stats['cache']['cache_list']);
	unset($stats['cache']['deleted_list']);
	unset($stats['cache']['slot_distribution']);
	$stats['sma'] = apcu_sma_info();
	unset($stats['sma']['block_lists']);
	print_r($stats);
},

];

$command = $commands[$_SERVER['QUERY_STRING']];
if ($command) {
	$command();
} else {
	println('Unrecognized command.');
}
