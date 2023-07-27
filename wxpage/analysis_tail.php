<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');

$excel = new ExcelData();

$cp_dayid = 0;

if(isset($dtype)){
	if($dtype == 3 || $dtype == 4){
		$rest = count_tailattr($cp_dayid, $dtype == 3 ? 1 : 2);
		$cp_dayid = $rest['cp_dayid'];
		$data     = $rest['data'];
	}else {
		$rest = count_tail($cp_dayid);
		$cp_dayid = $rest['cp_dayid'];
		$data     = $rest['data'];
		$perdata  = $rest['perdata'];
	}
	//导出尾数100期统计
	$dtype == 1 && export_tail($data, $cp_dayid);
	//导出尾数占比100期统计
	$dtype == 2 && export_tailper($perdata, $cp_dayid);
	//导出尾数属性100期统计
	$dtype == 3 && export_tailattr($data, $cp_dayid);
	$dtype == 4 && export_tailattr($data, $cp_dayid);
}

//尾数属性导表
function export_tailattr($data, $cp_dayid){
	global $excel;

	$headConfig = array(
		'tail' => '尾属性',
		'100q' => '100期',
		'50q'  => '50期',
		'30q'  => '30期',
		'10q'  => '10期',
		'5q'   => '5期',
	);

	$excelConfig = array(
		'filename'  => '尾数属性统计'.$cp_dayid,
		'sheetname' => '截止' . $cp_dayid . '期',
		'format'    => 'xlsx',
	);

	$widthCfg = array(
		'tail' => 10,
		'100q' => 10,
		'50q'  => 10,
		'30q'  => 10,
		'10q'  => 10,
		'5q'   => 10,
	);

	$excel->export($data, $headConfig, $excelConfig, $widthCfg);
}

//尾数占比导表
function export_tailper($data, $cp_dayid){
	global $excel;

	$headConfig = array(
		'tail' => '尾',
		// 'per1' => '30期在100期占比',
		'per2' => '10期在100期占比',
		'per3' => '10期在50期占比',
		'per4' => '10期在30期占比',
		// 'per5' => '5期在30期占比',
		'per6' => '5期在10期占比',
	);


	$excelConfig = array(
		'filename'  => '尾数占比统计'.$cp_dayid,
		'sheetname' => '截止' . $cp_dayid . '期',
		'format'    => 'xlsx',
	);

	$widthCfg = array(
		'tail' => 10,
		'per1' => 10,
		'per2' => 10,
		'per3' => 10,
		'per4' => 10,
		'per5' => 10,
		'per6' => 10,
	);

	$excel->export($data, $headConfig, $excelConfig, $widthCfg);
}

//尾数导表
function export_tail($data, $cp_dayid){
	global $excel;

	$headConfig = array(
		'tail' => '尾',
		'100q' => '100期',
		'50q'  => '50期',
		'30q'  => '30期',
		'10q'  => '10期',
		'5q'   => '5期',
	);


	$excelConfig = array(
		'filename'  => '尾数统计'.$cp_dayid,
		'sheetname' => '截止' . $cp_dayid . '期',
		'format'    => 'xlsx',
	);

	$widthCfg = array(
		'tail' => 10,
		'100q' => 10,
		'50q'  => 10,
		'30q'  => 10,
		'10q'  => 10,
		'5q'   => 10,
	);

	$excel->export($data, $headConfig, $excelConfig, $widthCfg);
}

//统计尾数出现次数
function count_tail($cp_dayid=0){
	global $dosql;

	$tailcount = array();

	$tailoffset = array(5, 10, 30, 50, 100);

	foreach ($tailoffset as $id) {
		for ($tail=0; $tail < 10; $tail++) { 
			if(!isset($tailcount[$id][$tail])) $tailcount[$id][$tail] = 0;
		}
	}

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

			$tailcount[100][$tail]++;
			$index <= 50 && $tailcount[50][$tail]++;
			$index <= 30 && $tailcount[30][$tail]++;
			$index <= 10 && $tailcount[10][$tail]++;
			$index <= 5 && $tailcount[5][$tail]++;
		}
		$index++;
	}

	foreach ($tailcount as &$tailarr) {
		ksort($tailarr);
	}

	$data = array();

	for ($i=0; $i < 10; $i++) { 
		$tmp = array();
		
		$tmp['tail'] = $i . '尾';
		$tmp['100q'] = isset($tailcount[100][$i]) ? $tailcount[100][$i] : 0;
		$tmp['50q']  = isset($tailcount[50][$i]) ? $tailcount[50][$i] : 0;
		$tmp['30q']  = isset($tailcount[30][$i]) ? $tailcount[30][$i] : 0;
		$tmp['10q']  = isset($tailcount[10][$i]) ? $tailcount[10][$i] : 0;
		$tmp['5q']   = isset($tailcount[5][$i]) ? $tailcount[5][$i] : 0;

		$data[] = $tmp;
	}

	$perdata = array();
	foreach ($data as $tail => $v) {
		$tmp = array();

		$tmp['tail'] = $v['tail'];
		// $tmp['per1'] = round($v['30q']/$v['100q'],2);
		$tmp['per2'] = round($v['10q']/$v['100q'],2);
		$tmp['per3'] = round($v['10q']/$v['50q'],2);
		$tmp['per4'] = round($v['10q']/$v['30q'],2);
		// $tmp['per5'] = round($v['5q']/$v['30q'],2);
		$tmp['per6'] = round($v['5q']/$v['10q'],2);

		$perdata[] = $tmp;
	}

	return array('cp_dayid'=>$cp_dayid, 'data'=>$data, 'perdata'=>$perdata);
}

//统计尾数的大小，奇偶
function count_tailattr($cp_dayid=0, $attrtype = 1){
	global $dosql;

	$tailcount = array();

	$tailoffset = array(5, 10, 30, 50, 100);

	foreach ($tailoffset as $i) {
		if(!isset($tailcount[$i]['big'])) $tailcount[$i]['big'] = 0;
		if(!isset($tailcount[$i]['small'])) $tailcount[$i]['small'] = 0;
		if(!isset($tailcount[$i]['odd'])) $tailcount[$i]['odd'] = 0;
		if(!isset($tailcount[$i]['even'])) $tailcount[$i]['even'] = 0;
		if(!isset($tailcount[$i]['prime'])) $tailcount[$i]['prime'] = 0;
		if(!isset($tailcount[$i]['compnum'])) $tailcount[$i]['compnum'] = 0;
		if(!isset($tailcount[$i]['0road'])) $tailcount[$i]['0road'] = 0;
		if(!isset($tailcount[$i]['1road'])) $tailcount[$i]['1road'] = 0;
		if(!isset($tailcount[$i]['2road'])) $tailcount[$i]['2road'] = 0;
		if(!isset($tailcount[$i]['metal'])) $tailcount[$i]['metal'] = 0;
		if(!isset($tailcount[$i]['wood'])) $tailcount[$i]['wood'] = 0;
		if(!isset($tailcount[$i]['water'])) $tailcount[$i]['water'] = 0;
		if(!isset($tailcount[$i]['fire'])) $tailcount[$i]['fire'] = 0;
		if(!isset($tailcount[$i]['earth'])) $tailcount[$i]['earth'] = 0;

		if(!isset($tailcount[$i]['tail09'])) $tailcount[$i]['tail09'] = 0;
		if(!isset($tailcount[$i]['tail18'])) $tailcount[$i]['tail18'] = 0;
		if(!isset($tailcount[$i]['tail27'])) $tailcount[$i]['tail27'] = 0;
		if(!isset($tailcount[$i]['tail36'])) $tailcount[$i]['tail36'] = 0;
		if(!isset($tailcount[$i]['tail45'])) $tailcount[$i]['tail45'] = 0;
	}

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

			if($tail >= 5) {
				$tailcount[100]['big']++;
				$index <= 50 && $tailcount[50]['big']++;
				$index <= 30 && $tailcount[30]['big']++;
				$index <= 10 && $tailcount[10]['big']++;
				$index <= 5 && $tailcount[5]['big']++;
			}
			else {
				$tailcount[100]['small']++;
				$index <= 50 && $tailcount[50]['small']++;
				$index <= 30 && $tailcount[30]['small']++;
				$index <= 10 && $tailcount[10]['small']++;
				$index <= 5 && $tailcount[5]['small']++;
			}

			if($tail % 2 == 1) {
				$tailcount[100]['odd']++;
				$index <= 50 && $tailcount[50]['odd']++;
				$index <= 30 && $tailcount[30]['odd']++;
				$index <= 10 && $tailcount[10]['odd']++;
				$index <= 5 && $tailcount[5]['odd']++;
			}
			else {
				$tailcount[100]['even']++;
				$index <= 50 && $tailcount[50]['even']++;
				$index <= 30 && $tailcount[30]['even']++;
				$index <= 10 && $tailcount[10]['even']++;
				$index <= 5 && $tailcount[5]['even']++;
			}

			if(in_array($tail, array(1,2,3,5,7))) {
				$tailcount[100]['prime']++;
				$index <= 50 && $tailcount[50]['prime']++;
				$index <= 30 && $tailcount[30]['prime']++;
				$index <= 10 && $tailcount[10]['prime']++;
				$index <= 5 && $tailcount[5]['prime']++;
			}
			else {
				$tailcount[100]['compnum']++;
				$index <= 50 && $tailcount[50]['compnum']++;
				$index <= 30 && $tailcount[30]['compnum']++;
				$index <= 10 && $tailcount[10]['compnum']++;
				$index <= 5 && $tailcount[5]['compnum']++;
			}

			if($tail % 3 == 0) {
				$tailcount[100]['0road']++;
				$index <= 50 && $tailcount[50]['0road']++;
				$index <= 30 && $tailcount[30]['0road']++;
				$index <= 10 && $tailcount[10]['0road']++;
				$index <= 5 && $tailcount[5]['0road']++;
			}
			else if($tail % 3 == 1) {
				$tailcount[100]['1road']++;
				$index <= 50 && $tailcount[50]['1road']++;
				$index <= 30 && $tailcount[30]['1road']++;
				$index <= 10 && $tailcount[10]['1road']++;
				$index <= 5 && $tailcount[5]['1road']++;
			}
			else if($tail % 3 == 2) {
				$tailcount[100]['2road']++;
				$index <= 50 && $tailcount[50]['2road']++;
				$index <= 30 && $tailcount[30]['2road']++;
				$index <= 10 && $tailcount[10]['2road']++;
				$index <= 5 && $tailcount[5]['2road']++;
			}

			if(in_array($tail, array(4,9))) {
				$tailcount[100]['metal']++;
				$index <= 50 && $tailcount[50]['metal']++;
				$index <= 30 && $tailcount[30]['metal']++;
				$index <= 10 && $tailcount[10]['metal']++;
				$index <= 5 && $tailcount[5]['metal']++;
			}
			else if(in_array($tail, array(3,8))) {
				$tailcount[100]['wood']++;
				$index <= 50 && $tailcount[50]['wood']++;
				$index <= 30 && $tailcount[30]['wood']++;
				$index <= 10 && $tailcount[10]['wood']++;
				$index <= 5 && $tailcount[5]['wood']++;
			}
			else if(in_array($tail, array(1,6))) {
				$tailcount[100]['water']++;
				$index <= 50 && $tailcount[50]['water']++;
				$index <= 30 && $tailcount[30]['water']++;
				$index <= 10 && $tailcount[10]['water']++;
				$index <= 5 && $tailcount[5]['water']++;
			}
			else if(in_array($tail, array(2,7))) {
				$tailcount[100]['fire']++;
				$index <= 50 && $tailcount[50]['fire']++;
				$index <= 30 && $tailcount[30]['fire']++;
				$index <= 10 && $tailcount[10]['fire']++;
				$index <= 5 && $tailcount[5]['fire']++;
			}
			else if(in_array($tail, array(0,5))) {
				$tailcount[100]['earth']++;
				$index <= 50 && $tailcount[50]['earth']++;
				$index <= 30 && $tailcount[30]['earth']++;
				$index <= 10 && $tailcount[10]['earth']++;
				$index <= 5 && $tailcount[5]['earth']++;
			}

			if(in_array($tail, array(0,9))) {
				$tailcount[100]['tail09']++;
				$index <= 50 && $tailcount[50]['tail09']++;
				$index <= 30 && $tailcount[30]['tail09']++;
				$index <= 10 && $tailcount[10]['tail09']++;
				$index <= 5 && $tailcount[5]['tail09']++;
			}
			else if(in_array($tail, array(1,8))) {
				$tailcount[100]['tail18']++;
				$index <= 50 && $tailcount[50]['tail18']++;
				$index <= 30 && $tailcount[30]['tail18']++;
				$index <= 10 && $tailcount[10]['tail18']++;
				$index <= 5 && $tailcount[5]['tail18']++;
			}
			else if(in_array($tail, array(2,7))) {
				$tailcount[100]['tail27']++;
				$index <= 50 && $tailcount[50]['tail27']++;
				$index <= 30 && $tailcount[30]['tail27']++;
				$index <= 10 && $tailcount[10]['tail27']++;
				$index <= 5 && $tailcount[5]['tail27']++;
			}
			else if(in_array($tail, array(3,6))) {
				$tailcount[100]['tail36']++;
				$index <= 50 && $tailcount[50]['tail36']++;
				$index <= 30 && $tailcount[30]['tail36']++;
				$index <= 10 && $tailcount[10]['tail36']++;
				$index <= 5 && $tailcount[5]['tail36']++;
			}
			else if(in_array($tail, array(4,5))) {
				$tailcount[100]['tail45']++;
				$index <= 50 && $tailcount[50]['tail45']++;
				$index <= 30 && $tailcount[30]['tail45']++;
				$index <= 10 && $tailcount[10]['tail45']++;
				$index <= 5 && $tailcount[5]['tail45']++;
			}
		}
		$index++;
	}

	foreach ($tailcount as &$tailarr) {
		ksort($tailarr);
	}

	$data = array();

	$attr1 = array(
		'big'     => '大尾',
		'small'   => '小尾',
		'odd'     => '奇数尾',
		'even'    => '偶数尾',
		'prime'   => '质数尾',
		'compnum' => '合数尾',
		'0road'   => '0路尾',
		'1road'   => '1路尾',
		'2road'   => '2路尾',
	);

	$attr2 = array(
		'metal'  => '金49',
		'wood'   => '木38',
		'water'  => '水16',
		'fire'   => '火27',
		'earth'  => '土05',
		'tail09' => '尾09',
		'tail18' => '尾18',
		'tail27' => '尾27',
		'tail36' => '尾36',
		'tail45' => '尾45',
	);

	$attr = $attrtype == 1 ? $attr1 : $attr2;

	$attrindex = array_keys($attr);
	foreach ($attrindex as $i) {
		$tmp = array();
		
		$tmp['tail'] = $attr[$i];
		$tmp['100q'] = isset($tailcount[100][$i]) ? $tailcount[100][$i] : 0;
		$tmp['50q']  = isset($tailcount[50][$i]) ? $tailcount[50][$i] : 0;
		$tmp['30q']  = isset($tailcount[30][$i]) ? $tailcount[30][$i] : 0;
		$tmp['10q']  = isset($tailcount[10][$i]) ? $tailcount[10][$i] : 0;
		$tmp['5q']   = isset($tailcount[5][$i]) ? $tailcount[5][$i] : 0;

		$data[] = $tmp;
	}

	return array('cp_dayid'=>$cp_dayid, 'data'=>$data);
}