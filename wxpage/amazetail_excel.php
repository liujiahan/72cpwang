<?php

require_once(dirname(__FILE__) . '/../include/config.inc.php');
require_once(dirname(__FILE__) . '/core/suanfa.func.php');
require_once(dirname(__FILE__) . '/core/choosered.func.php');
require_once(dirname(__FILE__) . '/core/redindexV2.func.php');
require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');

if(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey){
    ShowMsg("Permission denied","-1");
    exit;
}

set_time_limit(0);
ini_set('memory_limit', '512M');

$excel = new ExcelData();

$tbname = '#@__caipiao_tail_cpdata';

$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_tail_500w` WHERE id='$id'");
$cp_dayid = $max['cp_dayid'];

$edgecode = redEdgeCode($cp_dayid);
$edgecode = $edgecode['redList'];

$one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC");
$before_reds = explode(',', $one['red_num']);

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

	$ssqindex['edgecode'] = count(array_intersect($reds, $edgecode));
	$ssqindex['repeat'] = count(array_intersect($reds, $before_reds));

	//连号个数
	$lhdata = RedSerialNumber($reds);
	$ssqindex['lianhao'] = $lhdata[2];
	//连号组数及维度
	$lhdata2 = RedSerialNumberV2($reds);

	$ssqindex['lhdnum2'] = isset($lhdata2[2]) ? $lhdata2[2] : 0;
	$ssqindex['lhdnum3'] = isset($lhdata2[3]) ? $lhdata2[3] : 0;
	$ssqindex['lhdnum4'] = isset($lhdata2[4]) ? $lhdata2[4] : 0;

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
	'repeat'    => '直落',
	'edgecode'  => '边码',
	'7guanxima' => '7关系码',
	'6qujianma' => '6区间码',
);

$excelConfig = array(
	'filename'  => '魔幻尾数#'.$cp_dayid,
	'sheetname' => '欲顺欲發',
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
	'repeat'    => 4*(1+$n),
	'edgecode'  => 4*(1+$n),
	'7guanxima' => 4*(1+$n),
	'6qujianma' => 4*(1+$n),
);

$excel->export($data, $headConfig, $excelConfig, $widthCfg);