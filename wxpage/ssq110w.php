<?php

require_once dirname(__FILE__).'/core/core.func.php';

$red33 = array();
for ($i=1; $i < 34; $i++) { 
	$i = $i<10 ? '0'.$i : $i;
	$red33[] = $i;
}

ini_set('memory_limit', '3000M');

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$t1 = time();

$namekey = 'SSQ110W';

$allZH = combination($red33, 6);

foreach ($allZH as $k => $red) {
	$k = $k+1;
	$data = array();
	$data['red'] = $red;
	$data = json_encode($data);
	$redis->rPush($namekey, $data);
	echo $k;
	echo "\n\r";
}