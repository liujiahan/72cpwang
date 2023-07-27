<?php

require_once dirname(__FILE__) . '/../../include/config.inc.php';
require_once dirname(__FILE__) . '/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/choosered.func.php';
require_once dirname(__FILE__) . '/../core/wuxing.func.php';

if (!isset($token) || $token != md5('72cpwang')) {
	LoginCheck();

	if (!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)) {
		ShowMsg("Permission denied", "-1");
		exit;
	}
}

if ($action == 'red_sum_divisor') {
	$red_new_list = array();
	$next_redlist = array();

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_sfone`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$cydayid = $row['cp_dayid'];
		$next = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cydayid' ORDER BY cp_dayid ASC");
		if (!isset($next['id'])) {
			continue;
		}

		//开奖红球数组
		$red_num = explode(',', $row['red_num']);
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

		//猜中的红球
		$get_red_list = array();
		if (isset($next['red_num'])) {
			$next_red_num = explode(',', $next['red_num']);
			foreach ($next_redlist as $yc_red) {
				if (in_array($yc_red, $next_red_num)) {
					$get_red_list[] = $yc_red;
				}
			}
		}
		sort($get_red_list);
		$get_red_pinlv = array();
		foreach ($red_new_list as $k => $v) {
			foreach ($v['list'] as $num) {
				if (!isset($get_red_pinlv[$num])) {
					$get_red_pinlv[$num] = 0;
				}
				$get_red_pinlv[$num]++;
			}
		}
		$get_red_num = count($get_red_list);
		$get_red_list = implode(',', $get_red_list);
		ksort($get_red_pinlv);
		$get_red_pinlv = serialize($get_red_pinlv);

		$sql = "INSERT INTO `#@__caipiao_sfone` 
		(cp_dayid, opencode, red_num, get_red_list, get_red_num, get_red_pinlv, posttime) 
		VALUES 
		('" . $row['cp_dayid'] . "', '" . $row['opencode'] . "', '" . $row['red_num'] . "', '" . $get_red_list . "', '" . $get_red_num . "', '" . $get_red_pinlv . "', '" . time() . "')";

		$dosql->ExecNoneQuery($sql);
	}
	exit('1');
} else if ($action == 'red_gold') {
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_gold_analysis`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$blue_num    = $row['blue_num'];
		$red_num_arr = explode(',', $row['red_num']);

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 5", '5');
		$before_5red  = array();
		$before_5blue = array();
		while ($row2 = $dosql->GetArray(5)) {
			$red_arr = explode(',', $row2['red_num']);
			foreach ($red_arr as $key => $red_v) {
				$index = $key + 1;
				if (!isset($before_5red[$index])) {
					$before_5red[$index] = array();
				}
				$before_5red[$index][] = $red_v;
			}
			$before_5blue[] = $row2['blue_num'];
		}

		$yuce_red = array();
		foreach ($before_5red as $redball) {
			$redball_sum = array_sum($redball);
			$redball_avg = intval($redball_sum / 5);
			$yuce_red[] = $redball_avg - 1 < 10 ? '0' . ($redball_avg - 1) : $redball_avg - 1;
			$yuce_red[] = $redball_avg + 1 < 10 ? '0' . ($redball_avg + 1) : $redball_avg + 1;
		}

		$yuce_red_num = 0;
		$yuce_red_list = array();
		$yuce_red = array_unique($yuce_red);
		foreach ($yuce_red as $red) {
			if (in_array($red, $red_num_arr)) {
				$yuce_red_num++;
				$yuce_red_list[] = $red;
			}
		}

		$yuce_blue    = array();
		$blueball_sum = array_sum($before_5blue);
		$blueball_avg = intval($blueball_sum / 5);

		$yuce_blue[]  = $blueball_avg;
		$yuce_blue[]  = $blueball_avg - 1 < 10 ? '0' . ($blueball_avg - 1) : $blueball_avg - 1;
		$yuce_blue[]  = $blueball_avg - 2 < 10 ? '0' . ($blueball_avg - 2) : $blueball_avg - 2;
		$yuce_blue[]  = $blueball_avg + 1 < 10 ? '0' . ($blueball_avg + 1) : $blueball_avg + 1;
		$yuce_blue[]  = $blueball_avg + 2 < 10 ? '0' . ($blueball_avg + 2) : $blueball_avg + 2;

		$yuce_blue_num = 0;
		foreach ($yuce_blue as $blue) {
			if ($blue == $blue_num) {
				$yuce_blue_num = 1;
			}
		}

		$yuce_red      = implode(',', $yuce_red);
		$yuce_blue     = implode(',', $yuce_blue);
		$yuce_red_list = implode(',', $yuce_red_list);

		$sql = "INSERT INTO `#@__caipiao_gold_analysis` 
		(cp_dayid, opencode, red_num, blue_num, yuce_red, yuce_blue, yuce_red_list, yuce_red_num, yuce_blue_num, isdo) 
		VALUES ('" . $cp_dayid . "', '" . $opencode . "', '" . $red_num . "', '" . $blue_num . "', 
		'" . $yuce_red . "', '" . $yuce_blue . "', '" . $yuce_red_list . "', '" . $yuce_red_num . "', '" . $yuce_blue_num . "', '1')";
		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
} else if ($action == 'red_road') {
	$redRoad3 = array();
	$redRoad6 = array();
	for ($i = 1; $i <= 33; $i++) {
		$i < 10 && $i = '0' . $i;
		$yushu = $i % 3;
		if (!isset($redRoad3[$yushu])) {
			$redRoad3[$yushu] = array();
		}
		$redRoad3[$yushu][] = $i;
		$yushu = $i % 6;
		if (!isset($redRoad6[$yushu])) {
			$redRoad6[$yushu] = array();
		}
		$redRoad6[$yushu][] = $i;
	}

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_road`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		echo $cp_dayid  = $row['cp_dayid'];
		$opencode  = $row['opencode'];
		$red_num   = explode(',', $row['red_num']);

		$red_road3 = array();
		$red_road6 = array();
		foreach ($redRoad3 as $yushu => $redlist) {
			if (!isset($red_road3[$yushu])) {
				$red_road3[$yushu] = 0;
			}
			foreach ($red_num as $tmp_red) {
				if (in_array($tmp_red, $redlist)) {
					$red_road3[$yushu]++;
				}
			}
		}

		foreach ($redRoad6 as $yushu => $redlist) {
			if (!isset($red_road6[$yushu])) {
				$red_road6[$yushu] = 0;
			}
			foreach ($red_num as $tmp_red) {
				if (in_array($tmp_red, $redlist)) {
					$red_road6[$yushu]++;
				}
			}
		}

		$red_road3 = serialize($red_road3);
		$red_road6 = serialize($red_road6);
		$sql = "INSERT INTO `#@__caipiao_red_road` (cp_dayid, opencode, red_num, red_road3, red_road6, isdo) 
		VALUES ('" . $cp_dayid . "', '" . $opencode . "', '" . $row['red_num'] . "', '" . $red_road3 . "', '" . $red_road6 . "', '1')";
		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
} else if ($action == 'red_coolhot') {
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_cool_hot`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", "cool_hot");
	while ($row = $dosql->GetArray('cool_hot')) {
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$blue_num    = $row['blue_num'];
		$red_num_arr = explode(',', $row['red_num']);

		$redMiss  = redMissing($cp_dayid);
		$hot_num  = 0;
		$warm_num = 0;
		$cool_num = 0;

		$miss_sum = 0;
		$win_miss = array();
		foreach ($red_num_arr as $tmp_red) {
			if (isset($redMiss[$tmp_red])) {
				$tmp_red_miss = $redMiss[$tmp_red];
				$win_miss[$tmp_red] = $tmp_red_miss;
				if ($tmp_red_miss >= 0 && $tmp_red_miss <= 4) {
					$hot_num++;
				} else if ($tmp_red_miss >= 5 && $tmp_red_miss <= 9) {
					$warm_num++;
				} else if ($tmp_red_miss > 9) {
					$cool_num++;
				}
				$miss_sum += $tmp_red_miss;
			}
		}

		$miss_blue = blueMissing($cp_dayid);

		$win_miss     = serialize($win_miss);
		$miss_content = serialize($redMiss);
		$miss_blue = serialize($miss_blue);

		$sql = "INSERT INTO `#@__caipiao_cool_hot` 
		(cp_dayid, opencode, red_num, hot_num, warm_num, 
		cool_num, miss_sum, win_miss, miss_content, miss_blue) VALUES ('" . $cp_dayid . "', '" . $opencode . "', '" . $red_num . "', 
		'" . $hot_num . "', '" . $warm_num . "', '" . $cool_num . "', '" . $miss_sum . "', '" . $win_miss . "', '" . $miss_content . "', '" . $miss_blue . "')";

		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
}
// 三码组合 
else if ($action == 'red_3code') {
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid, MAX(orderid) as orderid FROM `#@__caipiao_3code`');
	$cp_dayid = 2003000;
	$orderid = 0;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
		$orderid = $max['orderid'];
	}
	$orderid++;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", "cool_hot");
	while ($row = $dosql->GetArray('cool_hot')) {
		$data = array();
		$data['cp_dayid'] = $row['cp_dayid'];
		$data['red_num'] = $row['red_num'];

		// $all3code = zuhe3code(explode(',', $row['red_num']));
		$all3code = combination(explode(',', $row['red_num']), 3);
		foreach ($all3code as &$arr) {
			$arr = implode(".", $arr);
		}
		$data['all3code'] = json_encode($all3code);
		$data['orderid'] = $orderid;

		$field = array_keys($data);
		$value = array_values($data);

		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_3code` (" . implode(',', $field) . ") VALUES ('" . implode("','", $value) . "')");

		$all3code_miss = threeCodeMissing($row['cp_dayid'], $all3code);
		$datas = array();
		foreach ($all3code_miss as $code => $missnum) {
			$datas[] = array(
				'code' => $code,
				'missnum' => $missnum,
				'cp_dayid' => $row['cp_dayid'],
			);
		}

		insertAll($datas, '#@__caipiao_3code_missing');
		$orderid++;
	}
	exit(1);
} else if ($action == 'red_coolhot_happy8') {
	ini_set("memory_limit", "256M");
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__happy8_cool_hot`');
	$cp_dayid = 2020000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__happy8_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", "cool_hot");
	while ($row = $dosql->GetArray('cool_hot')) {
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num_arr = explode(',', $row['opencode']);

		$redMiss  = happy8RedMissing($cp_dayid);
		$hot_num  = 0;
		$warm_num = 0;
		$cool_num = 0;

		$miss_sum = 0;
		$win_miss = array();
		foreach ($red_num_arr as $tmp_red) {
			if (isset($redMiss[$tmp_red])) {
				$tmp_red_miss = $redMiss[$tmp_red];
				$win_miss[$tmp_red] = $tmp_red_miss;
				if ($tmp_red_miss >= 0 && $tmp_red_miss <= 4) {
					$hot_num++;
				} else if ($tmp_red_miss >= 5 && $tmp_red_miss <= 9) {
					$warm_num++;
				} else if ($tmp_red_miss > 9) {
					$cool_num++;
				}
				$miss_sum += $tmp_red_miss;
			}
		}

		$win_miss     = serialize($win_miss);
		$miss_content = serialize($redMiss);

		$sql = "INSERT INTO `#@__happy8_cool_hot` 
		(cp_dayid, opencode, hot_num, warm_num, 
		cool_num, miss_sum, win_miss, miss_content) VALUES ('" . $cp_dayid . "', '" . $opencode . "', '" . $hot_num . "', '" . $warm_num . "', '" . $cool_num . "', '" . $miss_sum . "', '" . $win_miss . "', '" . $miss_content . "')";

		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
} else if ($action == 'chart3code') {
	$row = $dosql->GetOne("SELECT max(cp_dayid) as cp_dayid FROM `#@__caipiao_3code_chart`");
	$max_id = 2003056;
	if (!empty($row['cp_dayid'])) {
		$max_id = $row['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>" . $max_id . " ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$id = $row['cp_dayid'];
		$dosql->Execute("SELECT * FROM `#@__caipiao_3code_missing` where cp_dayid=$id order by id asc", 'B');
		$codes = array();

		$exceed55 = 9999;
		while ($row2 = $dosql->GetArray('B')) {
			if ($row2['missnum'] <= 55) {
				$codes[$row2['code']] = $row2['missnum'];
			} else {
				if ($row2['missnum'] < $exceed55) {
					$exceed55 = $row2['missnum'];
				}
			}
		}
		$rows_code = $codes;

		$insert = array();
		$insert['cp_dayid'] = $id;
		$insert['code'] = json_encode($rows_code);
		$codes = array_count_values($codes); // array_count_values函数对数组中的所有值进行计数
		arsort($codes); //倒排数组
		$max_freq = reset($codes);
		$missnum = key($codes);
		$insert['freq'] = 0;
		$insert['missnum'] = 0;
		if ($max_freq >= 3) {
			$insert['freq'] = $max_freq;
			$insert['missnum'] = $missnum;
		} else {
			$insert['freq'] = 1;
			if ($rows_code) {
				$insert['missnum'] = min($rows_code);
			} else {
				$insert['missnum'] = $exceed55;
			}
		}

		$insert['all_win']  = array();
		$insert['win']  = array();
		foreach ($rows_code as $reds => $miss) {
			$tmp = explode('.', $reds);
			$insert['all_win'] = array_merge($insert['all_win'], $tmp);
			if ($max_freq >= 3 && $miss == $missnum) {
				$insert['win'] = array_merge($insert['win'], $tmp);
			}
		}

		$insert['all_win'] = count(array_unique($insert['all_win']));
		if (!empty($insert['win'])) {
			$insert['win'] = count(array_unique($insert['win']));
		} else {
			if ($insert['freq'] == 1) {
				$insert['win'] = 3;
			} else {
				$insert['win'] = 0;
			}
		}
		insertAll(array($insert), '#@__caipiao_3code_chart');
	}
} else if ($action == 'red_wuxing') {
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_wuxing`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", "wuxing");
	while ($row = $dosql->GetArray('wuxing')) {
		$cp_dayid    = $row['cp_dayid'];
		$cp_day      = $row['cp_day'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];

		$day_attr = DigitFiveElements(date("d", strtotime($cp_day)) % 10);
		$attr = calcChineseEraDay($cp_day);
		$tiangan_attr = $attr['wuxing']['tiangan'];
		$dizhi_attr = $attr['wuxing']['dizhi'];

		$reds = explode(',', $red_num);

		$wuxing_rel = array();
		$wuxing_rel[] = LivingRestrain($reds[0], $reds[1]);
		$wuxing_rel[] = LivingRestrain($reds[1], $reds[2]);
		$wuxing_rel[] = LivingRestrain($reds[2], $reds[3]);
		$wuxing_rel[] = LivingRestrain($reds[3], $reds[4]);
		$wuxing_rel[] = LivingRestrain($reds[4], $reds[5]);

		$wuxing_rel = json_encode($wuxing_rel, JSON_UNESCAPED_UNICODE);

		$sql = "INSERT INTO `#@__caipiao_wuxing` 
		(cp_dayid, cp_day, opencode, red_num, day_attr, tiangan_attr, 
		dizhi_attr, wuxing_rel) VALUES ('" . $cp_dayid . "', '" . $cp_day . "', '" . $opencode . "', '" . $red_num . "', 
		'" . $day_attr . "', '" . $tiangan_attr . "', '" . $dizhi_attr . "', '" . $wuxing_rel . "')";

		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
} else if ($action == 'red_tail_coolhot') {
	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[$i] = $i;
	}

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_tail_cool_hot`');
	$cp_dayid = 2003001;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}
	$total = 0;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>$cp_dayid ORDER BY cp_dayid ASC", 'cool_hot');
	while ($row = $dosql->GetArray('cool_hot')) {
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$blue_num    = $row['blue_num'];
		$red_num_arr = explode(',', $row['red_num']);

		$redMiss  = redTailMissing($cp_dayid);
		$hot_num  = 0;
		$warm_num = 0;
		$cool_num = 0;

		$miss_sum = 0;
		$win_miss = array();
		foreach ($red_num_arr as $tmp_red) {
			$tmp_red = $tmp_red % 10;
			if (isset($redMiss[$tmp_red])) {
				$tmp_red_miss = $redMiss[$tmp_red];
				$win_miss[$tmp_red] = $tmp_red_miss;
				if ($tmp_red_miss == 0) {
					$hot_num++;
				} else if ($tmp_red_miss >= 1 && $tmp_red_miss <= 2) {
					$warm_num++;
				} else if ($tmp_red_miss > 2) {
					$cool_num++;
				}
				$miss_sum += $tmp_red_miss;
			}
		}

		$win_miss     = serialize($win_miss);
		$miss_content = serialize($redMiss);

		$sql = "INSERT INTO `#@__caipiao_tail_cool_hot` 
		(cp_dayid, opencode, red_num, hot_num, warm_num, 
		cool_num, miss_sum, win_miss, miss_content) VALUES ('" . $cp_dayid . "', '" . $opencode . "', '" . $red_num . "', 
		'" . $hot_num . "', '" . $warm_num . "', '" . $cool_num . "', '" . $miss_sum . "', '" . $win_miss . "', '" . $miss_content . "')";

		if ($dosql->ExecNoneQuery($sql)) {
		}
	}
	exit(1);
} else if ($action == 'red_pinlv_fenqu') {
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_pinlv_fenqu`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$redBall = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'" . $row['cp_dayid'] . "' ORDER BY cp_dayid DESC LIMIT 30", 30);
		while ($row2 = $dosql->GetArray(30)) {
			$red_num = explode(',', $row2['red_num']);
			foreach ($red_num as $tmp_red) {
				if (!isset($redBall[$tmp_red])) {
					$redBall[$tmp_red] = 0;
				}
				$redBall[$tmp_red]++;
			}
		}

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

		$cur_ball = array(
			'high' => array(
				'red' => array(),
				'num' => 0
			),
			'mid' => array(
				'red' => array(),
				'num' => 0
			),
			'low' => array(
				'red' => array(),
				'num' => 0
			)
		);

		$cur_red_num = explode(',', $row['red_num']);
		foreach ($cur_red_num as $tmp_red) {
			if (in_array($tmp_red, array_keys($ballpl['highpl']))) {
				$cur_ball['high']['red'][] = $tmp_red;
				$cur_ball['high']['num']++;
			}
			if (in_array($tmp_red, array_keys($ballpl['midpl']))) {
				$cur_ball['mid']['red'][] = $tmp_red;
				$cur_ball['mid']['num']++;
			}
			if (in_array($tmp_red, array_keys($ballpl['lowpl']))) {
				$cur_ball['low']['red'][] = $tmp_red;
				$cur_ball['low']['num']++;
			}
		}
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$cur_ball    = serialize($cur_ball);
		$highpl_ball = serialize($ballpl['highpl']);
		$midpl_ball  = serialize($ballpl['midpl']);

		$lowpl_ball  = serialize($ballpl['lowpl']);

		$sql = "INSERT INTO `#@__caipiao_red_pinlv_fenqu` (cp_dayid,opencode,red_num, cur_ball,highpl_ball,midpl_ball,lowpl_ball,isdo) VALUES ('" . $cp_dayid . "','" . $opencode . "','" . $red_num . "','" . $cur_ball . "','" . $highpl_ball . "','" . $midpl_ball . "','" . $lowpl_ball . "', '1')";
		$dosql->ExecNoneQuery($sql);
	}
} else if ($action == 'pinlv_fenqu_count') {
	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_red_pinlv_fenqu` ORDER BY cp_dayid DESC");
	$cur_ball = unserialize($row['cur_ball']);

	$cur_high = $cur_ball['high']['num'];
	$cur_mid  = $cur_ball['mid']['num'];
	$cur_low  = $cur_ball['low']['num'];

	$dosql->Execute("SELECT * FROM `#@__caipiao_red_pinlv_fenqu` WHERE cp_dayid<'" . $row['cp_dayid'] . "' ORDER BY cp_dayid ASC");
	$high = array();
	$mid  = array();
	$low  = array();
	while ($row = $dosql->GetArray()) {
		$cur_ball = unserialize($row['cur_ball']);
		$high[$row['cp_dayid']] = $cur_ball['high']['num'];
		$mid[$row['cp_dayid']]  = $cur_ball['mid']['num'];
		$low[$row['cp_dayid']]  = $cur_ball['low']['num'];
	}

	$high_count[$cur_high] = array();
	$mid_count[$cur_mid]   = array();
	$low_count[$cur_low]   = array();
	foreach ($high as $cp_dayid => $num) {
		if ($cur_high != $num) {
			continue;
		}
		if (isset($high[$cp_dayid + 1])) {
			$high_count[$cur_high][] = $high[$cp_dayid + 1];
		}
	}
	$high_count[$cur_high] = array_count_values($high_count[$cur_high]);
	arsort($high_count[$cur_high]);

	foreach ($mid as $cp_dayid => $num) {
		if ($cur_mid != $num) {
			continue;
		}
		if (isset($mid[$cp_dayid + 1])) {
			$mid_count[$cur_mid][] = $mid[$cp_dayid + 1];
		}
	}
	$mid_count[$cur_mid] = array_count_values($mid_count[$cur_mid]);
	arsort($mid_count[$cur_mid]);

	foreach ($low as $cp_dayid => $num) {
		if ($cur_low != $num) {
			continue;
		}
		if (isset($low[$cp_dayid + 1])) {
			$low_count[$cur_low][] = $low[$cp_dayid + 1];
		}
	}
	$low_count[$cur_low] = array_count_values($low_count[$cur_low]);
	arsort($low_count[$cur_low]);

	$content = "";
	foreach ($high_count as $cur_high => $trend) {
		$content .= "{$cur_high}高频\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_high}高频{$win_num}-{$nums}´Î\n";
		}
	}
	foreach ($mid_count as $cur_mid => $trend) {
		$content .= "\n{$cur_mid}高频\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_mid}高频{$win_num}-{$nums}´Î\n";
		}
	}
	foreach ($low_count as $cur_low => $trend) {
		$content .= "\n{$cur_low}高频\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_low}高频{$win_num}-{$nums}´Î\n";
		}
	}
	echo $content;
} else if ($action == 'red_pinlv_trend') {
	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[$i] = 0;
	}

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_pinlv_trend`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT 50", "50");
		$before50 = array();
		while ($row2 = $dosql->GetArray("50")) {
			$before50[] = explode(',', $row2['red_num']);
		}

		$before5  = array_slice($before50, 0, 5);
		$before10 = array_slice($before50, 0, 10);
		$before25 = array_slice($before50, 0, 25);

		$before5_pinlv = $allRed;
		foreach ($before5 as $tmp_reds) {
			foreach ($tmp_reds as $tmp_red) {
				$before5_pinlv[$tmp_red]++;
			}
		}

		foreach ($before5_pinlv as $tmp_red => &$value) {
			$value = round($value / 5, 2);
		}

		$before10_pinlv = $allRed;
		foreach ($before10 as $tmp_reds) {
			foreach ($tmp_reds as $tmp_red) {
				$before10_pinlv[$tmp_red]++;
			}
		}

		foreach ($before10_pinlv as $tmp_red => &$value) {
			$value = round($value / 10, 2);
		}

		$before25_pinlv = $allRed;
		foreach ($before25 as $tmp_reds) {
			foreach ($tmp_reds as $tmp_red) {
				$before25_pinlv[$tmp_red]++;
			}
		}

		foreach ($before25_pinlv as $tmp_red => &$value) {
			$value = round($value / 25, 2);
		}

		$before50_pinlv = $allRed;
		foreach ($before50 as $tmp_reds) {
			foreach ($tmp_reds as $tmp_red) {
				$before50_pinlv[$tmp_red]++;
			}
		}

		foreach ($before50_pinlv as $tmp_red => &$value) {
			$value = round($value / 50, 2);
		}

		$before5_pinlv  = serialize($before5_pinlv);
		$before10_pinlv = serialize($before10_pinlv);
		$before25_pinlv = serialize($before25_pinlv);
		$before50_pinlv = serialize($before50_pinlv);

		$sql = "INSERT INTO `#@__caipiao_red_pinlv_trend` (cp_dayid,opencode,red_num, before5_pinlv,before10_pinlv,before25_pinlv,before50_pinlv,isdo) VALUES ('" . $cp_dayid . "','" . $opencode . "','" . $red_num . "','" . $before5_pinlv . "','" . $before10_pinlv . "','" . $before25_pinlv . "','" . $before50_pinlv . "', '1')";
		$dosql->ExecNoneQuery($sql);
	}
} else if ($action == 'red_location_cross') {
	$arrs = array(
		8 => array(
			'1-8'   => array(1, 8),
			'9-16'  => array(9, 16),
			'17-24' => array(17, 24),
			'25-33' => array(25, 33),
		),
		5 => array(
			'1-5'   => array(1, 5),
			'6-10'  => array(6, 10),
			'11-15' => array(11, 15),
			'16-20' => array(16, 20),
			'21-25' => array(21, 25),
			'25-30' => array(25, 30),
			'31-33' => array(31, 33),
		),
		4 => array(
			'1-4'   => array(1, 4),
			'5-8'   => array(5, 8),
			'9-12'  => array(9, 12),
			'13-16' => array(13, 16),
			'17-20' => array(17, 20),
			'21-24' => array(21, 24),
			'25-28' => array(25, 28),
			'29-33' => array(29, 33),
		),
		3 => array(
			'1-11'   => array(1, 11),
			'12-22'   => array(12, 22),
			'23-33'  => array(23, 33),
		),
	);
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_location_cross`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while ($row = $dosql->GetArray()) {
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];

		$fenqu8 = array();
		$fenqu5 = array();
		$fenqu4 = array();
		$fenqu3 = array();

		$reds = explode(',', $row['red_num']);
		foreach ($reds as $red) {
			foreach ($arrs[8] as $key => $arr) {
				if (!isset($fenqu8[$arr[0] . '-' . $arr[1]])) {
					$fenqu8[$arr[0] . '-' . $arr[1]] = 0;
				}
				if ($red >= $arr[0] && $red <= $arr[1]) {
					$fenqu8[$arr[0] . '-' . $arr[1]]++;
				}
			}

			foreach ($arrs[5] as $key => $arr) {
				if (!isset($fenqu5[$arr[0] . '-' . $arr[1]])) {
					$fenqu5[$arr[0] . '-' . $arr[1]] = 0;
				}
				if ($red >= $arr[0] && $red <= $arr[1]) {
					$fenqu5[$arr[0] . '-' . $arr[1]]++;
				}
			}

			foreach ($arrs[4] as $key => $arr) {
				if (!isset($fenqu4[$arr[0] . '-' . $arr[1]])) {
					$fenqu4[$arr[0] . '-' . $arr[1]] = 0;
				}
				if ($red >= $arr[0] && $red <= $arr[1]) {
					$fenqu4[$arr[0] . '-' . $arr[1]]++;
				}
			}

			foreach ($arrs[3] as $key => $arr) {
				if (!isset($fenqu3[$arr[0] . '-' . $arr[1]])) {
					$fenqu3[$arr[0] . '-' . $arr[1]] = 0;
				}
				if ($red >= $arr[0] && $red <= $arr[1]) {
					$fenqu3[$arr[0] . '-' . $arr[1]]++;
				}
			}
		}

		$fenqu3_5num = array();

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC limit 5", "2");
		while ($row2 = $dosql->GetArray('2')) {
			$reds = explode(',', $row2['red_num']);
			foreach ($reds as $red) {
				foreach ($arrs[3] as $key => $arr) {
					if (!isset($fenqu3_5num[$arr[0] . '-' . $arr[1]])) {
						$fenqu3_5num[$arr[0] . '-' . $arr[1]] = 0;
					}
					if ($red >= $arr[0] && $red <= $arr[1]) {
						$fenqu3_5num[$arr[0] . '-' . $arr[1]]++;
					}
				}
			}
		}

		$fenqu3_5num = serialize($fenqu3_5num);

		$fenqu3 = serialize($fenqu3);
		$fenqu4 = serialize($fenqu4);
		$fenqu5 = serialize($fenqu5);
		$fenqu8 = serialize($fenqu8);

		$sql = "INSERT INTO `#@__caipiao_red_location_cross` (cp_dayid,opencode,red_num, fenqu8, fenqu5, fenqu4,fenqu3,fenqu3_5num) VALUES ('" . $cp_dayid . "','" . $opencode . "','" . $red_num . "','" . $fenqu8 . "','" . $fenqu5 . "','" . $fenqu4 . "','" . $fenqu3 . "','" . $fenqu3_5num . "')";
		$dosql->ExecNoneQuery($sql);
	}
} else if ($action == 'red_space_periods') {
	set_time_limit(0);
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_space_periods`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", "abc");
	while ($row = $dosql->GetArray('abc')) {
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];
		$curReds  = explode(',', $row['red_num']);

		$red_miss_arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0);
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 5", '5');
		while ($row2 = $dosql->GetArray('5')) {
			$tmp_cp_dayid = $row2['cp_dayid'];
			$redMiss      = getCurMiss($tmp_cp_dayid);
			$redMiss      = $redMiss['red_miss_arr'];
			$tmp_reds     = explode(',', $row2['red_num']);
			foreach ($tmp_reds as $tmp_red) {
				$red_miss = $redMiss[$tmp_red];
				if ($red_miss > 4) {
					continue;
				}
				if (!isset($red_miss_arr[$red_miss])) {
					$red_miss_arr[$red_miss] = 0;
				}
				$red_miss_arr[$red_miss]++;
			}
		}
		asort($red_miss_arr);

		$curRedMiss = getCurMiss($cp_dayid);
		$curRedMiss = $curRedMiss['red_miss_arr'];

		$next_miss_arr = array();
		foreach ($red_miss_arr as $miss_num => $num) {
			if (!isset($next_miss_arr[$miss_num])) {
				$next_miss_arr[$miss_num] = array();
			}
			foreach ($curRedMiss as $tmp_red => $cur_miss_num) {
				if ($cur_miss_num == $miss_num) {
					$next_miss_arr[$miss_num][] = $tmp_red;
				}
			}
		}

		$miss_win_num = array();
		foreach ($next_miss_arr as $miss_num => $tmp_reds) {
			if (!isset($miss_win_num[$miss_num])) {
				$miss_win_num[$miss_num] = 0;
			}
			foreach ($tmp_reds as $tmp_red) {
				if (in_array($tmp_red, $curReds)) {
					$miss_win_num[$miss_num]++;
				}
			}
		}

		$red_miss_sort = serialize($red_miss_arr);
		$miss_win_num  = serialize($miss_win_num);

		$sql = "INSERT INTO `#@__caipiao_red_space_periods` (cp_dayid,opencode,red_num, red_miss_sort, miss_win_num, isdo) VALUES ('" . $cp_dayid . "','" . $opencode . "','" . $red_num . "','" . $red_miss_sort . "','" . $miss_win_num . "', '1')";
		$dosql->ExecNoneQuery($sql);
	}
	exit(1);
} else if ($action == 'red_analysis') {
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
	$rest2 = array();
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
					// $rest[$cur_row['cp_dayid']][$arrindex] = array();
					$rest[$cur_row['cp_dayid']][$arrindex] = 0;
				}
				// $rest[$cur_row['cp_dayid']][$arrindex][] = '【'.$row['cp_dayid'].'】'.implode(",", $row['red_num']);
				$rest[$cur_row['cp_dayid']][$arrindex]++;
				if ($bluewin == 1 && $jjnum >= 4 || $jjnum > 4) {
					if (!isset($rest2[$arrindex])) {
						$rest2[$arrindex] = array();
					}
					$rest2[$arrindex][] = $cp_dayid;
				}
			}
		}
	}

	$string = "";
	foreach ($rest as $cp_dayid => $row) {
		$string .= "{$cp_dayid}您的选号：\n" . implode(" ", $reds) . '+' . $blues . "\n\n";
		foreach ($row as $arrindex => $num) {
			$string .= "往期出现{$arrindex}的次数：{$num}";
			if (isset($rest2[$arrindex])) {
				$string .= "[" . implode("、", $rest2[$arrindex]) . "]";
			}
			$string .= "\n\n";
		}
	}
	echo $string;
}
//百合算法
else if ($action == 'red_baihe') {
	$total = 0;
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_baihe`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", 'baihe');
	while ($row = $dosql->GetArray('baihe')) {
		$data = BaiHeAnalysis($row['cp_dayid']);
		foreach ($data as $key => &$dv) {
			if (is_array($dv)) {
				$dv = "'" . implode(',', $dv) . "'";
			} else if (is_string($dv)) {
				$dv = "'" . $dv . "'";
			}
		}

		$field = array_keys($data);
		$value = array_values($data);

		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_baihe` (" . implode(',', $field) . ") VALUES (" . implode(',', $value) . ")");
		$total++;
	}
	echo $total;
	exit;
}

//红球尾数统计
else if ($action == 'red_tail') {
	$tailarr = array();
	for ($i = 0; $i <= 9; $i++) {
		$tailarr[$i] = 0;
	}
	$alltail = array_keys($tailarr);
	$tail_detail_arr = array(
		'tail_num' => 0,
		'num4_tail' => 0, 'num3_tail' => 0,
		'small_tail' => 0, 'big_tail' => 0,
		'odd_tail' => 0, 'even_tail' => 0,
		'prime_tail' => 0, 'comb_tail' => 0, 'nopc_tail' => 0
	);
	$total = 0;

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_tail`');
	$cp_dayid = 2003000;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC", '1');
	while ($row = $dosql->GetArray('1')) {

		$data = array();
		$data['cp_dayid'] = $row['cp_dayid'];
		$data['opencode'] = $row['opencode'];
		$data['red_num']  = $row['red_num'];
		$data['red_tail'] = array();
		$data['posttime'] = time();

		$red_tail = $tailarr;
		$red_tail_detail = $tail_detail_arr;

		$red_num = explode(',', $row['red_num']);
		$tmp_tail = array();

		$num4_tail = $num3_tail = $small_tail = $big_tail = $odd_tail = $even_tail = $prime_tail = $comb_tail = $nopc_tail = array();
		foreach ($red_num as $red) {
			$tail = $red % 10;
			$red_tail[$tail]++;
			if (in_array($tail, array(1, 2, 3))) {
				if (!isset($num4_tail[$tail])) {
					$num4_tail[$tail] = 1;
					$red_tail_detail['num4_tail']++;
				}
			} else {
				if (!isset($num3_tail[$tail])) {
					$num3_tail[$tail] = 1;
					$red_tail_detail['num3_tail']++;
				}
			}

			if ($tail <= 4) {
				if (!isset($small_tail[$tail])) {
					$small_tail[$tail] = 1;
					$red_tail_detail['small_tail']++;
				}
			} else {
				if (!isset($big_tail[$tail])) {
					$big_tail[$tail] = 1;
					$red_tail_detail['big_tail']++;
				}
			}

			if ($tail % 2 == 1) {
				if (!isset($odd_tail[$tail])) {
					$odd_tail[$tail] = 1;
					$red_tail_detail['odd_tail']++;
				}
			} else {
				if (!isset($even_tail[$tail])) {
					$even_tail[$tail] = 1;
					$red_tail_detail['even_tail']++;
				}
			}
			if (in_array($tail, array(2, 3, 5, 7))) {
				if (!isset($prime_tail[$tail])) {
					$prime_tail[$tail] = 1;
					$red_tail_detail['prime_tail']++;
				}
			} else if (in_array($tail, array(4, 6, 8, 9))) {
				if (!isset($comb_tail[$tail])) {
					$comb_tail[$tail] = 1;
					$red_tail_detail['comb_tail']++;
				}
			} else if (in_array($tail, array(0, 1))) {
				if (!isset($nopc_tail[$tail])) {
					$nopc_tail[$tail] = 1;
					$red_tail_detail['nopc_tail']++;
				}
			}

			if (!in_array($tail, $tmp_tail)) {
				$tmp_tail[] = $tail;
			}
		}

		$repeat_tail = 0;
		$diff_tail = 0;
		if ($row['cp_dayid'] > 2003001) {
			$pre_row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'{$row['cp_dayid']}' ORDER BY cp_dayid DESC");
			$pre_red = explode(',', $pre_row['red_num']);
			$pre_tail = array();
			foreach ($pre_red as $pre_tmp_red) {
				$pre_tail[] = $pre_tmp_red % 10;
			}
			$pre_tail = array_unique($pre_tail);
		}

		$repeat_tail_arr     = array_intersect($tmp_tail, $pre_tail);
		$diff_tail_arr       = array_diff($alltail, $pre_tail);
		$diff_tail_arr       = array_intersect($diff_tail_arr, $tmp_tail);
		$data['repeat_tail'] = count($repeat_tail_arr);
		$data['diff_tail']   = count($diff_tail_arr);

		$red_tail_detail['tail_num'] = count($tmp_tail);
		$data['red_tail']            = serialize($red_tail);
		$data['red_tail_detail']     = serialize($red_tail_detail);

		$field = array_keys($data);
		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_red_tail` (" . implode(',', $field) . ") VALUES ('" . implode('\',\'', $data) . "')");
		$total++;
	}
	echo $total;
	exit;
} else if ($action == 'red_edgecode') {
	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[$i] = $i;
	}

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_edgecode`');
	$cp_dayid = 2003001;
	if (!empty($max['cp_dayid'])) {
		$cp_dayid = $max['cp_dayid'];
	}
	$total = 0;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>$cp_dayid ORDER BY cp_dayid ASC", 'aaa');
	while ($row = $dosql->GetArray('aaa')) {
		$data = array();
		$data['cp_dayid'] = $row['cp_dayid'];
		$data['opencode'] = $row['opencode'];
		$data['red_num']  = $row['red_num'];

		$red_num = explode(',', $row['red_num']);

		$edgecode = redEdgeCode($row['cp_dayid']);
		$edgecode = $edgecode['redList'];

		$win_num = 0;
		foreach ($red_num as $red) {
			in_array($red, $edgecode) && $win_num++;
		}
		$data['red_edgecode'] = implode(',', $edgecode);
		$data['win_num']      = $win_num;

		$field = array_keys($data);
		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_red_edgecode` (" . implode(',', $field) . ") VALUES ('" . implode('\',\'', $data) . "')");
		$total++;
	}
	echo $total;
	exit;
}
