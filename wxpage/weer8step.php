<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/weer.config.php';

$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` ORDER BY updatetime DESC");
$myweercfg = unserialize($row['weercfg']);

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$t1 = time();

$namekey = 'SSQ110W';
//获取彩票模拟大数据总数
$cpzhnum = $redis->lSize($namekey);
$pernum  = 10000 * 10;
$pgnum   = ceil($cpzhnum / $pernum);

$t1 = time();

// $restcfg = FormatWeerCfg($myweercfg);
$restcfg = $myweercfg;

$timearr = array('t1'=>0, 't2'=>0, 't3'=>0);
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

		$rest = RedWeerFilter($redarr, $restcfg);

		if($rest === false) continue;

		$filterData[] = $rest;

		$total++;

		// echo $total . '：' . implode(".", $rest);
		// echo "\n\r";
	}
}

echo $row['id'];
echo "\n\r";
$t2 = time();
echo ($t2-$t1);
echo "\n\r";

foreach ($filterData as $k => $v) {
	if($k == count($filterData) - 1){
		echo implode(".", $v);
	}else{
		echo implode(".", $v);
		echo "\n\r";
	}
}