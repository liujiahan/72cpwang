<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/core.func.php';
require_once dirname(__FILE__).'/../wxpage/core/wuxing.func.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';
require_once dirname(__FILE__).'/../wxpage/core/wuxing.func.php';
require_once dirname(__FILE__).'/../wxpage/core/fourMagic.func.php';
require_once dirname(__FILE__).'/../wxpage/core/suanfa.func.php';
require_once dirname(__FILE__).'/../wxpage/core/redindexV2.func.php';
require_once dirname(__FILE__).'/../wxpage/core/weer.func.php';

$wuxing = array(
	array(4,9),
	array(3,8),
	array(1,6),
	array(2,7),
	array(5,0)
);

$wuhang_total = array();
$wuhang_cpday = array();
$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
while($row = $dosql->GetArray()){
	$wuhang_tail = 0;
	$red_num = explode(',',$row['red_num']);
	$tails = array();
	foreach ($red_num as $red) {
		$tail = $red % 10;
		if(!in_array($tail, $tails)){
			$tails[] = $tail;
		}
	}
	$red_num = implode("_", $tails);

	if(false !== strpos($red_num, '4') && false !== strpos($red_num, '9')){
		$wuhang_tail++;
	}
	if(false !== strpos($red_num, '3') && false !== strpos($red_num, '8')){
		$wuhang_tail++;
	}
	if(false !== strpos($red_num, '1') && false !== strpos($red_num, '6')){
		$wuhang_tail++;
	}
	if(false !== strpos($red_num, '2') && false !== strpos($red_num, '7')){
		$wuhang_tail++;
	}
	if(false !== strpos($red_num, '5') && false !== strpos($red_num, '0')){
		$wuhang_tail++;
	}

	if(!isset($wuhang_total[$wuhang_tail])){
		$wuhang_total[$wuhang_tail] = 0;
		$wuhang_cpday[$wuhang_tail] = array();
	}
	$wuhang_total[$wuhang_tail]++;
	$wuhang_cpday[$wuhang_tail][] = $row['cp_dayid'];
}

echo "<pre>";
print_r($wuhang_total);
print_r($wuhang_cpday[3]);
die;