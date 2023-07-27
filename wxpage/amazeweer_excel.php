<?php

require_once(dirname(__FILE__) . '/../include/config.inc.php');
require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');
require_once(dirname(__FILE__) . '/core/suanfa.func.php');

if(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey){
    ShowMsg("Permission denied","-1");
    exit;
}

$excel = new ExcelData();

$tbname = '#@__caipiao_weermy_cpdata';

$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` WHERE id='$id'");
$cp_dayid = $max['cp_dayid'];

$sql = "SELECT * FROM `$tbname` WHERE sid='$id' ORDER BY id ASC";
$dosql->Execute($sql);
$data = array();
$index = 1;
while($row = $dosql->GetArray()){
	$tmp = array();

	$tmp['id'] = $index;
	$tmp['ssq'] = trim($row['ssq']);
	$ssqindex   = unserialize($row['ssqindex']);

	$wins = $ssqindex['wins'];
	unset($ssqindex['wins']);
	// $ssqindex['win31'] = isset($wins['3+1']) ? $wins['3+1'] : 0;
	$ssqindex['win40'] = isset($wins['4+0']) ? $wins['4+0'] : 0;
	// $ssqindex['win41'] = isset($wins['4+1']) ? $wins['4+1'] : 0;
	$ssqindex['win50'] = isset($wins['5+0']) ? $wins['5+0'] : 0;


	$reds = explode('.', explode('+', $tmp['ssq'])[0]);

	$lhdata = RedSerialNumber($reds);

	$ssqindex['lianhao'] = $lhdata[1] == 1 ? 1 : 0;
	$ssqindex['lhdnum2'] = $lhdata[2] != 0 ? $lhdata[2] : 0;
	$ssqindex['lhdnum3'] = $lhdata[3] != 0 ? $lhdata[3] : 0;
	$ssqindex['lhdnum4'] = $lhdata[4] != 0 ? $lhdata[4] : 0;

	$tmp        = array_merge($tmp, $ssqindex);

	$data[] = $tmp;
	$index++;
}

$headConfig = array(
	'id'       => '编号',
	'ssq'      => '双色球号码',
	'hotcool'  => '热冷',
	'oddeven'  => '奇偶',
	'prime'    => '质合',
	'bigsmall' => '大小',
	'redarea'  => '区间',
	'red012'   => '012路',
	'tail012'  => '尾012路',
	'tailbigs' => '尾大小',
	'killtail' => '定位杀',
	'ac'       => 'AC值',
	'sum'      => '和值',
	'dvalue'   => '跨度',
	'tailnum'  => '尾数',
	'tailsum'  => '尾和',
	'sumtail'  => '和尾',
	'lianhao'  => '连号',
	'lhdnum2'  => '2连',
	'lhdnum3'  => '3连',
	'lhdnum4'  => '4连',
	// 'win31'    => '3+1',
	'win40'    => '40中',
	// 'win41'    => '4+1',
	'win50'    => '50中',
);

$excelConfig = array(
	'filename'  => '百合算法#'.$cp_dayid,
	'sheetname' => '五百万666',
	'format'    => 'xlsx',
);

$n = 0.65;
$widthCfg = array(
	'id'       => 8,
	'ssq'      => 16,
	'hotcool'  => 5*(1+$n),
	'oddeven'  => 5*(1+$n),
	'prime'    => 5*(1+$n),
	'bigsmall' => 5*(1+$n),
	'redarea'  => 5*(1+$n),
	'red012'   => 6*(1+$n),
	'tail012'  => 6*(1+$n),
	'tailbigs' => 6*(1+$n),
	'killtail' => 5*(1+$n),
	'ac'       => 5*(1+$n),
	'sum'      => 5*(1+$n),
	'dvalue'   => 5*(1+$n),
	'tailnum'  => 5*(1+$n),
	'tailsum'  => 5*(1+$n),
	'sumtail'  => 5*(1+$n),
	'lianhao'  => 4*(1+$n),
	'lhdnum2'  => 4*(1+$n),
	'lhdnum3'  => 4*(1+$n),
	'lhdnum4'  => 4*(1+$n),
	// 'win31'    => 4*(1+$n),
	'win40'    => 4*(1+$n),
	// 'win41'    => 4*(1+$n),
	'win50'    => 4*(1+$n),
);

$excel->export($data, $headConfig, $excelConfig, $widthCfg);