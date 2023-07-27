<?php

require_once dirname(__FILE__).'/core/ssq_calc_prize.php';

//奖号
$prizeInfo = array(
	'red' => array('1','4','14','18','24','29'),
	'blue'=> '4'
);

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$cp_dayid = '2019086';
$namekey  = $cp_dayid;
$cpwinkey = $cp_dayid.'_WIN';

//redis奖项计数
$prizeKey = array(
	1 => $cp_dayid.'_p1',
	2 => $cp_dayid.'_p2',
	3 => $cp_dayid.'_p3',
	4 => $cp_dayid.'_p4',
	5 => $cp_dayid.'_p5',
	6 => $cp_dayid.'_p6',
);

$t1 = time();

//获取彩票模拟大数据总数
$cpnum = $redis->lSize($namekey);
// $cpnum = 10;

$pernum = 10000 * 10;
$pgnum  = ceil($cpnum / $pernum);

for ($pg=1; $pg <= $pgnum; $pg++) { 
	$s_index = ($pg - 1) * $pernum;
	$e_index = ($pg - 1) * $pernum + $pernum;

	$list = $redis->lRange($namekey, $s_index, $e_index);

	//开始计算
	foreach ($list as $i => $cpinfo) {
		$i = $s_index + $i;
		$txt = $cpinfo;
		$cpinfo = json_decode($cpinfo,true);
		$posttime = $cpinfo['time'];

		$red  = $cpinfo['red'];
		$blue = $cpinfo['blue'];

		$redwin = array_intersect($prizeInfo['red'], $red);
		$redwin = count($redwin);

		// $bluewin = in_array($prizeInfo['blue'], $blue) ? 1 : 0;
		$bluewin = $prizeInfo['blue'] = $blue ? 1 : 0;

		if($redwin >=4 || $bluewin){
			$cpinfo = json_encode($cpinfo);
			$redis->rPush($cpwinkey, $cpinfo);
			// echo $cpinfo;
			// echo "\n\r";
		}

		if(count($red) == 6){
			$redwin     = array_intersect($prizeInfo['red'], $red);
			$redwin     = count($redwin);
			if(count($blue) == 1){
				$bluewin    = $blue[0] == $prizeInfo['blue'] ? 1 : 0;
				$prizelevel = calcPrize($redwin, $bluewin);

				//统计中奖数量
				if(isset($prizeKey[$prizelevel])){
					$prizelevel_key = $prizeKey[$prizelevel];
					$cprow = array();
					$cprow['red']   = $red;
					$cprow['blue']  = $blue;
					$cprow['level'] = $prizelevel;
					$cprow['time']  = $posttime;
					
					$cprow = json_encode($cprow);
					$redis->rPush($prizelevel_key, $cprow);
				}
			}else{
				foreach ($blue as $tblue) {
					$bluewin    = $tblue == $prizeInfo['blue'] ? 1 : 0;
					$prizelevel = calcPrize($redwin, $bluewin);
					
					//统计中奖数量
					if(isset($prizeKey[$prizelevel])){
						$prizelevel_key = $prizeKey[$prizelevel];
						$cprow = array();
						$cprow['red']   = $red;
						$cprow['blue']  = $tblue;
						$cprow['level'] = $prizelevel;
						$cprow['time']  = $posttime;
						
						$cprow = json_encode($cprow);
						$redis->rPush($prizelevel_key, $cprow);
					}
				}
			}
		}else{
			$reds = combination($red, 6);
			if(count($blue) == 1){
				$bluewin = $blue[0] == $prizeInfo['blue'] ? 1 : 0;
				foreach ($reds as $tred) {
					$redwin     = array_intersect($prizeInfo['red'], $tred);
					$redwin     = count($redwin);
					$prizelevel = calcPrize($redwin, $bluewin);

					//统计中奖数量
					if(isset($prizeKey[$prizelevel])){
						$prizelevel_key = $prizeKey[$prizelevel];
						$cprow = array();
						$cprow['red']   = $tred;
						$cprow['blue']  = $blue[0];
						$cprow['level'] = $prizelevel;
						$cprow['time']  = $posttime;
						
						$cprow = json_encode($cprow);
						$redis->rPush($prizelevel_key, $cprow);
					}
				}
			}else{
				foreach ($reds as $tred) {
					foreach ($blue as $tblue) {
						$redwin     = array_intersect($prizeInfo['red'], $tred);
						$redwin     = count($redwin);
						$bluewin    = $tblue == $prizeInfo['blue'] ? 1 : 0;
						$prizelevel = calcPrize($redwin, $bluewin);

						//统计中奖数量
						if(isset($prizeKey[$prizelevel])){
							$prizelevel_key = $prizeKey[$prizelevel];
							$cprow = array();
							$cprow['red']   = $tred;
							$cprow['blue']  = $tblue;
							$cprow['level'] = $prizelevel;
							$cprow['time']  = $posttime;
							
							$cprow = json_encode($cprow);
							$redis->rPush($prizelevel_key, $cprow);
						}
					}
				}
			}
		}
		// echo $prizelevel ? "{$i}" : "-";
	}
}

echo "\n\r\n\r";
echo "------计算结果------";
echo "\n\r\n\r";
echo '中奖纪录数：' . ($redis->exists($cpwinkey) ? $redis->lSize($cpwinkey) : 0);
echo "\n\r";

$t2 = time();

echo "耗时：" . ($t2-$t1) . "秒\n\r";
echo "\n\r";
// exit;

echo "\n\r\n\r";
echo "------计算结果------";
echo "\n\r\n\r";
foreach ($prizeKey as $level => $prizelevel_key) {
	echo $level . '等奖数量：' . ($redis->exists($prizelevel_key) ? $redis->lSize($prizelevel_key) : 0);
	echo "\n\r";
}

$t2 = time();

echo "耗时：" . ($t2-$t1) . "秒\n\r";
echo "\n\r";