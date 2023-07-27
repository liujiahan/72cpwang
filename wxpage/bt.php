<?php
// require_once dirname(__FILE__).'/../include/config.inc.php';

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$namekey = 'BT_4';
//获取彩票模拟大数据总数
$cpnum = $redis->lSize($namekey);

$pernum = 10000 * 10;
$pgnum  = ceil($cpnum / $pernum);

$ssq = array();
$bcount = array();
for ($pg=1; $pg <= $pgnum; $pg++) { 
	$s_index = ($pg - 1) * $pernum;
	$e_index = ($pg - 1) * $pernum + $pernum;

	$list = $redis->lRange($namekey, $s_index, $e_index);

	//开始计算
	foreach ($list as $i => $cpinfo) {
		$i = $s_index + $i;
		$txt = $cpinfo;
		$cpinfo = json_decode($cpinfo,true);

		$red  = $cpinfo['red'];
		$blue = $cpinfo['blue'];

		$ssq[] = implode('.', $red).'+'.implode('.',$blue);

		$b = $blue[0];
		if(!isset($bcount[$b])){
			$bcount[$b] = 0;
		}
		$bcount[$b]++;
	}
}
ksort($bcount);

foreach ($bcount as $blue => $num) {
	echo "蓝号{$blue}：{$num}次";
	if($blue % 4 == 0){
		echo "<br/>";
		echo "<br/>";
	}else{
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
}

echo "<br/>";
echo "<br/>";

// foreach ($ssq as $k => $red) {
// 	echo $red;
// 	if(($k+1) % 4 == 0){
// 		echo "<br/>";
// 	}else{
// 		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
// 	}
// }
// print_r($bcount);