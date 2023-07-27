<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__).'/../excel/ExcelData.php';
require_once dirname(__FILE__) . '/../core/redindexV2.func.php';

$excel = new ExcelData();

	$cp_dayid = date("Y") . '001';

	$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>='$cp_dayid' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql, '000');
	$data = array();
	while($row = $dosql->GetArray('000')){
		$killtail = RedLocationKill2($row['cp_dayid']);
		$redmiss  = redMissing($row['cp_dayid']);

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']} ORDER BY cp_dayid DESC", "aaa");
		$allSSQ = array();
		while($rows = $dosql->GetArray('aaa')){
			$allSSQ[$rows['cp_dayid']]['cp_dayid'] = $rows['cp_dayid'];
			$allSSQ[$rows['cp_dayid']]['red_num'] = explode(",", $rows['red_num']);
			$allSSQ[$rows['cp_dayid']]['blue_num'] = $rows['blue_num'];
		}

		$reds = explode(",", $row['red_num']);
		$blue = $row['blue_num'] < 10 ? '0' . $row['blue_num'] : $row['blue_num'];

		$ssqindex = array();
		$ssqindex['id'] = $row['cp_dayid'];
		$ssqindex['ssq'] = str_replace(',', '.', $row['red_num']) . '+' . $blue;
		// $tmp_ssqindex = RedIndex22($reds, $redmiss, $killtail, $allSSQ, $blue);

		// $ssqindex = array_merge($ssqindex, $tmp_ssqindex);

		$tmp = array();

		// $wins = $ssqindex['wins'];
		// unset($ssqindex['wins']);
		// $ssqindex['win40'] = isset($wins['4+0']) ? $wins['4+0'] : 0;
		// $ssqindex['win50'] = isset($wins['5+0']) ? $wins['5+0'] : 0;

		// $lhdata = RedSerialNumber($reds);

		// $ssqindex['lianhao'] = $lhdata[1] == 1 ? 1 : 0;
		// $ssqindex['lhdnum2'] = $lhdata[2] != 0 ? $lhdata[2] : 0;
		// $ssqindex['lhdnum3'] = $lhdata[3] != 0 ? $lhdata[3] : 0;
		// $ssqindex['lhdnum4'] = $lhdata[4] != 0 ? $lhdata[4] : 0;

		$tmp = array_merge($tmp, $ssqindex);

		$data[] = $tmp;
	}
	// print_r($data);die;

	$headConfig = array(
		'id'       => '编号',
		'ssq'      => '双色球号码',
		// 'hotcool'  => '热冷',
		// 'oddeven'  => '奇偶',
		// 'prime'    => '质合',
		// 'bigsmall' => '大小',
		// 'redarea'  => '区间',
		// 'red012'   => '012路',
		// 'tail012'  => '尾012路',
		// 'tailbigs' => '尾大小',
		// 'killtail' => '定位杀',
		// 'ac'       => 'AC值',
		// 'sum'      => '和值',
		// 'dvalue'   => '跨度',
		// 'tailnum'  => '尾数',
		// 'tailsum'  => '尾和',
		// 'sumtail'  => '和尾',
		// 'lianhao'  => '连号',
		// 'lhdnum2'  => '2连',
		// 'lhdnum3'  => '3连',
		// 'lhdnum4'  => '4连',
		// 'win40'    => '40',
		// 'win50'    => '50',
	);

	$excelConfig = array(
		'filename'  => '双色球奖号指标',
		'sheetname' => '指标系',
		'format'    => 'xls',
	);

	$n = 0.65;
	$widthCfg = array(
		'id'       => 8,
		'ssq'      => 16,
		// 'hotcool'  => 5*(1+$n),
		// 'oddeven'  => 5*(1+$n),
		// 'prime'    => 5*(1+$n),
		// 'bigsmall' => 5*(1+$n),
		// 'redarea'  => 5*(1+$n),
		// 'red012'   => 6*(1+$n),
		// 'tail012'  => 6*(1+$n),
		// 'tailbigs' => 6*(1+$n),
		// 'killtail' => 5*(1+$n),
		// 'ac'       => 5*(1+$n),
		// 'sum'      => 5*(1+$n),
		// 'dvalue'   => 5*(1+$n),
		// 'tailnum'  => 5*(1+$n),
		// 'tailsum'  => 5*(1+$n),
		// 'sumtail'  => 5*(1+$n),
		// 'lianhao'  => 4*(1+$n),
		// 'lhdnum2'  => 4*(1+$n),
		// 'lhdnum3'  => 4*(1+$n),
		// 'lhdnum4'  => 4*(1+$n),
		// 'win40'    => 4*(1+$n),
		// 'win50'    => 4*(1+$n),
	);

	$excel->export($data, $headConfig, $excelConfig, $widthCfg);