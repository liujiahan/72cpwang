<?php

function randdata($data = array()){
	$num = rand(1, 33);
	$num = $num < 10 ? '0' . $num : $num;

	if(count($data) < 6){
		if(!in_array($num, $data)){
			$data[] = $num;
			if(count($data) < 6){
				return randdata($data);
			}else{
				return $data;
			}
		}else{
			return randdata($data);
		}
	}else{
		return $data;
	}
}

$num = 10000 * 1000;

$prefix   = '2019';
$cp_dayid = '084Q';
// $cp_dayid = '888888';
$namekey  = $prefix . $cp_dayid;

$redis = new Redis();  
$redis->connect('127.0.0.1',6379);

$t1 = time();
for ($i=0; $i < $num ; $i++) { 
	$red = array();
	$red = randdata($red);
	$blue = rand(1,16);
	$blue = $blue < 10 ? '0' . $blue : $blue;
	// $red = array('01','10', '13', '20', '27', '31');
	// $blue = 11;
	$data = array(
		'red' => $red,
		'blue' => $blue,
		'time' => time()
	);
	$data = json_encode($data);
	$redis->rPush($namekey, $data);
	echo implode('.', $red) . ' + ' . $blue;
	echo "\n\r";
}
$t2 = time();

echo '当前生成'.$num."注，耗时：" . ($t2-$t1);