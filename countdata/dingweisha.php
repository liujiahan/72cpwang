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

// 定位杀
$result = RedLocationKill();

$data = array();
foreach ($result as $v) {
	$num = array_sum($v['killrest']);
	if(!isset($data[$num])) $data[$num] = 0;
	$data[$num]++;
}

$sum = array_sum($data);
foreach ($data as $miss => $count) {
	echo "定位杀杀准{$miss}位概率：{$count}/{$sum}  ".(round($count / $sum, 4)*100).'%';
	echo "<br/>";
}