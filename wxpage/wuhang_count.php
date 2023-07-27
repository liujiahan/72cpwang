<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';

$limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 500;
$dosql->Execute("SELECT * FROM `#@__caipiao_blue_wuxing` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa['date'][] = $xdate;
    $suanfa['wuxing'][] = $row['wuxing_key'];
}

// $suanfa['date']   = array_reverse($suanfa['date']);

// $wuxing = $suanfa['wuxing'];
$wuxing = array_reverse($suanfa['wuxing']);
// $suanfa['wuxing'] = array_reverse($suanfa['wuxing']);

echo "<pre>";
// print_r($suanfa);

$data = array();
$arr3 = array_slice($wuxing, -3);
$arr3 = implode("", $arr3);
if(!isset($data[$arr3])){
	$data[$arr3] = array();
}
$arr2 = array_slice($wuxing, -2);
$arr2 = implode("", $arr2);
if(!isset($data[$arr2])){
	$data[$arr2] = array();
}

// print_r($wuxing);

foreach ($wuxing as $k => $v) {
	if(isset($wuxing[$k+1]) && isset($wuxing[$k+2]) && isset($wuxing[$k+3])){
		$index3 = $wuxing[$k].$wuxing[$k+1].$wuxing[$k+2];
		if(isset($data[$index3])){
			if(!isset($data[$index3][$wuxing[$k+3]])){
				$data[$index3][$wuxing[$k+3]] = 0;
			}
			$data[$index3][$wuxing[$k+3]]++;
		}

		$index2 = $wuxing[$k].$wuxing[$k+1];
		if(isset($data[$index2])){
			if(!isset($data[$index2][$wuxing[$k+2]])){
				$data[$index2][$wuxing[$k+2]] = 0;
			}
			$data[$index2][$wuxing[$k+2]]++;
		}

		// echo $index2.'###'.$index3;
		// echo "<br/>";
		// print_r($data);
	}
}
print_r($data);



