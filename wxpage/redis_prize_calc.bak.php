<?php

//计算获得几等奖
function calcPrize($red_win_num, $blue_win_num){
	$prizelevel = 0;
	if($blue_win_num == 1 && $red_win_num < 3){
		$prizelevel = 6;
	}else if(($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)){
		$prizelevel = 5;
	}else if(($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)){
		$prizelevel = 4;
	}else if($red_win_num == 5 && $blue_win_num == 1){
		$prizelevel = 3;
	}else if($red_win_num == 6 && $blue_win_num == 0){
		$prizelevel = 2;
	}else if($red_win_num == 6 && $blue_win_num == 1){
		$prizelevel = 1;
	}
	return $prizelevel;
}

//奖号
$prizeInfo = array(
	'red' => array('18','26','10','07','16','23'),
	'blue'=> '08'
);

$prizeInfo = array(
	'red' => array('03','06','09','17','20','31'),
	'blue'=> '12'
);

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$prefix   = '2018';
$cp_dayid = '27qi';
$namekey  = $prefix . $cp_dayid;

//redis奖项计数
$prizeKey = array(
	1 => $cp_dayid.'_p1',
	2 => $cp_dayid.'_p2',
	3 => $cp_dayid.'_p3',
	4 => $cp_dayid.'_p4',
	5 => $cp_dayid.'_p5',
	6 => $cp_dayid.'_p6',
);

//获取彩票模拟大数据总数
$cpnum = $redis->lSize($namekey);
$cpnum = 10000 * 100;

echo "\n\r计算开始......\n\r";

echo "彩票基数：" . $cpnum . "注\n\r";

$t1 = time();

$i = 1000000;
$cpnum = 1100000;
//开始计算
for ($i=0; $i < $cpnum; $i++) { 
	$cpinfo = $redis->lGet($namekey, $i);
	$cpinfo = json_decode($cpinfo,true);
	//兑奖则跳过
	if(isset($cpinfo['iscalc'])){
		continue;
	}

	$red  = $cpinfo['red'];
	$blue = $cpinfo['blue'];
	
	//命中信息
	$redwin     = array_intersect($prizeInfo['red'], $red);
	$redwin     = count($redwin);
	$bluewin    = $blue == $prizeInfo['blue'] ? 1 : 0;
	$prizelevel = calcPrize($redwin, $bluewin);

	//统计中奖数量
	// $cpinfo['iscalc'] = 1;
	if(isset($prizeKey[$prizelevel])){
		$prizelevel_key = $prizeKey[$prizelevel];
		if(!$redis->exists($prizelevel_key)){
			$redis->set($prizelevel_key, 0);
		}
		$redis->incr($prizelevel_key);
	}
	// $cpinfo['level'] = $prizelevel;
	// $cpinfo = json_encode($cpinfo);
	// $redis->lSet($namekey, $i, $cpinfo);
	echo $prizelevel ? "中{$i} " : "-";
}

echo "\n\r\n\r";
echo "------计算结果------";
echo "\n\r\n\r";
foreach ($prizeKey as $level => $prizelevel_key) {
	echo $level . '等奖数量：' . ($redis->exists($prizelevel_key) ? $redis->get($prizelevel_key) : 0);
	echo "\n\r";
}

$t2 = time();

echo "耗时：" . ($t2-$t1) . "秒\n\r";
echo "\n\r";