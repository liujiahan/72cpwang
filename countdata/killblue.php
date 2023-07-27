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

$killblue = array(
	0 => array('05', '09', '12'),
	1 => array('03', '07', '14'),
	2 => array('08', '11', '13'),
	3 => array('09', '14', '16'),
	4 => array('02', '07', '08', '15'),
	5 => array('01', '03', '06', '10'),
	6 => array('01', '04', '13'),
	7 => array('04', '10', '16'),
	8 => array('02', '10', '11'),
	9 => array('05', '06', '12', '15'),
);


$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
// $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC limit 1");

$index = 1;
$allIDS = array();
$allRed = array();
$allBlu = array();
while($row = $dosql->GetArray()){
	$allIDS[$index] = $row['cp_dayid'];
	$allRed[$index] = explode(",", $row['red_num']);
	$allBlu[$index] = $row['blue_num'];

	$index++;
}

$rest = array();
foreach ($allBlu as $index => $blue_num) {
	if($index == 1) continue;

	if($allBlu[$index-1]){
		$tail = $allBlu[$index-1] % 10;
		$killrow = $killblue[$tail];

		$killok = in_array($blue_num, $killrow) ? 0 : 1;
		$rest[] = $killok;
	}
}

$oknum = array_sum($rest);

echo round($oknum / count($rest), 4) * 100 . "%";
die;