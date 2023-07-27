<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

// $redstr = '123';
// echo file_put_contents('red_postail.txt', $redstr.PHP_EOL, FILE_APPEND);

// die;

$tailarr = [
	1 => [4, 6, 7],
	2 => [4, 6, 7],
	3 => [4, 6, 7],
	4 => [1,5, 7, 8],
	5 => [1,5, 7, 8],
	6 => [1,5, 7, 8],
];

$tailarr = [
	1 => [7,8],
	2 => [8,7],
	3 => [5],
	4 => [1, 2, 3],
	5 => [1, 2, 3],
	6 => [0, 1, 3],
];

$tailarr = [
	1 => [6,7],
	2 => [6,7],
	3 => [3],
	4 => [0,9],
	5 => [9,0],
	6 => [2],
];

$sid = 1;
$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_weermy_cpdata` WHERE sid='$sid'");

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$t1 = time();

$namekey = 'SSQ110W';
//获取彩票模拟大数据总数
$cpzhnum = $redis->lSize($namekey);
$pernum  = 10000 * 10;
$pgnum   = ceil($cpzhnum / $pernum);

$total = 0;
$filterData = array();
for ($pg=1; $pg <= $pgnum; $pg++) { 
	$s_index = ($pg - 1) * $pernum;
	$e_index = ($pg - 1) * $pernum + $pernum;

	$list = $redis->lRange($namekey, $s_index, $e_index);

	//开始计算
	foreach ($list as $i => $cpinfo) {
		$cpinfo = json_decode($cpinfo,true);
		$redarr = $cpinfo['red'];

		$red1tail = $redarr[0] % 10;
		$red2tail = $redarr[1] % 10;
		$red3tail = $redarr[2] % 10;
		$red4tail = $redarr[3] % 10;
		$red5tail = $redarr[4] % 10;
		$red6tail = $redarr[5] % 10;

		if( !in_array($red1tail, $tailarr[1]) || 
			!in_array($red2tail, $tailarr[2]) || 
			!in_array($red3tail, $tailarr[3]) || 
			!in_array($red4tail, $tailarr[4]) || 
			!in_array($red5tail, $tailarr[5]) || 
			!in_array($red6tail, $tailarr[6]) ){

			continue;
		}

		$redstr = implode('.', $redarr);
		file_put_contents('E:\001web_preject\datasay\2017\wxpage\red_postail.txt', $redstr.PHP_EOL, FILE_APPEND);

		$blue = '02';
		$ssq = $redstr . '+' . $blue;
		$sql = "INSERT INTO `#@__caipiao_weermy_cpdata` (sid, ssq, winlevel) VALUES ('".$sid."', '".$ssq."', '0')";
		$dosql->ExecNoneQuery($sql);

		$total++;

		echo $total . '：' . $redstr;
		echo "\n\r";
	}
}