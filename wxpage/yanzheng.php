<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

/*
1先判断和值 缩水包围
2判断质数和合数
3把16个数分成2组01245 36789
4看选蓝图（精八码）
选蓝图 质合数 组选 精八码 和值
 */

$allblue = array();
for ($i=1; $i < 17; $i++) { 
	$allblue[] = $i;
}

$blueFW = array();
$total = 0;
$win_total = 0;
$dosql->Execute("SELECT * FROM `lz_caipiao_history` ORDER BY cp_dayid DESC");
while ($row = $dosql->GetArray()) {
	$cp_dayid = $row['cp_dayid'];
	$blue_num = $row['blue_num'];
	// echo $cp_dayid;
	// echo "<br/>";
	// echo $blue_num;
	// echo "<br/>";

	$blues = array();
	$dosql->Execute("SELECT * FROM `lz_caipiao_history` where cp_dayid<{$cp_dayid} ORDER BY cp_dayid DESC LIMIT 4", '4');
	while ($row = $dosql->GetArray('4')) {
		$blues[] = $row['blue_num'];
	}

	$tmpsum = 0;
	$bluesum = array();
	$maybeblue = array();
	foreach ($blues as $k => $bv) {
		$tmpkey = $k.'_'.$bv;
		if(!isset($bluesum[$tmpkey])) $bluesum[$tmpkey] = array();
		foreach ($allblue as $kk => $allbv) {
			$tmpkey2 = $kk + 1;
			if($k == 0){
				if($kk == 0){
					$tmpsum = $bv + $allbv;
					$bluesum[$tmpkey][$tmpkey2] = $tmpsum;
				}else{
					$bluesum[$tmpkey][$tmpkey2] = $tmpsum + $allbv - 1;
				}
			}else{
				if($kk == 0){
					$tmpsum = $bv + $tmpsum;
					$bluesum[$tmpkey][$tmpkey2] = $tmpsum;
				}else{
					$bluesum[$tmpkey][$tmpkey2] = $tmpsum + $allbv - 1;
				}
			}
			if($bluesum[$tmpkey][$tmpkey2] % 10 == 0){
				$maybeblue[] = $tmpkey2;
			}
		}
	}
	$maybeblue = array_unique($maybeblue);
	$blueFW[$cp_dayid] = $maybeblue;
	// print_r($maybeblue);die;
	// sort($maybeblue);
	$win = 0;
	if(in_array($blue_num, $maybeblue)){
		$win = 1;
		$win_total++;
	}
	echo $win;
	$total++;
}
echo PHP_EOL;

echo $total;
echo PHP_EOL;
echo $win_total;
echo PHP_EOL;
echo round($win_total / $total, 4) * 100 . "%";

// echo "<pre>";
// print_r($blueFW);
die;



echo "<pre>";
print_r($maybeblue);
print_r($bluesum);