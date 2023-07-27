<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/choosered.func.php';
require_once dirname(__FILE__) . '/../core/wuxing.func.php';

if(!isset($token) || $token != md5('72cpwang')){
	LoginCheck();

	if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
		ShowMsg("Permission denied","-1");
	    exit;
	}
}

if($action == 'blue_choose'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_blue_choose`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	$oknum = 0;
	while($row = $dosql->GetArray()){
		$cur_cp_dayid = $cp_dayid = $row['cp_dayid'];
		$next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue1 = $next1['blue_num'];

		$cp_dayid = $next1['cp_dayid'];
		$next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue2 = $next2['blue_num'];

		$cp_dayid = $next2['cp_dayid'];
		$next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue3 = $next3['blue_num'];

		$chooseArr = plusminusGetBlue($blue1, $blue2, $blue3);
		$first_choose = $chooseArr['first_choose'];
		$second_choose = $chooseArr['second_choose'];

		$blue_num = $row['blue_num'];
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];

		$first_win = in_array($blue_num, $first_choose) ? 1 : 0;
		$second_win = in_array($blue_num, $second_choose) ? 1 : 0;

		$first_choose = implode(',', $first_choose);
		$second_choose = implode(',', $second_choose);

		$sql = "INSERT INTO `#@__caipiao_blue_choose` 
		(cp_dayid, opencode, blue_num, first_choose, first_win, 
		second_choose, second_win, isdo) VALUES ('".$cp_dayid."', '".$opencode."', '".$blue_num."', 
		'".$first_choose."', '".$first_win."', '".$second_choose."', '".$second_win."', '1')";

		$dosql->ExecNoneQuery($sql);
		$oknum++;
	}
	echo $oknum;
}

else if($action == 'kill_blue'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_kill_blue`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	$oknum = 0;
	while($row = $dosql->GetArray()){
		$cur_cp_dayid = $cp_dayid = $row['cp_dayid'];
		$next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue1 = $next1['blue_num'];
		$red_1_6 = explode(',', $next1['red_num']);
		$red_1_6 = array_sum($red_1_6);

		$cp_dayid = $next1['cp_dayid'];
		$next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue2 = $next2['blue_num'];

		$cp_dayid = $next2['cp_dayid'];
		$next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue3 = $next3['blue_num'];

		$cp_dayid = $next3['cp_dayid'];
		$next4 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$blue4 = $next4['blue_num'];

		$kill_1_blue = killBlue1($blue1, $blue2);
		$kill_2_blue = killBlue2($blue1);
		$kill_3_blue = killBlue3($blue1, $blue2, $blue3);
		$kill_4_blue = killBlue4($blue1, $blue2, $blue3, $blue4);
		$kill_5_blue = killBlue5($blue1, $red_1_6);
		$kill_6_blue = killBlue6($blue1);
		$kill_7_blue = killBlue7($blue1);
		$kill_8_blue = killBlue8($blue1);

		$kill_list = array();
		$kill_list[] = $kill_1_blue;
		$kill_list[] = $kill_2_blue;
		$kill_list[] = $kill_3_blue;
		$kill_list[] = $kill_4_blue;
		$kill_list[] = $kill_5_blue;
		$kill_list[] = $kill_6_blue;
		$kill_list[] = $kill_7_blue;
		$kill_list[] = $kill_8_blue;

		$kill_list = array_unique($kill_list);
		sort($kill_list);
		$kill_list = implode(',', $kill_list);

		$blue_num = $row['blue_num'];
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];

		$kill_1_win = $blue_num != $kill_1_blue ? 1 : 2;
		$kill_2_win = $blue_num != $kill_2_blue ? 1 : 2;
		$kill_3_win = $blue_num != $kill_3_blue ? 1 : 2;
		$kill_4_win = $blue_num != $kill_4_blue ? 1 : 2;
		$kill_5_win = $blue_num != $kill_5_blue ? 1 : 2;
		$kill_6_win = $blue_num != $kill_6_blue ? 1 : 2;
		$kill_7_win = $blue_num != $kill_7_blue ? 1 : 2;
		$kill_8_win = $blue_num != $kill_8_blue ? 1 : 2;

		$sql = "INSERT INTO `#@__caipiao_kill_blue` 
		(cp_dayid, opencode, blue_num, kill_1_blue, kill_1_win, 
		kill_2_blue, kill_2_win, kill_3_blue, kill_3_win, kill_4_blue, kill_4_win, 
		kill_5_blue, kill_5_win, kill_6_blue, kill_6_win, kill_7_blue, kill_7_win, 
		kill_8_blue, kill_8_win, kill_list, isdo) VALUES ('".$cp_dayid."', '".$opencode."', '".$blue_num."', 
		'".$kill_1_blue."', '".$kill_1_win."', '".$kill_2_blue."', '".$kill_2_win."', 
		'".$kill_3_blue."', '".$kill_3_win."', '".$kill_4_blue."', '".$kill_4_win."', 
		'".$kill_5_blue."', '".$kill_5_win."', '".$kill_6_blue."', '".$kill_6_win."', 
		'".$kill_7_blue."', '".$kill_7_win."', '".$kill_8_blue."', '".$kill_8_win."', '".$kill_list."', '1')";

		if($dosql->ExecNoneQuery($sql)){
			$oknum++;
		}
	}
	echo $oknum;
}

else if($action == 'new_kill_blue2'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_newkill_blue2`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}
	
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	$oknum = 0;
	while($row = $dosql->GetArray()){
		$cp_dayid = $row['cp_dayid'];

		$next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
		$sqblue1 = isset($next1['blue_num']) ? $next1['blue_num'] : 0;
		$cp_dayid2 = isset($next1['cp_dayid']) ? $next1['cp_dayid'] : 0;

		$next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid2 ORDER BY cp_dayid DESC LIMIT 1");
		$sqblue2 = isset($next2['blue_num']) ? $next2['blue_num'] : 0;
		$cp_dayid3 = isset($next2['cp_dayid']) ? $next2['cp_dayid'] : 0;

		$next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid3 ORDER BY cp_dayid DESC LIMIT 1");
		$sqblue3 = isset($next3['blue_num']) ? $next3['blue_num'] : 0;

		$kill_1_blue = NewkillBlue1($sqblue1, 15);
		$kill_2_blue = NewkillBlue1($sqblue1, 19);
		$kill_3_blue = NewkillBlue1($sqblue1, 21);
		$kill_4_blue = NewkillBlue2($sqblue1, $sqblue2);
		$kill_5_blue = NewkillBlue3($sqblue1, $sqblue2);
		$kill_6_blue = NewkillBlue4($sqblue1, $sqblue2);
		$kill_7_blue = NewkillBlue44($sqblue1, $sqblue3);
		$kill_8_blue = NewkillBlue5($sqblue1, 2);
		$kill_9_blue = NewkillBlue5($sqblue1, 4);
		$kill_10_blue = NewkillBlue6($sqblue1, 7);
		$kill_11_blue = NewkillBlue7($sqblue1, 6);

		$kill_list = array();

		$kill_json = array();
		for ($i=1; $i <=11 ; $i++) { 
			$arr = 'kill_'.$i.'_blue';
			$kill_json['kb'.$i] = array();
			$kill_json['kb'.$i]['list'] = $$arr;
			$kill_json['kb'.$i]['kill'] = in_array($row['blue_num'], $$arr) ? 0 : 1;
			foreach ($$arr as $kblue) {
				$kill_list[] = $kblue;
			}
		}
		$kill_list = array_unique($kill_list);
		sort($kill_list);
		$kill_win = in_array($row['blue_num'], $kill_list) ? 0 : 1;

		$kill_list = implode(',', $kill_list);

		$kill_json = json_encode($kill_json);


		$blue_num = $row['blue_num'];
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];

		$sql = "INSERT INTO `#@__caipiao_newkill_blue2` 
		(cp_dayid, opencode, blue_num, kill_json, kill_list, kill_win, isdo) VALUES ('".$cp_dayid."', '".$opencode."', '".$blue_num."', 
		'".$kill_json."', '".$kill_list."', '".$kill_win."', '1')";

		if($dosql->ExecNoneQuery($sql)){
			$oknum++;
		}
	}
	echo $oknum;
}

else if($action == 'blue_xsh'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_blue_xsh`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}
	
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$redlist5 = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<".$row['cp_dayid']." ORDER BY cp_dayid DESC LIMIT 5", '1');
		while($row2 = $dosql->GetArray('1')){
			$redlist5[] = $row2;
		}
		$bluexsh = blueXSH($redlist5);
		$bluexsh_bak = $bluexsh;

		$blue_range = explode(",", $bluexsh_bak['blue_range']);
		$blue_list  = explode(",", $bluexsh_bak['blue_list']);

		$bluexsh['blue_range_win'] = 0;
		$bluexsh['blue_list_win'] = 0;
		$blue_range[0] <= $row['blue_num'] && $blue_range[1] >= $row['blue_num'] && $bluexsh['blue_range_win'] = 1;
		in_array($row['blue_num'], $blue_list) && $bluexsh['blue_list_win'] = 1;

		$beixuan_1  = explode(',', $bluexsh_bak['beixuan_1']);
		$beixuan_2  = explode(',', $bluexsh_bak['beixuan_2']);
		$beixuan_3  = explode(',', $bluexsh_bak['beixuan_3']);
		$beixuan_4  = explode(',', $bluexsh_bak['beixuan_4']);

		$bluexsh['beixuan_1_win'] = 0;
		$bluexsh['beixuan_2_win'] = 0;
		$bluexsh['beixuan_3_win'] = 0;
		$bluexsh['beixuan_4_win'] = 0;

		in_array($row['blue_num'], $beixuan_1) && $bluexsh['beixuan_1_win'] = 1;
		in_array($row['blue_num'], $beixuan_2) && $bluexsh['beixuan_2_win'] = 1;
		in_array($row['blue_num'], $beixuan_3) && $bluexsh['beixuan_3_win'] = 1;
		in_array($row['blue_num'], $beixuan_4) && $bluexsh['beixuan_4_win'] = 1;

		$v = $bluexsh;
		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_blue_xsh` (cp_dayid,blue_num,blue_range,blue_range_win,blue_list,blue_list_win,beixuan_1,beixuan_1_win,beixuan_2,beixuan_2_win,beixuan_3,beixuan_3_win,beixuan_4,beixuan_4_win) 
			VALUES ('".$row['cp_dayid']."','".$row['blue_num']."','".$v['blue_range']."','".$v['blue_range_win']."','".$v['blue_list']."','".$v['blue_list_win']."','".$v['beixuan_1']."','".$v['beixuan_1_win']."','".$v['beixuan_2']."','".$v['beixuan_2_win']."','".$v['beixuan_3']."','".$v['beixuan_3_win']."','".$v['beixuan_4']."','".$v['beixuan_4_win']."')");
	}
	exit(1);	
}

else if($action == 'blue_wuxing'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `lz_caipiao_blue_wuxing`');
	$cp_dayid = 2003001;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `lz_caipiao_history` WHERE cp_dayid>='$cp_dayid' ORDER BY cp_dayid ASC");
	$result = array();
	$index = 0;
	while($row = $dosql->GetArray()){
		$tmp = array();
		$tmp['cp_dayid'] = $row['cp_dayid'];
		$tmp['blue_num'] = $row['blue_num'];
		$result[$index] = $tmp;
		$index++;
	}

	$data = array();
	foreach ($result as $k => $v) {
		if($k==0)continue;

		$cp_dayid = $v['cp_dayid'];
		$blue_num = $v['blue_num'];
		$pre_blue_num = $result[$k-1]['blue_num'];

		$wuxing = LivingRestrain($pre_blue_num, $blue_num);
		$wuxingKey = LivingRestrainConfig();
		$wuxingKey = array_flip($wuxingKey);

		$wuxing_key = $wuxingKey[$wuxing];

		$data[] = '("'.$cp_dayid.'","'.$pre_blue_num.'","'.$blue_num.'","'.$wuxing.'","'.$wuxing_key.'")';
	}

	$total = 0;
	if($data){
		$values = implode(",", $data);
		$sql = "INSERT INTO `#@__caipiao_blue_wuxing` (cp_dayid,pre_blue_num,blue_num,wuxing,wuxing_key) VALUES " . $values;
		if($dosql->ExecNoneQuery($sql)){
			$total = count($data);
		}
	}
	exit($total);
}

else if($action == 'blue_3d'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_blue_fx`');
	$cp_dayid = 2004000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$primes = array(1,2,3,5,7,11,13);

	$dosql->Execute("SELECT * FROM `#@__caipiao_history_prize` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC","blue_fx");
	while($row = $dosql->GetArray('blue_fx')){
		$cp_dayid    = $row['cp_dayid'];
		$blue_num    = $row['blue_num'];

		$prize = $row;
		$prize['rtn_money'] = $prize['p6'] * 5;
		$rtn_money_rate = round($prize['rtn_money'] / $prize['touzhu']*100);
		 
		$missdata    = blueMissing($cp_dayid);
		$blue_num    = $blue_num < 10 ? '0' . $blue_num : $blue_num;
		$blue_miss   = $missdata[$blue_num];
		$blue_status = $blue_miss > 11 ? '冷' : '热';

		$prime_num   = in_array($row['blue_num'], $primes) ? 1 : 0;

		$prerow = $dosql->GetOne("SELECT * FROM `lz_caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 1");
		$preblue = $prerow['blue_num'];

		$zhenfu = abs($blue_num - $preblue);

		$dosql->Execute("SELECT * FROM `lz_caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 160",'blue2');
		$blue_freq = array();
		$allBlue = array();
		for ($i=1; $i < 17; $i++) { 
		    $blue_freq[$i] = 0;
		}

		$blue_hotcool = array();
		while($row = $dosql->GetArray('blue2')){
			$blue_freq[$row['blue_num']]++;
		}
		ksort($blue_freq);

		$insertdata = array(
			'cp_dayid'       => $cp_dayid,
			'blue_num'       => $blue_num,
			'blue_miss'      => $blue_miss,
			'rtn_money_rate' => $rtn_money_rate,
			'blue_status'    => $blue_status,
			'zhenfu'         => $zhenfu,
			'prime_num'      => $prime_num,
			'blue_freq'      => json_encode($blue_freq),
		);

		$field    = implode(",",array_keys($insertdata));
		$fieldVal = "'".implode("','",array_values($insertdata))."'";

		$sql = "INSERT INTO `#@__caipiao_blue_fx` ({$field}) VALUES ({$fieldVal})";

		$dosql->ExecNoneQuery($sql);
	}
	exit($cp_dayid);
}

else if($action == 'wuhang_kill'){
	$wuhang_blue = array();
	for ($i=1; $i < 17; $i++) { 
		$tail = $i % 10;
		$wuhang = DigitFiveElements($tail);
		if(!isset($wuhang_blue[$wuhang])) $wuhang_blue[$wuhang] = array();

		$wuhang_blue[$wuhang][] = $i;
	}

	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_blue_whkill`');
	$cp_dayid = 2003001;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$total = 0;
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$blue_num    = $row['blue_num'];
		$red_num_arr = explode(',', $row['red_num']);

		$tmprow = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 1");
		$reds = explode(",", $tmprow['red_num']);

		$wuhang_reds = array();
		foreach ($reds as $red) {
			$tail = $red % 10;
			$wuhang = DigitFiveElements($tail);
			if(!isset($wuhang_reds[$wuhang])){
				$wuhang_reds[$wuhang] = 0;
			}
			$wuhang_reds[$wuhang]++;
		}
		$kill_blue = array();
		$kill_whnum = 0;
		foreach ($wuhang_reds as $wuhang => $num) {
			if($num > 1){
				$tmpkillblue = $wuhang_blue[$wuhang];
				$kill_blue = array_merge($kill_blue, $tmpkillblue);

				$kill_whnum++;
			}
		}
		$kill_win = $kill_blue && in_array($blue_num, $kill_blue) ? 0 : 1;

		$kill_blue = implode(',',$kill_blue);

		$sql = "INSERT INTO `#@__caipiao_blue_whkill` 
		(cp_dayid, opencode, red_num, blue_num, kill_whnum, kill_blue, kill_win) 
		VALUES ('".$cp_dayid."', '".$opencode."', '".$red_num."', '".$blue_num."', 
		'".$kill_whnum."', '".$kill_blue."', '".$kill_win."')";
		$dosql->ExecNoneQuery($sql);

		$total++;
	}
	exit(1);
}