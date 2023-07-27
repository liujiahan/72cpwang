<?php

require_once dirname(__FILE__) . '/suanfa.func.php';

/**
 * 百分比预测
 * @return [type] [description]
 */
function redPercent()
{
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 10");
	while ($row = $dosql->GetArray()) {
		$redBalls10[] = explode(',', $row['red_num']);
	}
	$redBalls05 = array_slice($redBalls10, 0, 5);

	$reds10 = array();
	$reds05 = array();
	foreach ($redBalls10 as $reds) {
		foreach ($reds as $red) {
			if (!isset($reds10[$red])) {
				$reds10[$red] = 0;
			}
			$reds10[$red]++;
		}
	}

	foreach ($redBalls05 as $reds) {
		foreach ($reds as $red) {
			if (!isset($reds05[$red])) {
				$reds05[$red] = 0;
			}
			$reds05[$red]++;
		}
	}

	$tuice_reds = array();
	foreach ($reds10 as $red => $num) {
		if ($num < 2) {
			continue;
		}
		if (isset($reds05[$red]) && $reds05[$red] > 0) {
			$tuice_reds[] = $red;
		}
	}
	sort($tuice_reds);

	return $tuice_reds;
}

/**
 * 和值除数定胆法
 * @param  string $cp_dayid [description]
 * @return [type]           [description]
 */
function redSumDivisor($cp_dayid = '')
{
	global $dosql;

	if (!empty($cp_dayid)) {
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	} else {
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	}

	//开奖红球数组
	$red_num = explode(',', $row['red_num']);
	$opencode = $row['opencode'];
	//红球之和
	$red_sum = 0;
	foreach ($red_num as $redv) {
		$red_sum += $redv;
	}
	//加减乘除结果数组
	$red_new_list = array();
	$next_redlist = array();

	foreach ($red_num as $redk => $redv) {
		$red_new_list[$redk + 1] = array();
		$red_new_list[$redk + 1]['red'] = $redv;
		$red_new_list[$redk + 1]['list'] = array();

		$tmp_num = (intval(($red_sum - $redv) / $redv)) % 10;
		$tmp_num > 0 && $tmp_num < 10 && $tmp_num = '0' . $tmp_num;
		if ($tmp_num == 0) {
			foreach (array(10, 20, 30) as $num) {
				array_push($red_new_list[$redk + 1]['list'], $num);
				if (!isset($next_redlist[$num])) {
					$next_redlist[$num] = 0;
				}
				$next_redlist[$num]++;
			}
		} else {
			foreach (array($tmp_num, $tmp_num + 10, $tmp_num + 20, $tmp_num + 30) as $k => $num) {
				if ($k == 3) {
					if ($num <= 33) {
						array_push($red_new_list[$redk + 1]['list'], $num);
						if (!isset($next_redlist[$num])) {
							$next_redlist[$num] = 0;
						}
						$next_redlist[$num]++;
					}
				} else {
					array_push($red_new_list[$redk + 1]['list'], $num);
					if (!isset($next_redlist[$num])) {
						$next_redlist[$num] = 0;
					}
					$next_redlist[$num]++;
				}
			}
		}
	}

	$next_redlist = array_keys($next_redlist);
	sort($next_redlist);

	//猜中的红球
	/*$get_red_list = array();
	if(isset($next['red_num'])){
	    $next_red_num = explode(',', $next['red_num']);
	    foreach ($next_redlist as $yc_red) {
	        if(in_array($yc_red, $next_red_num)){
	            $get_red_list[] = $yc_red;
	        }
	    }
	}
	sort($get_red_list);*/

	$get_red_pinlv = array();
	foreach ($red_new_list as $k => $v) {
		foreach ($v['list'] as $num) {
			if (!isset($get_red_pinlv[$num])) {
				$get_red_pinlv[$num] = 0;
			}
			$get_red_pinlv[$num]++;
		}
	}
	// $get_red_num = count($get_red_list);
	// $get_red_list = implode(',', $get_red_list);
	ksort($get_red_pinlv);

	$get_red_pinlv = array_keys($get_red_pinlv);
	return $get_red_pinlv;
}

/**
 * 黄金点位推测法
 * @return [type] [description]
 */
function redGold()
{
	global $dosql;

	$before_5red  = array();
	$before_5blue = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 5");
	while ($row = $dosql->GetArray()) {
		$red_arr = explode(',', $row['red_num']);
		foreach ($red_arr as $key => $red_v) {
			$index = $key + 1;
			if (!isset($before_5red[$index])) {
				$before_5red[$index] = array();
			}
			$before_5red[$index][] = $red_v;
		}
		$before_5blue[] = $row['blue_num'];
	}
	//预测红球数
	$yuce_red = array();
	foreach ($before_5red as $redball) {
		$redball_sum = array_sum($redball);
		$redball_avg = intval($redball_sum / 5);
		$yuce_red[] = $redball_avg - 1 < 10 ? '0' . ($redball_avg - 1) : $redball_avg - 1;
		$yuce_red[] = $redball_avg + 1 < 10 ? '0' . ($redball_avg + 1) : $redball_avg + 1;
	}
	$yuce_red = array_unique($yuce_red);

	//预测蓝球数
	$yuce_blue    = array();
	$blueball_sum = array_sum($before_5blue);
	$blueball_avg = intval($blueball_sum / 5);

	$yuce_blue[]  = $blueball_avg;
	$yuce_blue[]  = $blueball_avg - 1 < 10 ? '0' . ($blueball_avg - 1) : $blueball_avg - 1;
	$yuce_blue[]  = $blueball_avg - 2 < 10 ? '0' . ($blueball_avg - 2) : $blueball_avg - 2;
	$yuce_blue[]  = $blueball_avg + 1 < 10 ? '0' . ($blueball_avg + 1) : $blueball_avg + 1;
	$yuce_blue[]  = $blueball_avg + 2 < 10 ? '0' . ($blueball_avg + 2) : $blueball_avg + 2;

	return $yuce_red;
}

/**
 * 热温冷预测法
 * @return [type] [description]
 */
function redHotWarmCool()
{
	$missArr = array('hot' => array(), 'warm' => array(), 'cool' => array());
	$all_red_miss = redMissing();
	foreach ($all_red_miss as $tmp_red => $tmp_miss) {
		if ($tmp_miss >= 0 && $tmp_miss <= 4) {
			$missArr['hot'][$tmp_red] = $tmp_miss;
		} else if ($tmp_miss >= 5 && $tmp_miss <= 9) {
			$missArr['warm'][$tmp_red] = $tmp_miss;
		} else if ($tmp_miss > 9) {
			$missArr['cool'][$tmp_red] = $tmp_miss;
		}
	}

	return $missArr;
}

/**
 * 高中低频预测法
 * @return [type] [description]
 */
function redPinLv()
{
	global $dosql;

	$redBall = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 30");
	while ($row = $dosql->GetArray()) {
		$red_num = explode(',', $row['red_num']);
		foreach ($red_num as $tmp_red) {
			if (!isset($redBall[$tmp_red])) {
				$redBall[$tmp_red] = 0;
			}
			$redBall[$tmp_red]++;
		}
	}
	//如何区分高频 中频 低频
	arsort($redBall);
	$highpl = max($redBall);
	$midpl = round($highpl / 2);
	$lowpl = round($midpl / 2);

	$ballpl = array('highpl' => array(), 'midpl' => array(), 'lowpl' => array());
	foreach ($redBall as $tmp_red => $pinlv) {
		if ($pinlv > $midpl && $pinlv <= $highpl) {
			$ballpl['highpl'][$tmp_red] = $pinlv;
		} else if ($pinlv > $lowpl && $pinlv <= $midpl) {
			$ballpl['midpl'][$tmp_red] = $pinlv;
		} else if ($pinlv <= $lowpl) {
			$ballpl['lowpl'][$tmp_red] = $pinlv;
		}
	}
	ksort($ballpl['highpl']);
	ksort($ballpl['midpl']);
	ksort($ballpl['lowpl']);

	return $ballpl;
}

/**
 * 尾数球预测法
 * @param  string  $cp_dayid    [description]
 * @param  integer $before_days [description]
 * @return [type]               [description]
 */
function redTailNum($cp_dayid = '', $before_days = 5)
{
	global $dosql;

	$weishu = array();
	$wsarrs = array();
	for ($i = 0; $i < 10; $i++) {
		$wsarrs[$i] = array();
		$weishu[$i] = 0;
	}

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $before_days";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $before_days";
	}

	$dosql->Execute($sql);
	$day_reds = array();
	while ($row = $dosql->GetArray()) {
		$red_num  = explode(',', $row['red_num']);
		foreach ($red_num as $tmp_red) {
			$tmp_ws = $tmp_red % 10;
			$weishu[$tmp_ws]++;
			if (!in_array($tmp_red, $wsarrs[$tmp_ws])) {
				$wsarrs[$tmp_ws][] = $tmp_red;
			}
		}
	}
	asort($weishu);

	$win_reds = array();
	$winlist = array();
	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid=$cp_dayid";
		$win_reds = $dosql->GetOne($sql);
		$win_reds = explode(',', $win_reds['red_num']);

		foreach ($win_reds as $red) {
			$tail = $red % 10;
			if (!isset($winlist[$tail])) {
				$winlist[$tail] = array();
				$winlist[$tail]['hot'] = array();
				$winlist[$tail]['cool'] = array();
			}

			if (in_array($red, $wsarrs[$tail])) {
				$winlist[$tail]['hot'][] = $red;
			} else {
				$winlist[$tail]['cool'][] = $red;
			}
		}
	}

	return array('weishu' => $weishu, 'winlist' => $winlist);
}

function blueXSH($redlist5)
{

	$bluesum = 0;
	foreach ($redlist5 as $key => $v) {
		$bluesum += $v['blue_num'];
	}
	$blueavg = intval($bluesum / 5);

	$blue1 = $blueavg - 5 > 0 ? $blueavg - 5 : 0;
	$blue2 = $blueavg + 5 < 17 ? $blueavg + 5 : 16;

	$blue1 == 0 && $blue1 = 1;

	$blue_range2 = array($blue1, $blue2);
	$blue_range = implode(',', $blue_range2);

	$red_num = explode(',', $redlist5[0]['red_num']);
	$red_num = array_reverse($red_num);
	$diffArr = array();
	foreach ($red_num as $k1 => $red1) {
		foreach ($red_num as $k2 => $red2) {
			if ($k2 > $k1) {
				$val = abs($red1 - $red2);
				if ($val <= 16) {
					$diffArr[] =  $val;
				}
			}
		}
	}
	$diffArr = array_unique($diffArr);
	sort($diffArr);

	$blue_jj = array();
	foreach ($diffArr as $blue) {
		if ($blue >= $blue_range2[0] && $blue <= $blue_range2[1]) {
			$blue_jj[] = $blue;
		}
	}

	$beixuan_1 = array();
	$beixuan_2 = array();
	$beixuan_3 = array();
	$beixuan_4 = array();
	for ($i = $blue_range2[0]; $i <= $blue_range2[1]; $i++) {
		if (!in_array($i, $diffArr)) {
			array_push($beixuan_1, $i);
		} else {
			array_push($beixuan_2, $i);
		}
	}

	for ($i = 1; $i <= 16; $i++) {
		if ($i < $blue_range2[0] || $i > $blue_range2[1]) {
			if (in_array($i, $diffArr)) {
				array_push($beixuan_3, $i);
			} else {
				array_push($beixuan_4, $i);
			}
		}
	}

	$blue_list = implode(',', $diffArr);

	$result = array();
	$result['cp_dayid']   = $redlist5[0]['cp_dayid'];
	$result['blue_range'] = $blue_range;
	$result['blue_list']  = $blue_list;
	$result['blue_jj']    = implode(',', $blue_jj);
	$result['beixuan_1']  = implode(',', $beixuan_1);
	$result['beixuan_2']  = implode(',', $beixuan_2);
	$result['beixuan_3']  = implode(',', $beixuan_3);
	$result['beixuan_4']  = implode(',', $beixuan_4);

	return $result;
}

function nextBaiHe()
{
	global $dosql;

	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");

	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[] = $i;
	}

	$data_percent_reds = redPercent();
	$data_sum_reds = redSumDivisor();

	//百合并集
	$merge_data = array_merge($data_percent_reds, $data_sum_reds);
	$merge_data = array_unique($merge_data);
	sort($merge_data);

	//33-百合余下的红球
	$other_reds = array_diff($allRed, $merge_data);

	//百合交集
	$jiaoji_reds = array_intersect($data_percent_reds, $data_sum_reds);

	//百【减和】
	$percent_reds = array_diff($data_percent_reds, $data_sum_reds);

	//和【减百】
	$sum_reds = array_diff($data_sum_reds, $data_percent_reds);

	return array(
		'cp_dayid'          => $row['cp_dayid'] + 1,
		'data_percent_reds' => $data_percent_reds,
		'data_sum_reds'     => $data_sum_reds,
		'merge_data'        => $merge_data,
		'other_reds'        => $other_reds,
		'jiaoji_reds'       => $jiaoji_reds,
		'percent_reds'      => $percent_reds,
		'sum_reds'          => $sum_reds,
	);
}

function BaiHeAnalysis($cp_dayid = '')
{
	global $dosql;

	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[] = $i;
	}

	if (!empty($cp_dayid)) {
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	} else {
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	}

	$red_num = explode(',', $row['red_num']);
	$data_percent = $dosql->GetOne("SELECT * FROM `#@__caipiao_red_percent` WHERE cp_dayid='{$row['cp_dayid']}'");
	$data_percent_reds = explode(',', $data_percent['tuice_reds']);

	$pre_row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']} ORDER BY cp_dayid DESC");
	$pre_id = $pre_row['cp_dayid'];

	$data_sum = $dosql->GetOne("SELECT * FROM `#@__caipiao_sfone` WHERE cp_dayid='{$pre_id}'");
	$data_sum_reds = unserialize($data_sum['get_red_pinlv']);
	$data_sum_reds = array_keys($data_sum_reds);

	//百合并集
	$merge_data = array_merge($data_percent_reds, $data_sum_reds);
	$merge_data = array_unique($merge_data);
	$merge_data_win = 0;
	foreach ($merge_data as $tmp_red) {
		if (in_array($tmp_red, $red_num)) {
			$merge_data_win++;
		}
	}

	//33-百合余下的红球
	$other_reds = array_diff($allRed, $merge_data);
	$other_reds_win = 0;
	foreach ($other_reds as $tmp_red) {
		if (in_array($tmp_red, $red_num)) {
			$other_reds_win++;
		}
	}

	//百合交集
	$jiaoji_reds = array_intersect($data_percent_reds, $data_sum_reds);
	$jiaoji_reds_win = 0;
	foreach ($jiaoji_reds as $tmp_red) {
		if (in_array($tmp_red, $red_num)) {
			$jiaoji_reds_win++;
		}
	}

	//百【减和】
	$percent_reds = array_diff($data_percent_reds, $data_sum_reds);
	$percent_reds_win = 0;
	foreach ($percent_reds as $tmp_red) {
		if (in_array($tmp_red, $red_num)) {
			$percent_reds_win++;
		}
	}

	//和【减百】
	$sum_reds = array_diff($data_sum_reds, $data_percent_reds);
	$sum_reds_win = 0;
	foreach ($sum_reds as $tmp_red) {
		if (in_array($tmp_red, $red_num)) {
			$sum_reds_win++;
		}
	}

	return array(
		'cp_dayid'          => $row['cp_dayid'],
		'red_num'           => $red_num,
		'opencode'          => $row['opencode'],
		'baihe_reds'        => $merge_data,
		'baihe_win'         => $merge_data_win,
		'percent_reds'      => explode(',', $data_percent['tuice_reds']),
		'percent_reds_win'  => $data_percent['win_num'],

		'sum_reds'          => $data_sum_reds,
		'sum_reds_win'      => $data_sum['get_red_num'],

		'other_reds'        => $other_reds,
		'other_reds_win'    => $other_reds_win,

		'jiaoji_reds'       => $jiaoji_reds,
		'jiaoji_reds_win'   => $jiaoji_reds_win,

		'percent_jreds'     => $percent_reds,
		'percent_jreds_win' => $percent_reds_win,

		'sum_jreds'         => $sum_reds,
		'sum_jreds_win'     => $sum_reds_win,
	);
}

function redEdgeCode($cp_dayid = '')
{
	global $dosql;

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC";
	}

	$edgecode = $dosql->GetOne($sql);
	$red_num = explode(',', $edgecode['red_num']);

	$redList = array();
	foreach ($red_num as $red) {
		if ($red == 1) {
			$redList[] = 33;
			$redList[] = 2;
		} else if ($red >= 2 && $red <= 32) {
			$redList[] = $red - 1;
			$redList[] = $red + 1;
		} else {
			$redList[] = 32;
			$redList[] = 1;
		}
	}

	$redList = array_unique($redList);
	sort($redList);

	return array('cp_dayid' => $edgecode['cp_dayid'], 'redList' => $redList);
}

function redUpTailTrend($cp_dayid = '', $num = 8)
{
	global $dosql;

	$alltail = array();
	for ($i = 0; $i < 10; $i++) {
		$alltail[$i] = $i;
	}

	$num = $num * 4;
	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $num";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $num";
	}

	if (empty($cp_dayid)) {
		$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
		$max_dayid = $max['cp_dayid'];
		$cp_dayid = $max_dayid + 1;
	}
	$odd_even = $cp_dayid % 2;

	$redUpTail = array();
	$dosql->Execute($sql, 'bbb');
	$index = 0;
	$redUpTailDetail = array();
	$result = array();
	while ($row = $dosql->GetArray('bbb')) {
		$result[] = $row;
	}

	foreach ($result as $key => $row) {

		$index++;
		if ($index % 4 != 0) {
			continue;
		}
		$red_num = explode(',', $row['red_num']);
		$tmp = array();
		foreach ($red_num as $red) {
			$redUpTail[] = $tmp[] = $red % 10;
		}
		$tmp = array_unique($tmp);
		sort($tmp);
		$diff = array_diff($alltail, $tmp);


		$red_num2 = explode(',', $result[$key - 2]['red_num']);
		$tmp2 = array();
		foreach ($red_num2 as $red) {
			$tmp2[] = $red % 10;
		}
		$tmp2 = array_unique($tmp2);
		sort($tmp2);
		$tmp = array('cp_dayid' => $row['cp_dayid'], 'win' => $tmp, 'nowin' => $diff, 'pretail' => $tmp2, 'blue_num' => $row['blue_num']);
		$redUpTailDetail[$row['cp_dayid']] = $tmp;
	}

	return array_reverse($redUpTailDetail);
}

function redUpTailNear($cp_dayid = '', $limit = 4)
{
	global $dosql;

	$alltail = array();
	for ($i = 0; $i < 10; $i++) {
		$alltail[$i] = $i;
	}

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit";
	}

	if (empty($cp_dayid)) {
		$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
		$max_dayid = $max['cp_dayid'];
		$cp_dayid = $max_dayid + 1;
	}

	$redUpTail = array();
	$dosql->Execute($sql, 'bbb');
	$index = 0;
	$redUpTailDetail = array();
	$result = array();
	while ($row = $dosql->GetArray('bbb')) {
		$result[] = $row;
	}

	foreach ($result as $key => $row) {
		$red_num = explode(',', $row['red_num']);
		$tmp = array();
		$tailnum = array();
		foreach ($red_num as $red) {
			$tail = $red % 10;
			$redUpTail[] = $tmp[] = $tail;
			if (!isset($tailnum[$tail])) {
				$tailnum[$tail] = 0;
			}
			$tailnum[$tail]++;
		}
		$tmp = array_unique($tmp);
		sort($tmp);
		$diff = array_diff($alltail, $tmp);
		$tmp = array('cp_dayid' => $row['cp_dayid'], 'win' => $tmp, 'nowin' => $diff, 'tailnum' => $tailnum);
		$redUpTailDetail[$row['cp_dayid']] = $tmp;
	}

	return array_reverse($redUpTailDetail);
}

/**
 * 红9码规律
 */
function red9Code($cp_dayid = '', $limit = 4)
{
	global $dosql;

	$data = array();
	$data['lu'] = array(0 => array(), 1 => array(), 2 => array());
	$nums = array(3, 6, 9, 1, 4, 7, 2, 5, 8);
	$data['9ma'] = $nums;
	$data['9code'] = array();
	foreach ($nums as $val) {
		$data['9code'][$val] = array();
	}
	$data['9code_red'] = array();
	for ($i = 1; $i <= 33; $i++) {
		$i < 10 && $i = '0' . $i;
		$data['lu'][$i % 3][] = $i;
		$tail = $i % 9 == 0 ? 9 : $i % 9;
		$data['9code'][$tail][] = $i;
		$data['9code_red'][$i] = $tail;
	}

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit";
	}

	if (empty($cp_dayid)) {
		$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
		$max_dayid = $max['cp_dayid'];
		$cp_dayid = $max_dayid + 1;
	}

	$redUpTail = array();
	$dosql->Execute($sql, 'bbb');
	$index = 0;
	$redUpTailDetail = array();
	$result = array();
	while ($row = $dosql->GetArray('bbb')) {
		$result[$row['cp_dayid']] = explode(',', $row['red_num']);
	}

	$data['list'] = array();
	foreach ($result as $cp_dayid => $reds) {
		if (!isset($data['list'][$cp_dayid])) {
			$data['list'][$cp_dayid] = array();
			$data['list'][$cp_dayid]['cp_dayid'] = $cp_dayid;
			$data['list'][$cp_dayid]['code_list'] = array();
			$data['list'][$cp_dayid]['code'] = array();
			foreach ($nums as $val) {
				$data['list'][$cp_dayid]['code'][$val] = array();
				$data['list'][$cp_dayid]['code'][$val]['num'] = 0;
				$data['list'][$cp_dayid]['code'][$val]['child'] = array();
			}
		}
		foreach ($reds as $red) {
			$code = $data['9code_red'][$red];
			$data['list'][$cp_dayid]['code'][$code]['num']++;
			$data['list'][$cp_dayid]['code'][$code]['child'][] = $red;

			$data['list'][$cp_dayid]['code_list'][] = $code;
		}
		$data['list'][$cp_dayid]['code_list'] = array_unique($data['list'][$cp_dayid]['code_list']);
		sort($data['list'][$cp_dayid]['code_list']);
	}

	$data['list'] = array_reverse($data['list']);
	return $data;
}

function curRed9Code()
{
	global $dosql;

	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY id DESC LIMIT 1");
	$reds = explode(',', $row['red_num']);

	$tmpCode = array();
	foreach ($reds as $key => $red) {
		$code = $red % 9 == 0 ? 9 : $red % 9;
		if (!isset($tmpCode[$code])) {
			$tmpCode[$code] = 1;
		}
		$tmpCode[$code]++;
	}
	return implode(',', array_keys($tmpCode));
}

/**
 * 红9码规律
 */
function red9CodeByCode($base, $pipei = 0)
{
	global $dosql;

	$data = array();
	$data['lu'] = array(0 => array(), 1 => array(), 2 => array());
	$nums = array(3, 6, 9, 1, 4, 7, 2, 5, 8);
	$data['9ma'] = $nums;
	$data['9code'] = array();
	foreach ($nums as $val) {
		$data['9code'][$val] = array();
	}
	$data['9code_red'] = array();
	for ($i = 1; $i <= 33; $i++) {
		$i < 10 && $i = '0' . $i;
		$data['lu'][$i % 3][] = $i;
		$tail = $i % 9 == 0 ? 9 : $i % 9;
		$data['9code'][$tail][] = $i;
		$data['9code_red'][$i] = $tail;
	}

	$baseCode = explode(',', $base);
	sort($baseCode);

	$result = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC", "aaa");
	while ($row = $dosql->GetArray('aaa')) {
		$reds = explode(',', $row['red_num']);

		$tmpCode = array();
		foreach ($reds as $key => $red) {
			$code = $red % 9 == 0 ? 9 : $red % 9;
			if (!isset($tmpCode[$code])) {
				$tmpCode[$code] = 1;
			}
			$tmpCode[$code]++;
		}
		$tmpCode = array_keys($tmpCode);
		sort($tmpCode);
		$isok = 0;
		if ($pipei) {
			$isok = 1;
			foreach ($baseCode as $val) {
				if (!in_array($val, $tmpCode)) {
					$isok = 0;
					break;
				}
			}
		} elseif ($baseCode == $tmpCode) {
			$isok = 1;
		}
		if ($isok) {
			$result[$row['cp_dayid']] = explode(',', $row['red_num']);
		}
	}

	$redUpTail = array();
	$index = 0;
	$redUpTailDetail = array();
	$data['list'] = array();
	foreach ($result as $cp_dayid => $reds) {
		if (!isset($data['list'][$cp_dayid])) {
			$data['list'][$cp_dayid] = array();
			$data['list'][$cp_dayid]['cp_dayid'] = $cp_dayid;
			$data['list'][$cp_dayid]['code_list'] = array();
			$data['list'][$cp_dayid]['code'] = array();
			foreach ($nums as $val) {
				$data['list'][$cp_dayid]['code'][$val] = array();
				$data['list'][$cp_dayid]['code'][$val]['num'] = 0;
				$data['list'][$cp_dayid]['code'][$val]['child'] = array();
			}
		}
		foreach ($reds as $red) {
			$code = $data['9code_red'][$red];
			$data['list'][$cp_dayid]['code'][$code]['num']++;
			$data['list'][$cp_dayid]['code'][$code]['child'][] = $red;

			$data['list'][$cp_dayid]['code_list'][] = $code;
		}
		$data['list'][$cp_dayid]['code_list'] = array_unique($data['list'][$cp_dayid]['code_list']);
		sort($data['list'][$cp_dayid]['code_list']);
	}

	$data['list'] = array_reverse($data['list']);
	return $data;
}



//历史中奖数据
function redHistoryWin($reds, $blues, $cp_dayid = '')
{
	global $dosql;

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC");
	} else {
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	}
	$cpRest = array();
	$maxid = 0;
	while ($row = $dosql->GetArray()) {
		if ($maxid == 0) {
			$maxid = $row['cp_dayid'];
		}
		$cpRest[$row['cp_dayid']]['cp_dayid'] = $row['cp_dayid'];
		$cpRest[$row['cp_dayid']]['red_num'] = explode(",", $row['red_num']);
		$cpRest[$row['cp_dayid']]['blue_num'] = $row['blue_num'];
	}

	$maxid = $maxid + 1;
	$curYearRest = array();
	$curYearRest[$maxid] = array();

	$curYearRest[$maxid]['cp_dayid'] = $maxid;
	$curYearRest[$maxid]['red_num']  = $reds;
	$curYearRest[$maxid]['blue_num'] = $blues;

	$rest = array();
	foreach ($curYearRest as $key => $cur_row) {
		if (!isset($rest[$cur_row['cp_dayid']])) {
			$rest[$cur_row['cp_dayid']] = array();
		}
		foreach ($cpRest as $cp_dayid => $row) {
			if ($cur_row['cp_dayid'] <= $cp_dayid) {
				continue;
			}
			$jjarr = array_intersect($cur_row['red_num'], $row['red_num']);
			$jjnum = count($jjarr);
			$bluewin = $cur_row['blue_num'] == $row['blue_num'] ? 1 : 0;
			if ($bluewin == 1 && $jjnum >= 3  || $bluewin == 0 && $jjnum > 3) {
				$arrindex = $jjnum . '+' . $bluewin;
				if (!isset($rest[$cur_row['cp_dayid']][$arrindex])) {
					$rest[$cur_row['cp_dayid']][$arrindex] = 0;
				}
				$rest[$cur_row['cp_dayid']][$arrindex]++;
			}
		}
	}

	$string = "";
	foreach ($rest as $cp_dayid => $row) {
		// $string .= "{$cp_dayid}您的选号：\n" . implode(" ", $reds) . '+' . $blues . "\n\n";
		foreach ($row as $arrindex => $num) {
			$string .= "{$arrindex}={$num}次 ";
		}
	}
	return $string;
}

function GetRedTail($cp_dayid = '', $limit = 4)
{
	global $dosql;

	$alltail = array();
	for ($i = 0; $i < 10; $i++) {
		$alltail[$i] = $i;
	}

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<=$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit";
	}

	$redList = array();
	$dosql->Execute($sql);
	$index = 0;
	while ($row = $dosql->GetArray()) {
		$tmp = array();
		$tmp['cp_dayid'] = $row['cp_dayid'];

		$red_num = explode(',', $row['red_num']);
		$tail = array();
		foreach ($red_num as $red) {
			!in_array($red % 10, $tail) && $tail[] = $red % 10;
		}
		sort($tail);
		$tmp['win'] = $tail;
		$tmp['nowin'] = array_diff($alltail, $tail);

		array_push($redList, $tmp);
	}
	$redList = array_reverse($redList);

	foreach ($redList as $k => $v) {
		$repeat_win    = isset($redList[$k - 1]['win']) ? array_intersect($v['win'], $redList[$k - 1]['win']) : array();
		$nowin_nowin   = isset($redList[$k - 1]['nowin']) ? array_intersect($v['nowin'], $redList[$k - 1]['nowin']) : array();
		$nowin_nextwin = isset($redList[$k + 1]['win']) ? array_intersect($redList[$k + 1]['win'], $v['nowin']) : array();
		$redList[$k]['repeat_win']     = $repeat_win;
		$redList[$k]['nowin_nowin']    = $nowin_nowin;
		$redList[$k]['nowin_nextwin']  = $nowin_nextwin;
		$redList[$k]['op']             = $v['cp_dayid'] % 2 ? '-' : '+';
		$redList[$k]['nowin_nextwin2'] = $nowin_nextwin;
	}

	return $redList;
}

function redTailSum($cp_dayid = '', $num = 9)
{
	global $dosql;

	$alltail = array();
	$alltailsum = array();
	for ($i = 0; $i < 10; $i++) {
		$alltail[$i] = $i;
		if ($i) {
			$alltailsum[$i] = $i;
		}
	}

	if (isset($cp_dayid) && !empty($cp_dayid)) {
		$sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<=$cp_dayid ORDER BY cp_dayid DESC LIMIT $num";
	} else {
		$sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $num";
	}

	if (empty($cp_dayid)) {
		$max = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
		$max_dayid = $max['cp_dayid'];
		$cp_dayid = $max_dayid + 1;
	}


	$result = array();
	$dosql->Execute($sql);
	while ($row = $dosql->GetArray()) {
		$result[] = $row;
	}
	$result = array_reverse($result);

	$index = 0;
	$redTailSum = array();
	foreach ($result as $key => $row) {
		$red_num = explode(',', $row['red_num']);
		$red_tail = array();
		$red_tail_sum = array();
		foreach ($red_num as $red) {
			$red_tail[] = $red % 10;
			$red_tail_sum[] = getSumVal($red);
		}
		$tail_sum_miss = array_diff($alltailsum, $red_tail_sum);
		$repeat = array_count_values($red_tail_sum);
		foreach ($repeat as $k => $v) {
			if ($v == 1) unset($repeat[$k]);
		}
		$repeat = array_keys($repeat);

		$tmp = array();
		$tmp['cp_dayid']      = $row['cp_dayid'];
		$tmp['red_num']       = $red_num;
		$tmp['red_tail']      = $red_tail;
		$tmp['red_tail_sum']  = $red_tail_sum;
		$tmp['repeat']        = $repeat;
		$tmp['tail_sum_miss'] = $tail_sum_miss;

		$redTailSum[] = $tmp;
	}

	foreach ($redTailSum as $index => $row) {
		$redTailSum[$index]['repeat_tail_sum'] = array();
		if (isset($redTailSum[$index - 1]['red_tail_sum'])) {
			$redTailSum[$index]['repeat_tail_sum'] = array_intersect($redTailSum[$index]['red_tail_sum'], $redTailSum[$index - 1]['red_tail_sum']);
			$redTailSum[$index]['repeat_tail_sum'] = array_unique($redTailSum[$index]['repeat_tail_sum']);
			sort($redTailSum[$index]['repeat_tail_sum']);
		}

		$redTailSum[$index]['repeat_tailsum_miss'] = array();
		if (isset($redTailSum[$index - 1]['tail_sum_miss'])) {
			$redTailSum[$index]['repeat_tailsum_miss'] = array_intersect($redTailSum[$index]['red_tail_sum'], $redTailSum[$index - 1]['tail_sum_miss']);
			$redTailSum[$index]['repeat_tailsum_miss'] = array_unique($redTailSum[$index]['repeat_tailsum_miss']);
			sort($redTailSum[$index]['repeat_tailsum_miss']);
		}
	}

	return $redTailSum;
}
