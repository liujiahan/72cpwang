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

$redssq = array(
	array('06', '10', '11', '20', '29', '32')
);
// 区间杀
$result = RedSectionKill($redssq);
$result = current($result);
echo "<pre>";
print_r($result);die;

$sid = isset($sid) ? $sid : 1;
$data = array();
foreach ($result as $v) {
	if(!$v['sectionHas']) continue;
	$num = $v['sectionNum'];
	if(!isset($data[$num])) $data[$num] = 0;
	$data[$num]++;
}

$sum = array_sum($data);
foreach ($data as $miss => $count) {
	// echo "区间关联码".($miss==1 ? '出' : '断')."号的概率：{$count}/{$sum}  ".(round($count / $sum, 4)*100).'%';
	echo "区间关联码出".($miss)."个号的概率：{$count}/{$sum}  ".(round($count / $sum, 4)*100).'%';
	echo "<br/>";
}