<?php

require_once dirname(__FILE__) . '/../../include/config.inc.php';
require_once dirname(__FILE__) . '/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/redindexV2.func.php';
require_once(dirname(__FILE__) . '/../core/choosered.func.php');
require_once(dirname(__FILE__) . '/../core/Lottery.class.php');
require_once(SNRUNNING_ROOT . '/library/excel/ExcelData.php');

if (!isset($token) || $token != md5('72cpwang')) {
	LoginCheck();

	if (!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)) {
		ShowMsg("Permission denied", "-1");
		exit;
	}
}

if ($action == 'getcost') {
	if ($reds && $blues) {
		$reds = explode(',', $reds);
		$reds_zuhe = combination($reds, 6);

		$reds_zuhe = count($reds_zuhe);

		$blues = explode(',', $blues);
		$blues_zuhe = combination($blues, 1);

		$blues_zuhe = count($blues_zuhe);

		$order_num = $blues_zuhe * $reds_zuhe;
		$order_cost = $order_num * 2 * $multiple;

		$rest = array();
		$rest['choose']  = implode(" ", $reds) . '+' . implode(', ', $blues);
		$rest['cost']    = "共：{$order_num}注，费用{$order_cost}元";
		$rest['buycost'] = $order_cost;
		// echo "选号：" . implode(" ", $reds) . '+' . implode(', ', $blues) . "\n";
		// echo "共：{$order_num}注，费用{$order_cost}元";
		echo json_encode($rest);
		exit();
	}
} else if ($action == 'cash_prize') {
	$sql = "SELECT * FROM `#@__caipiao_myorder` WHERE isdo='0' AND buytype='$buytype'";
	$dosql->Execute($sql);

	$total_repay = 0;
	while ($row = $dosql->GetArray()) {
		$cur_ssq = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='" . $row['cp_dayid'] . "'");
		if (!isset($cur_ssq['id'])) {
			continue;
		}

		//开奖号
		$opencode = $cur_ssq['opencode'];

		//下单的红球 篮球
		$myred  = explode(',', $row['red_num']);
		$myblue = explode(',', $row['blue_num']);

		$mychoose = array();
		if (count($myred) > 6) {
			$mychoose = combination($myred, 6);
		} else {
			$mychoose[] = $myred;
		}

		$red_win_list  = array();
		$blue_win_list = array();
		$red_win_num   = 0;
		$blue_win_num  = 0;

		$cur_reds = explode(',', $cur_ssq['red_num']);

		$myRest = array();
		$win_red_list = array();
		foreach ($mychoose as $k => $myred) {
			$red_win_num = 0;
			$red_win_list = array();
			foreach ($myred as $vred) {
				if (in_array($vred, $cur_reds)) {
					$red_win_list[] = $vred;
					$red_win_num++;
					if (!isset($win_red_list[$vred])) {
						$win_red_list[$vred] = 1;
					}
				}
			}
			$myRest[$k]['red_win_list'] = $red_win_list;
			$myRest[$k]['red_win_num'] = $red_win_num;
		}

		$myBlueRest = array();
		$win_blue_list = '';
		foreach ($myblue as $k => $vblue) {
			$blue_win_num = 0;
			$blue_win_list = 0;
			if ($vblue == $cur_ssq['blue_num']) {
				$blue_win_list = $vblue;
				$blue_win_num++;
				$win_blue_list = $vblue;
			}

			$myBlueRest[$k]['blue_win_list'] = $blue_win_list;
			$myBlueRest[$k]['blue_win_num'] = $blue_win_num;
		}

		$repay = 0;
		foreach ($myRest as $key => $myreds) {
			$red_win_num = $myreds['red_win_num'];
			foreach ($myBlueRest as $key2 => $myblues) {
				$blue_win_num = $myblues['blue_win_num'];

				if ($blue_win_num == 1 && $red_win_num < 3) {
					$repay += 5 * $row['multiple'];
				} else if (($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)) {
					$repay += 10 * $row['multiple'];
				} else if (($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)) {
					$repay += 200 * $row['multiple'];
				} else if ($red_win_num == 5 && $blue_win_num == 1) {
					$repay += 3000 * $row['multiple'];
				} else if ($red_win_num == 6 && $blue_win_num == 0) {
					$repay += 100000 * $row['multiple'];
				} else if ($red_win_num == 6 && $blue_win_num == 1) {
					$repay += 5000000 * $row['multiple'];
				}
			}
		}

		$win_red_list = array_keys($win_red_list);
		$red_win_num = count($win_red_list);
		$red_win_list = implode(',', $win_red_list);

		$blue_win_list = $win_blue_list;
		$blue_win_num = $win_blue_list ? 1 : 0;

		$updateSql = "
		UPDATE `#@__caipiao_myorder` 
		SET 
			opencode      ='$opencode', 
			red_win_list  ='$red_win_list', 
			blue_win_list ='$blue_win_list', 
			red_win_num   ='$red_win_num', 
			blue_win_num  ='$blue_win_num', 
			repay         ='$repay', 
			isdo          ='1' 
		WHERE id=" . $row['id'];

		if ($dosql->ExecNoneQuery($updateSql)) {
			$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_payandget` WHERE type='ssq' AND buytype='" . $row['buytype'] . "'");
			$get_num = $row['get_num'] + $repay;
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_payandget` SET get_num='$get_num' WHERE type='ssq' AND buytype='" . $row['buytype'] . "'");
		}
		$total_repay += $repay;
	}
	echo $total_repay;
	exit();
} else if ($action == 'buyssq') {
	$red_num = trim($red_num);
	$blue_num = trim($blue_num);
	$sql = "INSERT INTO `#@__caipiao_myorder` (cp_dayid, red_num, blue_num, multiple, buycost, buytype, posttime) 
	VALUES 
	('" . $cp_dayid . "', '" . $red_num . "', '" . $blue_num . "', '" . $multiple . "', '" . $buycost . "', '" . $buytype . "', '" . time() . "')";


	if ($dosql->ExecNoneQuery($sql)) {
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_payandget` WHERE type='ssq' AND buytype='$buytype'");
		if (!isset($row['pay_num'])) {
			$pay_num = $buycost;
			$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_payandget` (type, buytype, pay_num) VALUES ('ssq', '" . $buytype . "', '" . $pay_num . "')");
		} else {
			$pay_num = $row['pay_num'] + $buycost;
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_payandget` SET pay_num='$pay_num' WHERE type='ssq' AND buytype='$buytype'");
		}
		// ShowMsg("信息添加成功！",'index.php');
		if ($buytype == 2) {

			header("location: /wxpage/myorder2.php");
		} else {

			header("location: /wxpage/myorder.php");
		}
		exit();
	}
} else if ($action == 'download_ssq_index') {
	$excel = new ExcelData();

	$cp_dayid = date("Y") . '001';

	if (isset($num) && $num >= 100) {
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT {$num}");
		while ($row = $dosql->GetArray()) {
			$cp_dayid = $row['cp_dayid'];
		}
	}

	$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>='$cp_dayid' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql, '000');
	$data = array();
	while ($row = $dosql->GetArray('000')) {
		$killtail = RedLocationKill2($row['cp_dayid']);
		$redmiss  = redMissing($row['cp_dayid']);

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']} ORDER BY cp_dayid DESC", "aaa");
		$allSSQ = array();
		while ($rows = $dosql->GetArray('aaa')) {
			$allSSQ[$rows['cp_dayid']]['cp_dayid'] = $rows['cp_dayid'];
			$allSSQ[$rows['cp_dayid']]['red_num'] = explode(",", $rows['red_num']);
			$allSSQ[$rows['cp_dayid']]['blue_num'] = $rows['blue_num'];
		}

		$edgecode = redEdgeCode($row['cp_dayid']);
		$edgecode = $edgecode['redList'];

		$one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']} ORDER BY cp_dayid DESC");
		$before_reds = explode(',', $one['red_num']);

		$reds = explode(",", $row['red_num']);
		$blue = $row['blue_num'] < 10 ? '0' . $row['blue_num'] : $row['blue_num'];

		$ssqindex = array();
		$ssqindex['id'] = $row['cp_dayid'] . '期';
		$ssqindex['ssq'] = str_replace(',', '.', $row['red_num']) . '+' . $blue;
		$tmp_ssqindex = RedIndex22($reds, $redmiss, $killtail, $allSSQ, $blue);

		$ssqindex = array_merge($ssqindex, $tmp_ssqindex);

		$tmp = array();

		$wins = $ssqindex['wins'];
		unset($ssqindex['wins']);
		$ssqindex['win40'] = isset($wins['4+0']) ? $wins['4+0'] : 0;
		$ssqindex['win50'] = isset($wins['5+0']) ? $wins['5+0'] : 0;

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

		$tmp = array_merge($tmp, $ssqindex);

		$data[] = $tmp;
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
		'win40'    => '40W',
		'win50'    => '50W',
		'repeat'    => '直落',
		'edgecode'  => '边码',
		'7guanxima' => '7关系码',
		'6qujianma' => '6区间码',
	);

	$excelConfig = array(
		'filename'  => '双色球奖号指标',
		'sheetname' => '指标系',
		'format'    => 'xlsx',
	);

	$n = 0.65;
	$widthCfg = array(
		'id'       => 8,
		'ssq'      => 16,
		'hotcool'  => 5 * (1 + $n),
		'oddeven'  => 5 * (1 + $n),
		'prime'    => 5 * (1 + $n),
		'bigsmall' => 5 * (1 + $n),
		'redarea'  => 5 * (1 + $n),
		'red012'   => 6 * (1 + $n),
		'tail012'  => 6 * (1 + $n),
		'tailbigs' => 6 * (1 + $n),
		'killtail' => 5 * (1 + $n),
		'ac'       => 5 * (1 + $n),
		'sum'      => 5 * (1 + $n),
		'dvalue'   => 5 * (1 + $n),
		'tailnum'  => 5 * (1 + $n),
		'tailsum'  => 5 * (1 + $n),
		'sumtail'  => 5 * (1 + $n),
		'lianhao'  => 4 * (1 + $n),
		'lhdnum2'  => 4 * (1 + $n),
		'lhdnum3'  => 4 * (1 + $n),
		'lhdnum4'  => 4 * (1 + $n),
		'win40'    => 4 * (1 + $n),
		'win50'    => 4 * (1 + $n),
		'repeat'    => 4 * (1 + $n),
		'edgecode'  => 4 * (1 + $n),
		'7guanxima' => 4 * (1 + $n),
		'6qujianma' => 4 * (1 + $n),
	);

	$excel->export($data, $headConfig, $excelConfig, $widthCfg);
} else if ($action == 'download') {
	set_time_limit(0);
	ini_set('memory_limit', '512M');

	$excel = new ExcelData();

	$allReds = array();
	for ($i = 1; $i <= 33; $i++) {
		$i < 10 && $i = '0' . $i;
		$allReds[] = $i;
	}

	$allReds = array();
	for ($i = 0; $i < 10; $i++) {
		if ($i == 0) {
			$allReds[] = $i + 10;
			$allReds[] = $i + 20;
			$allReds[] = $i + 30;
		} else {
			$allReds[] = '0' . $i;
			$allReds[] = $i + 10;
			$allReds[] = $i + 20;
			if ($i + 30 <= 33) {
				$allReds[] = $i + 30;
			}
		}
	}

	$headerColumn = array();
	$widthCfg     = array();
	$widthCfg['day']          = 8.5;
	$widthCfg['opencode']     = 18;
	$headerColumn['day']      = '期数';
	$headerColumn['opencode'] = '开奖号码';
	foreach ($allReds as $red) {
		$widthCfg[$red]     = 3.75;
		$headerColumn[$red] = $red;
	}

	$cpnum = $cpnum > 100 ? $cpnum : 100;

	if (in_array($datatype, array(3, 4, 5))) {
		$cpnum *= 3;
	} else if ($datatype == 2) {
		$cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 1");
		$maxid = $cur['cp_dayid'] + 1;
		$dayid = $maxid % date("Y");
		$dayids = array();
		for ($i = 2003; $i <= date("Y"); $i++) {
			$dayids[] = $i . $dayid;
		}
	}

	if (isset($dayids)) {
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid IN (" . implode(",", $dayids) . ") ORDER BY cp_dayid DESC");
	} else {
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT {$cpnum}");
	}

	$data = array();
	$datalist = array();
	while ($row = $dosql->GetArray()) {
		$datalist[] = $row;
	}

	$datalist = array_reverse($datalist);
	foreach ($datalist as $row) {
		$curw = date("w", strtotime($row['cp_day']));
		if ($datatype == 3 && $curw != 2) continue;
		if ($datatype == 4 && $curw != 4) continue;
		if ($datatype == 5 && $curw != 0) continue;

		$red_num = explode(',', $row['red_num']);
		$curReds = array();
		$curReds['day'] = $row['cp_dayid'] . '期';
		$curReds['opencode'] = str_replace(',', ' ', $row['opencode']);

		$missData = getCurMiss($row['cp_dayid'] + 1);
		$missData = $missData['red_miss_arr'];

		foreach ($allReds as $red) {
			if (in_array($red, $red_num)) {
				$curReds[$red] = $red . '#1';
			} else {
				$curReds[$red] = $datatype == 1 ? $missData[$red] . '#2' : '-#2';
			}
		}
		$data[] = $curReds;
	}

	$end = end($data);
	$maxday = $end['day'];

	for ($i = 1; $i < 4; $i++) {
		$curReds = array();
		$curReds['day'] = $datatype == 2 ? date("Y") . $dayid . '期' : ($maxday + $i) . '期';
		foreach ($allReds as $red) {
			$curReds[$red] = '';
		}
		$data[] = $curReds;
	}

	$type = array(1 => '最新', 2 => '同期', 3 => '周二', 4 => '周四', 5 => '周日', 6 => '最新无遗漏');

	$excelConfig = array(
		'filename'  => '尾组视图-' . $type[$datatype],
		'sheetname' => '尾组视图-' . $type[$datatype],
		'format'    => 'xlsx',
	);
	$excel->exportTail($data, $headerColumn, $excelConfig, $widthCfg);
} else if ($action == 'pull_ssqinfo') {
	$lotteryNo = isset($lotteryNo) ? $lotteryNo : '';
	$ssq = Lottery::getLotteryRes('ssq', $lotteryNo);

	file_put_contents('ssq.log', json_encode($ssq));
	if ($ssq && $ssq['error_code'] == 0) {
		$result = $ssq['result'];
		
		if(strlen($result['lottery_res'])!=20){
			exit(1);
		}

		$cp_dayid  = '20' . $result['lottery_no'];
		$cp_day    = $result['lottery_date'];
		$red_num   = substr($result['lottery_res'], 0, -3);
		$blue_num  = substr($result['lottery_res'], -2);

		$reds = explode(',', $red_num);
		if (count($reds) != 6) exit(1);
		if (empty($blue_num)) exit(1);

		foreach ($reds as &$v) {
			if (mb_strlen($v) != 2) {
				$v = '0' . $v;
			}
		}
		if (mb_strlen($blue_num) != 2) {
			$blue_num = '0' . $blue_num;
		}
		$red_num = implode(',', $reds);
		$opencode  = $red_num . '+' . $blue_num;
		$red_order = $red_num;

		$exist = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
		if (!isset($exist['id'])) {
			$sql = "INSERT INTO `#@__caipiao_history` (cp_dayid, cp_day, opencode, red_num, blue_num, red_order) 
			VALUES 
			('" . $cp_dayid . "', '" . $cp_day . "', '" . $opencode . "', '" . $red_num . "', '" . $blue_num . "', '" . $red_order . "')";

			$dosql->ExecNoneQuery($sql);
		}

		$touzhu   = $result['lottery_sale_amount'];
		$jiangchi = $result['lottery_pool_amount'];
		$p1       = $result['lottery_prize'][0]['prize_num'];
		$p1_bonus = $result['lottery_prize'][0]['prize_amount'];
		$p2       = $result['lottery_prize'][1]['prize_num'];
		$p2_bonus = $result['lottery_prize'][1]['prize_amount'];
		$p3       = $result['lottery_prize'][2]['prize_num'];
		$p3_bonus = $result['lottery_prize'][2]['prize_amount'];
		$p4       = $result['lottery_prize'][3]['prize_num'];
		$p4_bonus = $result['lottery_prize'][3]['prize_amount'];
		$p5       = $result['lottery_prize'][4]['prize_num'];
		$p5_bonus = $result['lottery_prize'][4]['prize_amount'];
		$p6       = $result['lottery_prize'][5]['prize_num'];
		$p6_bonus = $result['lottery_prize'][5]['prize_amount'];

		$touzhu   = str_replace(",", "", $touzhu);
		$jiangchi = str_replace(",", "", $jiangchi);
		$p1_bonus = str_replace(",", "", $p1_bonus);
		$p2_bonus = str_replace(",", "", $p2_bonus);
		$p3_bonus = str_replace(",", "", $p3_bonus);
		$p4_bonus = str_replace(",", "", $p4_bonus);
		$p5_bonus = str_replace(",", "", $p5_bonus);
		$p6_bonus = str_replace(",", "", $p6_bonus);

		$exist = $dosql->GetOne("SELECT * FROM `#@__caipiao_history_prize` WHERE cp_dayid='$cp_dayid'");

		if (!isset($exist['id']) && is_numeric($touzhu)) {
			$sql = "INSERT INTO `#@__caipiao_history_prize` (cp_dayid, blue_num, touzhu, jiangchi, p1, p1_bonus, p2, p2_bonus, p3, p4, p5, p6) 
			VALUES 
			('" . $cp_dayid . "', '" . $blue_num . "', '" . $touzhu . "', '" . $jiangchi . "', '" . $p1 . "', '" . $p1_bonus . "', '" . $p2 . "', '" . $p2_bonus . "', '" . $p3 . "', '" . $p4 . "', '" . $p5 . "', '" . $p6 . "')";

			$dosql->ExecNoneQuery($sql);
		}
	}
	exit(1);
} else if ($action == 'pull_happy8') {
	$url = "http://e.17500.cn/getData/kl81000.txt";
	// $url = "http://e.17500.cn/getData/kl81000_asc.txt";
	$file = fopen($url, "r");
	if (!$file) {
		projectLog('ssq_pull', $file, '打开远程文件失败！！');
	}
	$ssqHistory = array();
	$lineNum = 0;
	$sqlArr = array();

	$field = array();
	while (!feof($file)) {
		if ($lineNum == 5) {
			break;
		}

		//每读取一行
		$row = fgets($file, 1024);

		$row = str_replace('  ', ' ', $row);
		$row = explode(' ', $row);
		$opencode = array();

		foreach ($row as $key => $value) {
			if ($key >= 2 && $key <= 21) {
				$opencode[] = $value;
			}

			$field['p10_10'] = $p10_10 = $row[24];
			$field['p10_10_num'] = $p10_10_num = $row[25];
			$field['p10_9'] = $p10_9 = $row[26];
			$field['p10_9_num'] = $p10_9_num = $row[27];
			$field['p10_8'] = $p10_8 = $row[28];
			$field['p10_8_num'] = $p10_8_num = $row[29];
			$field['p10_7'] = $p10_7 = $row[30];
			$field['p10_7_num'] = $p10_7_num = $row[31];
			$field['p10_6'] = $p10_6 = $row[32];
			$field['p10_6_num'] = $p10_6_num = $row[33];
			$field['p10_5'] = $p10_5 = $row[34];
			$field['p10_5_num'] = $p10_5_num = $row[35];
			$field['p10_0'] = $p10_0 = $row[36];
			$field['p10_0_num'] = $p10_0_num = $row[37];

			$field['p9_9'] = $p9_9 = $row[38];
			$field['p9_9_num'] = $p9_9_num = $row[39];
			$field['p9_8'] = $p9_8 = $row[40];
			$field['p9_8_num'] = $p9_8_num = $row[41];
			$field['p9_7'] = $p9_7 = $row[42];
			$field['p9_7_num'] = $p9_7_num = $row[43];
			$field['p9_6'] = $p9_6 = $row[44];
			$field['p9_6_num'] = $p9_6_num = $row[45];
			$field['p9_5'] = $p9_5 = $row[46];
			$field['p9_5_num'] = $p9_5_num = $row[47];
			$field['p9_4'] = $p9_4 = $row[48];
			$field['p9_4_num'] = $p9_4_num = $row[49];
			$field['p9_0'] = $p9_0 = $row[50];
			$field['p9_0_num'] = $p9_0_num = $row[51];

			$field['p8_8'] = $p8_8 = $row[52];
			$field['p8_8_num'] = $p8_8_num = $row[53];
			$field['p8_7'] = $p8_7 = $row[54];
			$field['p8_7_num'] = $p8_7_num = $row[55];
			$field['p8_6'] = $p8_6 = $row[56];
			$field['p8_6_num'] = $p8_6_num = $row[57];
			$field['p8_5'] = $p8_5 = $row[58];
			$field['p8_5_num'] = $p8_5_num = $row[59];
			$field['p8_4'] = $p8_4 = $row[60];
			$field['p8_4_num'] = $p8_4_num = $row[61];
			$field['p8_0'] = $p8_0 = $row[62];
			$field['p8_0_num'] = $p8_0_num = $row[63];

			$field['p7_7'] = $p7_7 = $row[64];
			$field['p7_7_num'] = $p7_7_num = $row[65];
			$field['p7_6'] = $p7_6 = $row[66];
			$field['p7_6_num'] = $p7_6_num = $row[67];
			$field['p7_5'] = $p7_5 = $row[68];
			$field['p7_5_num'] = $p7_5_num = $row[69];
			$field['p7_4'] = $p7_4 = $row[70];
			$field['p7_4_num'] = $p7_4_num = $row[71];
			$field['p7_0'] = $p7_0 = $row[72];
			$field['p7_0_num'] = $p7_0_num = $row[73];

			$field['p6_6'] = $p6_6 = $row[74];
			$field['p6_6_num'] = $p6_6_num = $row[75];
			$field['p6_5'] = $p6_5 = $row[76];
			$field['p6_5_num'] = $p6_5_num = $row[77];
			$field['p6_4'] = $p6_4 = $row[78];
			$field['p6_4_num'] = $p6_4_num = $row[79];
			$field['p6_3'] = $p6_3 = $row[80];
			$field['p6_3_num'] = $p6_3_num = $row[81];

			$field['p5_5'] = $p5_5 = $row[82];
			$field['p5_5_num'] = $p5_5_num = $row[83];
			$field['p5_4'] = $p5_4 = $row[84];
			$field['p5_4_num'] = $p5_4_num = $row[85];
			$field['p5_3'] = $p5_3 = $row[86];
			$field['p5_3_num'] = $p5_3_num = $row[87];

			$field['p4_4'] = $p4_4 = $row[88];
			$field['p4_4_num'] = $p4_4_num = $row[89];
			$field['p4_3'] = $p4_3 = $row[90];
			$field['p4_3_num'] = $p4_3_num = $row[91];
			$field['p4_2'] = $p4_2 = $row[92];
			$field['p4_2_num'] = $p4_2_num = $row[93];

			$field['p3_3'] = $p3_3 = $row[94];
			$field['p3_3_num'] = $p3_3_num = $row[95];
			$field['p3_2'] = $p3_2 = $row[96];
			$field['p3_2_num'] = $p3_2_num = $row[97];

			$field['p2_2'] = $p2_2 = $row[98];
			$field['p2_2_num'] = $p2_2_num = $row[99];

			$field['p1_1'] = $p1_1 = $row[100];
			$field['p1_1_num'] = $p1_1_num = $row[101];
		}

		$field['cp_dayid'] = $cp_dayid  = $row[0];
		$field['cp_day'] = $cp_day    = $row[1];
		$field['opencode'] = $opencode  = implode(",", $opencode);

		if (empty($field['cp_dayid'])) {
			continue;
		}

		$field['touzhu'] = $touzhu = $row[22];
		$field['jiangchi'] = $jiangchi = $row[23];

		$exist = $dosql->GetOne("SELECT * FROM `#@__happy8_history` WHERE cp_dayid='$cp_dayid'");
		if (isset($exist['id'])) {
			continue;
		}

		$fields = array_keys($field);
		$fields_val = array_values($field);
		$fields = implode(',', $fields);
		$fields_val = "('" . implode('\',\'', $fields_val) . "')";

		$sql = "INSERT INTO `#@__happy8_history` ({$fields}) VALUES " . $fields_val;
		$dosql->ExecNoneQuery($sql);

		$lineNum++;
	}
	echo '1';
	exit();
} else if ($action == 'pull_ssqprize2') {
	// $file = fopen('http://www.17500.cn/getData/ssq.TXT', "r") or die("打开远程文件失败！！");
	$file = fopen('http://www.17500.cn/getData/ssq_DESC.TXT', "r") or die("打开远程文件失败！！");
	$ssqHistory = array();
	$lineNum = 0;
	$sqlArr = array();
	while (!feof($file)) {
		if ($lineNum == 5) {
			break;
		}

		//每读取一行
		$row = fgets($file, 1024);
		$row = explode(' ', $row);

		$cp_dayid = $row[0];
		$blue_num = $row[8];
		$touzhu   = $row[15];
		$jiangchi = $row[16];
		$p1       = $row[17];
		$p1_bonus = $row[18];
		$p2       = $row[19];
		$p2_bonus = $row[20];
		$p3       = $row[21];
		$p4       = $row[23];
		$p5       = $row[25];
		$p6       = $row[27];

		if (empty($p2) || empty($p3)) {
			continue;
		}

		$exist = $dosql->GetOne("SELECT * FROM `#@__caipiao_history_prize` WHERE cp_dayid='$cp_dayid'");
		if (isset($exist['id'])) {
			continue;
		}

		$sql = "INSERT INTO `#@__caipiao_history_prize` (cp_dayid, blue_num, touzhu, jiangchi, p1, p1_bonus, p2, p2_bonus, p3, p4, p5, p6) 
		VALUES 
		('" . $cp_dayid . "', '" . $blue_num . "', '" . $touzhu . "', '" . $jiangchi . "', '" . $p1 . "', '" . $p1_bonus . "', '" . $p2 . "', '" . $p2_bonus . "', '" . $p3 . "', '" . $p4 . "', '" . $p5 . "', '" . $p6 . "')";

		$dosql->ExecNoneQuery($sql);

		$lineNum++;
	}
	echo '1';
	exit();
}
