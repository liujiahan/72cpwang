<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}

if($action == 'postail'){
	$cpnum = isset($cpnum) ? $cpnum : 10;

	$cp_dayid = '';

	$data = array();
	if(isset($cp_dayid) && !empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT {$cpnum}");
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT {$cpnum}");
	}
	while($row = $dosql->GetArray()){

		$redarr = explode(',', $row['red_num']);

		$red1tail = $redarr[0] % 10;
		$red2tail = $redarr[1] % 10;
		$red3tail = $redarr[2] % 10;
		$red4tail = $redarr[3] % 10;
		$red5tail = $redarr[4] % 10;
		$red6tail = $redarr[5] % 10;

		$red1road = $redarr[0] % 3;
		$red2road = $redarr[1] % 3;
		$red3road = $redarr[2] % 3;
		$red4road = $redarr[3] % 3;
		$red5road = $redarr[4] % 3;
		$red6road = $redarr[5] % 3;

		$tmp = array();
		$tmp['cp_dayid'] = $row['cp_dayid'];
		$tmp['opencode'] = $row['opencode'];

		// $tmp['pos1'] = $redarr[0] . '.' . $redarr[1] . '.' . $redarr[2] . '#' . $red1road . $red2road . $red3road . '#' . $red1tail . $red2tail . $red3tail;
		// $tmp['pos2'] = $redarr[1] . '.' . $redarr[2] . '.' . $redarr[3] . '#' . $red2road . $red3road . $red4road . '#' . $red2tail . $red3tail . $red4tail;
		// $tmp['pos3'] = $redarr[2] . '.' . $redarr[3] . '.' . $redarr[4] . '#' . $red3road . $red4road . $red5road . '#' . $red3tail . $red4tail . $red5tail;
		// $tmp['pos4'] = $redarr[3] . '.' . $redarr[4] . '.' . $redarr[5] . '#' . $red4road . $red5road . $red6road . '#' . $red4tail . $red5tail . $red6tail;
		// $tmp['pos5'] = $redarr[4] . '.' . $redarr[5] . '.' . $redarr[0] . '#' . $red5road . $red6road . $red1road . '#' . $red5tail . $red6tail . $red1tail;
		// $tmp['pos6'] = $redarr[5] . '.' . $redarr[0] . '.' . $redarr[1] . '#' . $red6road . $red1road . $red2road . '#' . $red6tail . $red1tail . $red2tail;

		$tmp['pos1'] = $red1road . $red2road . $red3road . '--' . $red1tail . $red2tail . $red3tail;
		$tmp['pos2'] = $red2road . $red3road . $red4road . '--' . $red2tail . $red3tail . $red4tail;
		$tmp['pos3'] = $red3road . $red4road . $red5road . '--' . $red3tail . $red4tail . $red5tail;
		$tmp['pos4'] = $red4road . $red5road . $red6road . '--' . $red4tail . $red5tail . $red6tail;
		$tmp['pos5'] = $red5road . $red6road . $red1road . '--' . $red5tail . $red6tail . $red1tail;
		$tmp['pos6'] = $red6road . $red1road . $red2road . '--' . $red6tail . $red1tail . $red2tail;

		$data[] = $tmp;
	}
	$data = array_reverse($data);

	$rest = array('table_th'=>'', 'table_td'=>'');


	$rest['table_th'] = '<tr>
	<th style="color: blue;">期号</th>
	<th style="color: red;">开奖号</th>
	<th>123路-尾</th>
	<th>234路-尾</th>
	<th>345路-尾</th>
	<th>456路-尾</th>
	<th>561路-尾</th>
	<th>612路-尾</th>
	';
	$rest['table_th'] .= '</tr>';

	foreach ($data as $v) {
		$rest['table_td'] .= '<tr><td style="color: blue;">'.$v['cp_dayid'].'</td><td style="color: red;">'.$v['opencode'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos1'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos2'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos3'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos4'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos5'].'</td>';
		$rest['table_td'] .= '<td>'.$v['pos6'].'</td>';
	}

	exit(json_encode($rest));
}