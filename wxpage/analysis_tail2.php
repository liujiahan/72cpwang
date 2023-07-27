<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');

$excel = new ExcelData();

$tailcount = array();

$tailoffset = array(5, 10, 30, 50, 100);

$cp_dayid = 0;
$maxid = maxDayid();
if(empty($cp_dayid)){
	$cp_dayid = $maxid + 1;
}

$num = 100;
$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT {$num}");
$index = 1;
while($row = $dosql->GetArray()){
	$reds = explode(',', $row['red_num']);

	foreach ($reds as $key => $red) {
		$tail = $red % 10;

		if($index > 50){
			if(!isset($tailcount[100][$tail])){
				$tailcount[100][$tail] = 0;
			}
			$tailcount[100][$tail]++;
		}
		else if($index > 30 && $index <= 50){
			if(!isset($tailcount[50][$tail])){
				$tailcount[50][$tail] = 0;
			}
			$tailcount[50][$tail]++;

			if(!isset($tailcount[100][$tail])){
				$tailcount[100][$tail] = 0;
			}
			$tailcount[100][$tail]++;
		}
		else if($index > 10 && $index <= 30){
			if(!isset($tailcount[30][$tail])){
				$tailcount[30][$tail] = 0;
			}
			$tailcount[30][$tail]++;

			if(!isset($tailcount[50][$tail])){
				$tailcount[50][$tail] = 0;
			}
			$tailcount[50][$tail]++;
			
			if(!isset($tailcount[100][$tail])){
				$tailcount[100][$tail] = 0;
			}
			$tailcount[100][$tail]++;
		}
		else if($index > 5 && $index <= 10){
			if(!isset($tailcount[10][$tail])){
				$tailcount[10][$tail] = 0;
			}
			$tailcount[10][$tail]++;

			if(!isset($tailcount[30][$tail])){
				$tailcount[30][$tail] = 0;
			}
			$tailcount[30][$tail]++;

			if(!isset($tailcount[50][$tail])){
				$tailcount[50][$tail] = 0;
			}
			$tailcount[50][$tail]++;
			
			if(!isset($tailcount[100][$tail])){
				$tailcount[100][$tail] = 0;
			}
			$tailcount[100][$tail]++;
		}
		else if($index <= 5){
			if(!isset($tailcount[5][$tail])){
				$tailcount[5][$tail] = 0;
			}
			$tailcount[5][$tail]++;

			if(!isset($tailcount[10][$tail])){
				$tailcount[10][$tail] = 0;
			}
			$tailcount[10][$tail]++;

			if(!isset($tailcount[30][$tail])){
				$tailcount[30][$tail] = 0;
			}
			$tailcount[30][$tail]++;

			if(!isset($tailcount[50][$tail])){
				$tailcount[50][$tail] = 0;
			}
			$tailcount[50][$tail]++;
			
			if(!isset($tailcount[100][$tail])){
				$tailcount[100][$tail] = 0;
			}
			$tailcount[100][$tail]++;
		}
		
	}
	$index++;
}

foreach ($tailcount as &$tailarr) {
	ksort($tailarr);
}
// ksort($tailcount);
// echo "<pre>";
// print_r($tailcount);

$data = array();

for ($i=0; $i < 10; $i++) { 
	$tmp = array();
	
	$tmp['tail'] = $i . '尾';
	$tmp['5q']   = isset($tailcount[5][$i]) ? $tailcount[5][$i] : 0;
	$tmp['10q']  = isset($tailcount[10][$i]) ? $tailcount[10][$i] : 0;
	$tmp['30q']  = isset($tailcount[30][$i]) ? $tailcount[30][$i] : 0;
	$tmp['50q']  = isset($tailcount[50][$i]) ? $tailcount[50][$i] : 0;
	$tmp['100q'] = isset($tailcount[100][$i]) ? $tailcount[100][$i] : 0;

	$data[] = $tmp;
}

// print_r($data);die;

$headConfig = array(
	'tail' => '尾',
	'5q'   => '5期',
	'10q'  => '10期',
	'30q'  => '30期',
	'50q'  => '50期',
	'100q' => '100期',
);


$excelConfig = array(
	'filename'  => '尾数统计'.$cp_dayid,
	'sheetname' => '截止' . $cp_dayid . '期',
	'format'    => 'xlsx',
);

$widthCfg = array(
	'tail' => 10,
	'5q'   => 10,
	'10q'  => 10,
	'30q'  => 10,
	'50q'  => 10,
	'100q' => 10,
);

$excel->export($data, $headConfig, $excelConfig, $widthCfg);