<?php

function ssqUpdateCfg()
{
	$urlarr = array(
		'pull_ssqinfo' => array(
			'name'   => '双色球拉取',
			'url'    => 'ajax/ssq_do.php?action=pull_ssqinfo&token=' . md5('72cpwang'),
			'params' => array('action' => 'pull_ssqinfo'),
		),
		'red_coolhot' => array(
			'name'   => '计算热温冷号',
			'url'    => 'ajax/red_suanfa_do.php?action=red_coolhot&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_coolhot'),
		),
		'red_3code' => array(
			'name'   => '计算3码组合',
			'url'    => 'ajax/red_suanfa_do.php?action=red_3code&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_3code'),
		),
		'red_wuxing' => array(
			'name'   => '计算五行关系',
			'url'    => 'ajax/red_suanfa_do.php?action=red_wuxing&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_wuxing'),
		),
		'red_pinlv_fenqu' => array(
			'name'   => '计算高中低频率',
			'url'    => 'ajax/red_suanfa_do.php?action=red_pinlv_fenqu&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_pinlv_fenqu'),
		),
		'red_space_periods' => array(
			'name'   => '计算间隔期数',
			'url'    => 'ajax/red_suanfa_do.php?action=red_space_periods&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_space_periods'),
		),
		'red_location_cross' => array(
			'name'   => '计算三区出号',
			'url'    => 'ajax/red_suanfa_do.php?action=red_location_cross&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_location_cross'),
		),
		'red_tail' => array(
			'name'   => '计算红球尾数',
			'url'    => 'ajax/red_suanfa_do.php?action=red_tail&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_tail'),
		),
		'red_tail_coolhot' => array(
			'name'   => '计算热温冷尾数',
			'url'    => 'ajax/red_suanfa_do.php?action=red_tail_coolhot&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_tail_coolhot'),
		),
		'red_edgecode' => array(
			'name'   => '计算边码红球',
			'url'    => 'ajax/red_suanfa_do.php?action=red_edgecode&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_edgecode'),
		),
		'red_sum_divisor' => array(
			'name'   => '计算和值除数定胆红球',
			'url'    => 'ajax/red_suanfa_do.php?action=red_sum_divisor&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_sum_divisor'),
		),
		'offset_red_percent' => array(
			'name'   => '计算百分比红球',
			'url'    => 'ajax/red_offset_do.php?action=offset_red_percent&token=' . md5('72cpwang'),
			'params' => array('action' => 'offset_red_percent'),
		),
		'red_baihe' => array(
			'name'   => '计算百合红球',
			'url'    => 'ajax/red_suanfa_do.php?action=red_baihe&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_baihe'),
		),
		'blue_xsh' => array(
			'name'   => '计算蓝号心水号',
			'url'    => 'ajax/blue_suanfa_do.php?action=blue_xsh&token=' . md5('72cpwang'),
			'params' => array('action' => 'blue_xsh'),
		),
		'blue_choose' => array(
			'name'   => '计算蓝号正主选号',
			'url'    => 'ajax/blue_suanfa_do.php?action=blue_choose&token=' . md5('72cpwang'),
			'params' => array('action' => 'blue_choose'),
		),
		'blue_wuxing' => array(
			'name'   => '计算蓝号五行',
			'url'    => 'ajax/blue_suanfa_do.php?action=blue_wuxing&token=' . md5('72cpwang'),
			'params' => array('action' => 'blue_wuxing'),
		),
		'red_gold' => array(
			'name'   => '计算红蓝黄金定点',
			'url'    => 'ajax/red_suanfa_do.php?action=red_gold&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_gold'),
		),
		'kill_blue' => array(
			'name'   => '计算蓝号八杀法',
			'url'    => 'ajax/blue_suanfa_do.php?action=kill_blue&token=' . md5('72cpwang'),
			'params' => array('action' => 'kill_blue'),
		),
		'new_kill_blue2' => array(
			'name'   => '计算蓝号11招绝密杀蓝',
			'url'    => 'ajax/blue_suanfa_do.php?action=new_kill_blue2&token=' . md5('72cpwang'),
			'params' => array('action' => 'new_kill_blue2'),
		),
		'wuhang_kill' => array(
			'name'   => '计算五行杀蓝',
			'url'    => 'ajax/blue_suanfa_do.php?action=wuhang_kill&token=' . md5('72cpwang'),
			'params' => array('action' => 'wuhang_kill'),
		),
		'pull_happy8' => array(
			'name'   => '快乐8拉取',
			'url'    => 'ajax/ssq_do.php?action=pull_happy8&token=' . md5('72cpwang'),
			'params' => array('action' => 'pull_happy8'),
		),
		'red_coolhot_happy8' => array(
			'name'   => '快乐8遗漏',
			'url'    => 'ajax/red_suanfa_do.php?action=red_coolhot_happy8&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_coolhot_happy8'),
		),
		'chart3code' => array(
			'name'   => '3码趋势',
			'url'    => 'ajax/red_suanfa_do.php?action=chart3code&token=' . md5('72cpwang'),
			'params' => array('action' => 'chart3code'),
		),
		// 'pull_ssqprize' => array(
		//   'name'   => '拉取双色球500万中奖情况',
		//   'url'    => 'ajax/ssq_do.php?action=pull_ssqprize&token='.md5('72cpwang'),
		//   'params' => array('action'=>'pull_ssqprize'),
		// ),
		'blue_3d' => array(
			'name'   => '计算蓝号3D数据',
			'url'    => 'ajax/blue_suanfa_do.php?action=blue_3d&token=' . md5('72cpwang'),
			'params' => array('action' => 'blue_3d'),
		),
		'red_weer' => array(
			'name'   => 'weer算法',
			'url'    => 'ajax/red_weer_do.php?action=red_weer&token=' . md5('72cpwang'),
			'params' => array('action' => 'red_weer'),
		),
	);
	return $urlarr;
}


function maxDayid()
{
	global $dosql;

	$row = $dosql->GetOne("SELECT max(cp_dayid) as cp_dayid FROM `#@__caipiao_history`");
	return $row['cp_dayid'];
}

function getDaySel($limit = 30)
{
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit");
	$data = array();
	while ($row = $dosql->GetArray()) {
		$data[$row['cp_dayid']] = $row['cp_dayid'] . '期';
	}

	return $data;
}

function getAvgMiss()
{
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot`");
	$missarr = array();
	while ($row = $dosql->GetArray()) {
		$missarr[$row['cp_dayid']] = unserialize($row['win_miss']);
	}

	// echo count($missarr);die;

	$daynums = count($missarr);
	$totalmiss = 0;
	$lt4totalmiss = 0;
	foreach ($missarr as $cp_dayid => $win_miss) {
		foreach ($win_miss as $red => $miss_num) {
			if ($miss_num <= 4) {
				$lt4totalmiss += $miss_num;
			}
			$totalmiss += $miss_num;
		}
	}

	$miss_total_avg = round($totalmiss / $daynums, 1);
	$miss_lt4_avg = round($lt4totalmiss / $daynums, 1);
	return array('miss_avg' => $miss_total_avg, 'lt4_miss_avg' => $miss_lt4_avg);
}

function getRowColumn()
{
	$reds = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$reds[] = $i;
	}
	// $oneReds   = array_slice($reds, 0, 11);
	// $twoReds   = array_slice($reds, 11, 11);
	// $threeReds = array_slice($reds, 22);

	$rowarr = array();
	$colarr = array();

	$rcNum = 6;

	//组合6行数据
	for ($row = 1; $row <= $rcNum; $row++) {
		$rowarr[$row] = array_slice($reds, ($row - 1) * $rcNum, $rcNum);
	}

	//组合6列数据
	for ($col = 1; $col <= $rcNum; $col++) {
		foreach ($reds as $key => $red) {
			if ($red % 6 == 0 && $col == 6) {
				if (!isset($colarr[$col])) {
					$colarr[$col] = array();
				}
				$colarr[$col][] = $red;
			} else if ($red % 6 == $col) {
				if (!isset($colarr[$col])) {
					$colarr[$col] = array();
				}
				$colarr[$col][] = $red;
			}
		}
	}

	return array('row' => $rowarr, 'col' => $colarr);
}

function getWinRowCol($reds, $rowColumn)
{
	$winrow = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
	foreach ($rowColumn['row'] as $rownum => $rowreds) {
		foreach ($reds as $red) {
			if (in_array($red, $rowreds)) {
				$winrow[$rownum]++;
			}
		}
	}

	$wincolumn = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
	foreach ($rowColumn['col'] as $columnnum => $columnreds) {
		foreach ($reds as $red) {
			if (in_array($red, $columnreds)) {
				$wincolumn[$columnnum]++;
			}
		}
	}

	return array('row' => $winrow, 'col' => $wincolumn);
}

function nextCpDayId($cp_dayid)
{
	// if(date('m') == '01' && (date("d") == '01' || date("d") == '02')){
	// 	return date("Y") . '001';
	// }
	$time = time(); //时间
	$now = date("w", $time); //获取今天的周几
	$now = $now == 0 ? 7 : $now; //修正周日
	$nextyear1 = date("Y", $time - ($now - 2) * 86400); //得到周二
	$nextyear2 = date("Y", $time - ($now - 4) * 86400); //得到周二
	$nextyear3 = date("Y", $time - ($now - 7) * 86400); //得到周二

	$nextid = $cp_dayid + 1;
	// if (false === strpos($cp_dayid, "'" . $nextyear1 . "'")) {
	// 	$nextid = $nextyear1 . '001';
	// } else if (false === strpos($cp_dayid, "'" . $nextyear2 . "'")) {
	// 	$nextid = $nextyear2 . '001';
	// } else if (false === strpos($cp_dayid, "'" . $nextyear3 . "'")) {
	// 	$nextid = $nextyear3 . '001';
	// }
	return $nextid;
}

function getSelArr()
{
	global $adminkey;

	$selArr = array(
		'20'   => '20期',
		'30'   => '30期',
		'40'   => '40期',
		'50'   => '50期',
	);
	if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) {
		$selArr['80']   = '80期';
		$selArr['100']  = '100期';
		$selArr['150']  = '150期';
		$selArr['200']  = '200期';
		$selArr['300']  = '300期';
		$selArr['500']  = '500期';
		$selArr['1000'] = '1000期';
	}
	return $selArr;
}

function getSelItem($itemid = '')
{
	$selArr = array(
		'blue'      => '蓝球',
		'bigsmall'  => '大小比',
		'oddeven'   => '奇偶比',
		'redarea'   => '区间比',
		'primenum'  => '质合比',
		'hotcool'   => '遗漏冷热',
		'misssum'   => '遗漏和',
		'sum'       => '和数值',
		'ac'        => 'AC值',
		'repeat'    => '重号',
		'tailnum'   => '尾数和',
		'tailgroup' => '尾数组数',
		'tail4_3'   => '43尾球比',
	);
	return empty($itemid) ? $selArr : $selArr[$itemid];
}

function itemsTable($itemid)
{
	$tables = array(
		'blue' => array(
			'01', '02', '03', '04',
			'05', '06', '07', '08',
			'09', '10', '11', '12',
			'13', '14', '15', '16'
		),
		'bigsmall' => array(
			'0:6', '1:5', '2:4', '3:3', '4:2', '5:1', '6:0'
		),
		'oddeven' => array(
			'0:6', '1:5', '2:4', '3:3', '4:2', '5:1', '6:0'
		),
		'primenum' => array(
			'0:6', '1:5', '2:4', '3:3', '4:2', '5:1', '6:0'
		),
		'tail4_3' => array(
			'0:6', '1:5', '2:4', '3:3', '4:2', '5:1', '6:0'
		),
		'sum' => array(
			'21-49', '50-59', '60-69', '70-79', '80-89', '90-99', '100-109',
			'110-119', '120-129', '130-139', '140-183',
		),
		'tailnum' => array(
			'3-15', '16-20', '21-25', '26-30', '31-35', '36-40', '41-51'
		),
		'tailgroup' => array(
			'2', '3', '4', '5', '6'
		),
		'misssum' => array(
			'0-10', '11-15', '16-20', '21-25', '26-30', '31-35', '36-40', '41-45', '46-50', '51-55', '56-60', '61-100'
		),
		'hotcool' => array(
			'0:4:2', '1:5:0', '1:4:1', '1:3:2', '1:2:3', '1:1:4', '2:0:4',
			'2:1:3', '2:2:2', '2:3:1', '2:4:0', '3:0:3', '3:1:2', '3:2:1',
			'3:3:0', '4:0:2', '4:1:1', '4:2:0', '5:0:1', '5:1:0', '6:0:0'
		),
		'ac' => array(
			'4', '5', '6', '7', '8', '9', '10'
		),
		'repeat' => array(
			'0', '1', '2', '3', '4', '5', '6'
		),
		'redarea' => array(
			'006', '015', '024', '033', '042', '051', '060',
			'105', '114', '123', '132', '141', '150', '204',
			'213', '222', '231', '240', '303', '312', '321',
			'330', '402', '411', '420', '501', '510', '600',
		),
	);



	return $tables[$itemid];
}

function indexScore()
{
	$scoreCfg = array(
		'bigsmall' => array(
			'3:3' => 2,
			'4:2' => 2,
			'2:4' => 2,
			'5:1' => 1,
			'1:5' => 1,
			'6:0' => 0,
			'0:6' => 0,
		),
		'oddeven' => array(
			'3:3' => 2,
			'4:2' => 2,
			'2:4' => 2,
			'5:1' => 1,
			'1:5' => 1,
			'6:0' => 0,
			'0:6' => 0,
		),
		'redarea' => array(
			'2:2:2' => 2,
			'1:2:3' => 2,
			'3:1:2' => 2,
			'1:3:2' => 2,
			'3:2:1' => 2,
			'2:3:1' => 2,
			'2:1:3' => 2,
			'1:4:1' => 1,
			'4:1:1' => 1,
			'1:1:4' => 1,
			'3:3:0' => 1,
			'3:0:3' => 1,
			'0:3:3' => 1,
			'0:4:2' => 1,
			'2:4:0' => 1,
			'4:2:0' => 1,
			'0:2:4' => 1,
			'2:0:4' => 1,
			'4:0:2' => 1,
			'0:5:1' => 0,
			'1:5:0' => 0,
			'5:0:1' => 0,
			'5:1:0' => 0,
			'1:0:5' => 0,
			'0:1:5' => 0,
			'0:6:0' => 0,
			'0:0:6' => 0,
			'6:0:0' => 0,
		),
		'cool_hot' => array(
			'4:1:1' => 2,
			'5:1:0' => 2,
			'4:2:0' => 2,
			'3:2:1' => 2,
			'5:0:1' => 2,
			'3:1:2' => 2,
			'3:3:0' => 2,
			'6:0:0' => 2,
			'4:0:2' => 2,
			'2:2:2' => 1,
			'2:3:1' => 1,
			'2:4:0' => 1,
			'3:0:3' => 1,
			'1:3:2' => 1,
			'2:1:3' => 1,
			'1:4:1' => 0,
			'1:1:4' => 0,
			'1:5:0' => 0,
			'1:2:3' => 0,
			'2:0:4' => 0,
			'0:4:2' => 0,
		),
		'primenum' => array(
			'2:4' => 2,
			'3:3' => 2,
			'1:5' => 2,
			'4:2' => 1,
			'0:6' => 1,
			'5:1' => 0,
			'6:0' => 0,
		),
		'tail4_3' => array(
			'2:4' => 2,
			'3:3' => 2,
			'1:5' => 2,
			'4:2' => 1,
			'0:6' => 1,
			'5:1' => 0,
			'6:0' => 0,
		),
	);
	return $scoreCfg;
}

function indexScorebak()
{
	$scoreCfg = array(
		'bigsmall' => array(
			'3:3' => 753,
			'4:2' => 543,
			'2:4' => 472,
			'5:1' => 182,
			'1:5' => 139,
			'6:0' => 15,
			'0:6' => 12,
		),
		'oddeven' => array(
			'3:3' => 741,
			'4:2' => 519,
			'2:4' => 489,
			'5:1' => 171,
			'1:5' => 143,
			'6:0' => 29,
			'0:6' => 24,
		),
		'redarea' => array(
			'2:2:2' => 320,
			'1:2:3' => 202,
			'3:1:2' => 199,
			'1:3:2' => 195,
			'3:2:1' => 193,
			'2:3:1' => 180,
			'2:1:3' => 178,
			'1:4:1' => 82,
			'4:1:1' => 79,
			'1:1:4' => 67,
			'3:3:0' => 63,
			'3:0:3' => 55,
			'0:3:3' => 46,
			'0:4:2' => 40,
			'2:4:0' => 38,
			'4:2:0' => 35,
			'0:2:4' => 29,
			'2:0:4' => 29,
			'4:0:2' => 29,
			'0:5:1' => 12,
			'1:5:0' => 11,
			'5:0:1' => 11,
			'5:1:0' => 8,
			'1:0:5' => 7,
			'0:1:5' => 5,
			'0:6:0' => 1,
			'0:0:6' => 1,
			'6:0:0' => 1,
		),
		'miss' => array(
			'4:1:1' => 345,
			'5:1:0' => 300,
			'4:2:0' => 283,
			'3:2:1' => 257,
			'5:0:1' => 161,
			'3:1:2' => 159,
			'3:3:0' => 140,
			'6:0:0' => 113,
			'4:0:2' => 103,
			'2:2:2' => 72,
			'2:3:1' => 63,
			'2:4:0' => 34,
			'3:0:3' => 25,
			'1:3:2' => 16,
			'2:1:3' => 15,
			'1:4:1' => 11,
			'1:1:4' => 7,
			'1:5:0' => 4,
			'1:2:3' => 4,
			'2:0:4' => 2,
			'0:4:2' => 2,
		),
		'prime' => array(
			'2:4' => 712,
			'3:3' => 577,
			'1:5' => 472,
			'4:2' => 202,
			'0:6' => 116,
			'5:1' => 36,
			'6:0' => 1,
		),
		'tail43' => array(
			'2:4' => 734,
			'3:3' => 552,
			'1:5' => 489,
			'4:2' => 203,
			'0:6' => 108,
			'5:1' => 29,
			'6:0' => 1,
		),
	);
}
