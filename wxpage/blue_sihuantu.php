<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/core.func.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';

$bluedata = array(
	'01' => array(2,3,5,7,9,10,1),
	'02' => array(1,4,6,8,9,10,2),
	'03' => array(1,4,5,7,11,12,3),
	'04' => array(2,3,6,8,11,12,4),
	'05' => array(1,3,6,7,13,14,5),
	'06' => array(2,4,5,8,13,14,6),
	'07' => array(1,3,5,8,15,16,7),
	'08' => array(2,4,6,7,15,16,8),
	'09' => array(1,2,10,11,13,15,9),
	'10' => array(1,2,9,12,14,16,10),
	'11' => array(3,4,9,12,13,15,11),
	'12' => array(3,4,10,11,14,16,12),
	'13' => array(5,6,9,11,14,15,13),
	'14' => array(5,6,10,12,13,16,14),
	'15' => array(7,8,9,11,13,16,15),
	'16' => array(7,8,10,12,14,15,16),
);

$allBlue = array();
$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
while($row = $dosql->GetArray()){
	$allBlue[] = $row['blue_num'];
}
// var_dump($allBlue);die;

$winRest = array();
foreach ($allBlue as $key => $blue) {
	if($key == 0) continue;

	$upblue = $allBlue[$key-1];
	$upblue = $upblue < 10 ? '0' . $upblue : $upblue;

	$bluearr = $bluedata[$upblue];

	$is_win = 0;
	!in_array($blue, $bluearr) && $is_win = 1;

	$winRest[] = $is_win;
}

echo count($winRest);
echo "<br/>";
$allwin = array_sum($winRest);
echo round($allwin / count($winRest), 4) * 100 . '%';
echo "<br/>";
echo implode('', $winRest);die;

