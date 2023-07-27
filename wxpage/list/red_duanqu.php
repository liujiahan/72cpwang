<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_history` order by cp_dayid asc");
$index = 0;
$allRedList = array();
while($row = $dosql->GetArray()){
    $allRedList[$index]['red_num']  = explode(",", $row['red_num']);
    $allRedList[$index]['blue_num'] = $row['blue_num'];
    $allRedList[$index]['cp_dayid'] = $row['cp_dayid'];
	$index++;
}

$duanqu = array();
foreach ($allRedList as $index => $row) {
	$duanqu[$index] = array();
	$duanqu[$index]['1qu'] = 0;
	$duanqu[$index]['2qu'] = 0;
	$duanqu[$index]['3qu'] = 0;
	$duanqu[$index]['duanqu'] = 0;

	$reds = $row['red_num'];

	foreach ($reds as $red) {
		if($red >= 1 && $red <= 11 && $duanqu[$index]['1qu'] == 0){
			$duanqu[$index]['1qu'] = 1;
		}
		if($red >= 12 && $red <= 22 && $duanqu[$index]['2qu'] == 0){
			$duanqu[$index]['2qu'] = 1;
		}
		if($red >= 23 && $red <= 33 && $duanqu[$index]['3qu'] == 0){
			$duanqu[$index]['3qu'] = 1;
		}
	}

	$duanqu[$index]['duanqu'] = 3 - array_sum($duanqu[$index]);
}

$d0qu = 0;
$d1qu = 0;
$d2qu = 0;

$dq_1 = 0;
$dq_2 = 0;
$dq_3 = 0;

$dq_total = 0;
$dq_str = '';
$dq_str1 = '';
$dq_str2 = '';
$dq_str3 = '';
foreach ($duanqu as $index => $row) {
	$row['1qu'] == 0 && $dq_1++;
	$row['2qu'] == 0 && $dq_2++;
	$row['3qu'] == 0 && $dq_3++;

	$row['duanqu'] == 0 && $d0qu++;
	$row['duanqu'] == 1 && $d1qu++;
	$row['duanqu'] == 2 && $d2qu++;

	if($row['duanqu']){
		$dq_total++;
		$dq_str .= '1';
	}else{
		$dq_str .= '0';
	}

	if($row['3qu'] == 0){
		$dq_str2 .= '1';
	}else{
		$dq_str2 .= '0';
	}
}
$arr = explode('1', $dq_str2);
// $arr = array_unique($arr);
$len = array();
foreach ($arr as $key => $str) {
	// if($str == '')
	// 	continue;

	$len[] = strlen($str);
}
arsort($len);
print_r($len);die;

$arr = explode('1', $dq_str);
// $arr = array_unique($arr);
$len = array();
foreach ($arr as $key => $str) {
	if($str == '')
		continue;

	$len[] = strlen($str);
}
arsort($len);
print_r($len);die;
echo $dq_str;
die;
echo $total = count($duanqu);
echo "<br/>";
echo $dq_total;
echo "<br/>";
echo '1区断区次数'.$dq_1.' 占比：' . round($dq_1 / $dq_total, 4) * 100 . '%' . '占比：' . round($dq_1 / $total, 4) * 100 . '%';
echo "<br/>";
echo '2区断区次数'.$dq_2.' 占比：' . round($dq_2 / $dq_total, 4) * 100 . '%' . '占比：' . round($dq_2 / $total, 4) * 100 . '%';
echo "<br/>";
echo '3区断区次数'.$dq_3.' 占比：' . round($dq_3 / $dq_total, 4) * 100 . '%' . '占比：' . round($dq_3 / $total, 4) * 100 . '%';
echo "<br/>";
die;


$d0qu = 0;
$d1qu = 0;
$d2qu = 0;
foreach ($duanqu as $index => $row) {
	$row['duanqu'] == 0 && $d0qu++;
	$row['duanqu'] == 1 && $d1qu++;
	$row['duanqu'] == 2 && $d2qu++;
}

echo $total = count($duanqu);
echo "<br/>";
echo '断0区'.$d0qu.'次 占比：' . round($d0qu / $total, 4) * 100 . '%';
echo "<br/>";
echo '断1区'.$d1qu.'次 占比：' . round($d1qu / $total, 4) * 100 . '%';
echo "<br/>";
echo '断2区'.$d2qu.'次 占比：' . round($d2qu / $total, 4) * 100 . '%';
echo "<br/>";
die;


echo "<br/>";
echo $d0qu;
echo "<br/>";
echo $d1qu;
echo "<br/>";
echo $d2qu;
die;

echo "<pre>";
print_r($duanqu);die;
echo "</pre>";