<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/suanfa.func.php';

LoginCheck();
if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}
if($action == 'kill_blue'){
	$sql = "SELECT * FROM `#@__caipiao_history` WHERE is_kill_blue='0' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql);
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
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_kill_blue='1' WHERE id=".$row['id']);
			$oknum++;
		}
	}
	echo $oknum;
}

//ÕýÖ÷Ñ¡ºÅ
else if($action == 'blue_choose'){
	$sql = "SELECT * FROM `#@__caipiao_history` WHERE is_blue_choose='0' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql);
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

		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_blue_choose='1' WHERE id=".$row['id']);
			$oknum++;
		}
	}
	echo $oknum;
}
//»Æ½ðµãÎ»²â6ºì+1À¶
else if($action == 'gold_tuice'){
	$sql = "SELECT * FROM `#@__caipiao_history` WHERE is_gold='0' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql);
	while($row = $dosql->GetArray()){
		$cp_dayid    = $row['cp_dayid'];
		$opencode    = $row['opencode'];
		$red_num     = $row['red_num'];
		$blue_num    = $row['blue_num'];
		$red_num_arr = explode(',', $row['red_num']);

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 5", '5');
		$before_5red  = array();
		$before_5blue = array();
		while($row2 = $dosql->GetArray(5)){
			$red_arr = explode(',', $row2['red_num']);
			foreach ($red_arr as $key => $red_v) {
				$index = $key + 1;
				if(!isset($before_5red[$index])){
					$before_5red[$index] = array();
				}
				$before_5red[$index][] = $red_v;
			}
			$before_5blue[] = $row2['blue_num'];
		}
		//Ô¤²âºìÇòÊý
		$yuce_red = array();
		foreach ($before_5red as $redball) {
			$redball_sum = array_sum($redball);
			$redball_avg = intval($redball_sum / 5);
			$yuce_red[] = $redball_avg - 1 < 10 ? '0'.($redball_avg - 1) : $redball_avg - 1;
			$yuce_red[] = $redball_avg + 1 < 10 ? '0'.($redball_avg + 1) : $redball_avg + 1;
		}

		//²ÂÖÐºìÇòÊý
		$yuce_red_num = 0;
		$yuce_red_list = array();
		foreach ($yuce_red as $red) {
			if(in_array($red, $red_num_arr)){
				$yuce_red_num++;
				$yuce_red_list[] = $red;
			}
		}

		//Ô¤²âÀ¶ÇòÊý
		$yuce_blue    = array();
		$blueball_sum = array_sum($before_5blue);
		$blueball_avg = intval($blueball_sum / 5);

		$yuce_blue[]  = $blueball_avg;
		$yuce_blue[]  = $blueball_avg - 1 < 10 ? '0'.($blueball_avg - 1) : $blueball_avg - 1;
		$yuce_blue[]  = $blueball_avg - 2 < 10 ? '0'.($blueball_avg - 2) : $blueball_avg - 2;
		$yuce_blue[]  = $blueball_avg + 1 < 10 ? '0'.($blueball_avg + 1) : $blueball_avg + 1;
		$yuce_blue[]  = $blueball_avg + 2 < 10 ? '0'.($blueball_avg + 2) : $blueball_avg + 2;

		//ÊÇ·ñ²ÂÖÐÀ¶Çò
		$yuce_blue_num = 0;
		foreach ($yuce_blue as $blue) {
			if($blue == $blue_num){
				$yuce_blue_num = 1;
			}
		}

		$yuce_red      = implode(',', $yuce_red);
		$yuce_blue     = implode(',', $yuce_blue);
		$yuce_red_list = implode(',', $yuce_red_list);

		$sql = "INSERT INTO `#@__caipiao_gold_analysis` 
		(cp_dayid, opencode, red_num, blue_num, yuce_red, yuce_blue, yuce_red_list, yuce_red_num, yuce_blue_num, isdo) 
		VALUES ('".$cp_dayid."', '".$opencode."', '".$red_num."', '".$blue_num."', 
		'".$yuce_red."', '".$yuce_blue."', '".$yuce_red_list."', '".$yuce_red_num."', '".$yuce_blue_num."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_gold=1 WHERE id=".$row['id']);
		}
	}
	exit(1);
}
//ºìÇòÒÅÂ©ÀäÈÈ×ßÊÆÍ¼
else if($action == 'cool_hot'){
	$sql = "SELECT * FROM `#@__caipiao_history` WHERE is_cool_hot='0' ORDER BY cp_dayid ASC";
	$dosql->Execute($sql, 'cool_hot');
	while($row = $dosql->GetArray('cool_hot')){
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
			if(isset($redMiss[$tmp_red])){
				$tmp_red_miss = $redMiss[$tmp_red];
				$win_miss[$tmp_red] = $tmp_red_miss;
				if($tmp_red_miss >= 0 && $tmp_red_miss <= 4){
					$hot_num++;
				}else if($tmp_red_miss >= 5 && $tmp_red_miss <= 9){
					$warm_num++;
				}else if($tmp_red_miss > 9){
					$cool_num++;
				}
				$miss_sum += $tmp_red_miss;
			}
		}

		$win_miss     = serialize($win_miss);
		$miss_content = serialize($redMiss);

		$sql = "INSERT INTO `#@__caipiao_cool_hot` 
		(cp_dayid, opencode, red_num, hot_num, warm_num, 
		cool_num, miss_sum, win_miss, miss_content, isdo) VALUES ('".$cp_dayid."', '".$opencode."', '".$red_num."', 
		'".$hot_num."', '".$warm_num."', '".$cool_num."', '".$miss_sum."', '".$win_miss."', '".$miss_content."', '1')";

		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_cool_hot='1' WHERE id=".$row['id']);
		}
	}
	exit(1);
}

else if($action == 'pinlv_fenqu'){
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE is_pinlv_fenqu='0' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$redBall = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'".$row['cp_dayid']."' ORDER BY cp_dayid DESC LIMIT 30", 30);
		while ($row2 = $dosql->GetArray(30)) {
			$red_num = explode(',', $row2['red_num']);
			foreach ($red_num as $tmp_red) {
				if(!isset($redBall[$tmp_red])){
					$redBall[$tmp_red] = 0;
				}
				$redBall[$tmp_red]++;
			}
		}
		//ÈçºÎÇø·Ö¸ßÆµ ÖÐÆµ µÍÆµ
		arsort($redBall);
		$highpl = max($redBall);
		$midpl = round($highpl / 2);
		$lowpl = round($midpl / 2);

		$ballpl = array('highpl'=>array(), 'midpl'=>array(), 'lowpl'=>array());
		foreach ($redBall as $tmp_red => $pinlv) {
			if($pinlv > $midpl && $pinlv <= $highpl){
				$ballpl['highpl'][$tmp_red] = $pinlv;
			}else if($pinlv > $lowpl && $pinlv <= $midpl){
				$ballpl['midpl'][$tmp_red] = $pinlv;
			}else if($pinlv <= $lowpl){
				$ballpl['lowpl'][$tmp_red] = $pinlv;
			}
		}
		ksort($ballpl['highpl']);
		ksort($ballpl['midpl']);
		ksort($ballpl['lowpl']);
		// print_r($ballpl['highpl']);
		// print_r($ballpl['midpl']);
		// print_r($ballpl['lowpl']);
		// die;

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
			if(in_array($tmp_red, array_keys($ballpl['highpl']))){
				$cur_ball['high']['red'][] = $tmp_red;
				$cur_ball['high']['num']++;
			}
			if(in_array($tmp_red, array_keys($ballpl['midpl']))){
				$cur_ball['mid']['red'][] = $tmp_red;
				$cur_ball['mid']['num']++;
			}
			if(in_array($tmp_red, array_keys($ballpl['lowpl']))){
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

		$sql = "INSERT INTO `#@__caipiao_red_pinlv_fenqu` (cp_dayid,opencode,red_num, cur_ball,highpl_ball,midpl_ball,lowpl_ball,isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$cur_ball."','".$highpl_ball."','".$midpl_ball."','".$lowpl_ball."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_pinlv_fenqu='1' WHERE id=".$row['id']);
		}
		// print_r($lowpl_ball);die;
	}
}

else if($action == 'pinlv_fenqu_count'){
	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_red_pinlv_fenqu` ORDER BY cp_dayid DESC");
	$cur_ball = unserialize($row['cur_ball']);

	// $cur = array();
	$cur_high = $cur_ball['high']['num'];
	$cur_mid  = $cur_ball['mid']['num'];
	$cur_low  = $cur_ball['low']['num'];

	$dosql->Execute("SELECT * FROM `#@__caipiao_red_pinlv_fenqu` WHERE cp_dayid<'".$row['cp_dayid']."' ORDER BY cp_dayid ASC");
	$high = array();
	$mid  = array();
	$low  = array();
	while($row = $dosql->GetArray()){
		$cur_ball = unserialize($row['cur_ball']);
		$high[$row['cp_dayid']] = $cur_ball['high']['num'];
		$mid[$row['cp_dayid']]  = $cur_ball['mid']['num'];
		$low[$row['cp_dayid']]  = $cur_ball['low']['num'];
	}

	$high_count[$cur_high] = array();
	$mid_count[$cur_mid]   = array();
	$low_count[$cur_low]   = array();
	foreach ($high as $cp_dayid => $num) {
		if($cur_high != $num){
			continue;
		}
		if(isset($high[$cp_dayid+1])){
			$high_count[$cur_high][] = $high[$cp_dayid+1];
		}
	}
	$high_count[$cur_high] = array_count_values($high_count[$cur_high]);
	arsort($high_count[$cur_high]);

	foreach ($mid as $cp_dayid => $num) {
		if($cur_mid != $num){
			continue;
		}
		if(isset($mid[$cp_dayid+1])){
			$mid_count[$cur_mid][] = $mid[$cp_dayid+1];
		}
	}
	$mid_count[$cur_mid] = array_count_values($mid_count[$cur_mid]);
	arsort($mid_count[$cur_mid]);

	foreach ($low as $cp_dayid => $num) {
		if($cur_low != $num){
			continue;
		}
		if(isset($low[$cp_dayid+1])){
			$low_count[$cur_low][] = $low[$cp_dayid+1];
		}
	}
	$low_count[$cur_low] = array_count_values($low_count[$cur_low]);
	arsort($low_count[$cur_low]);

	$content = "";
	foreach ($high_count as $cur_high => $trend) {
		$content .= "{$cur_high}µÄ×ßÊÆ\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_high}×ßÏò{$win_num}¹²{$nums}´Î\n";
		}
	}
	foreach ($mid_count as $cur_mid => $trend) {
		$content .= "\n{$cur_mid}µÄ×ßÊÆ\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_mid}×ßÏò{$win_num}¹²{$nums}´Î\n";
		}
	}
	foreach ($low_count as $cur_low => $trend) {
		$content .= "\n{$cur_low}µÄ×ßÊÆ\n";
		foreach ($trend as $win_num => $nums) {
			$content .= "{$cur_low}×ßÏò{$win_num}¹²{$nums}´Î\n";
		}
	}
	echo $content;
}

else if($action == 'pinlv_trend'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = 0;
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE is_pinlv_trend='0' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT 50", "50");
		$before50 = array();
		while($row2 = $dosql->GetArray("50")){
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

		$sql = "INSERT INTO `#@__caipiao_red_pinlv_trend` (cp_dayid,opencode,red_num, before5_pinlv,before10_pinlv,before25_pinlv,before50_pinlv,isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$before5_pinlv."','".$before10_pinlv."','".$before25_pinlv."','".$before50_pinlv."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_pinlv_trend='1' WHERE id=".$row['id']);
		}
	}
}

else if($action == 'location_cross'){
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE is_cross='0' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];

		$location_one   = 0;
		$location_two   = 0;
		$location_three = 0;
		$location_four  = 0;

		$reds = explode(',', $row['red_num']);
		foreach ($reds as $red) {
			if($red >= 1 && $red <= 8){
				$location_one++;
			}else if($red >= 9 && $red <= 16){
				$location_two++;
			}else if($red >= 17 && $red <= 24){
				$location_three++;
			}else if($red >= 25 && $red <= 33){
				$location_four++;
			}
		}

		$sql = "INSERT INTO `#@__caipiao_red_location_cross` (cp_dayid,opencode,red_num, location_one, location_two, location_three, location_four, isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$location_one."','".$location_two."','".$location_three."','".$location_four."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_cross='1' WHERE id=".$row['id']);
		}
	}
}

else if($action == 'space_periods'){
	set_time_limit(0);
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE is_space_periods='0' ORDER BY cp_dayid ASC", 'space_periods');
	while($row = $dosql->GetArray('space_periods')){
		$cp_dayid = $row['cp_dayid'];
		$opencode = $row['opencode'];
		$red_num  = $row['red_num'];
		$curReds  = explode(',', $row['red_num']);

		$red_miss_arr = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 5", '5');
		while($row2 = $dosql->GetArray('5')){
			$tmp_cp_dayid = $row2['cp_dayid'];
			$redMiss      = getCurMiss($tmp_cp_dayid);
			$redMiss      = $redMiss['red_miss_arr'];
			$tmp_reds     = explode(',', $row2['red_num']);
			foreach ($tmp_reds as $tmp_red) {
				$red_miss = $redMiss[$tmp_red];
				if($red_miss > 4){
					continue;
				}
				if(!isset($red_miss_arr[$red_miss])){
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
			if(!isset($next_miss_arr[$miss_num])){
				$next_miss_arr[$miss_num] = array();
			}
			foreach ($curRedMiss as $tmp_red => $cur_miss_num) {
				if($cur_miss_num == $miss_num){
					$next_miss_arr[$miss_num][] = $tmp_red;
				}
			}
		}

		$miss_win_num = array();
		foreach ($next_miss_arr as $miss_num => $tmp_reds) {
			if(!isset($miss_win_num[$miss_num])){
				$miss_win_num[$miss_num] = 0;
			}
			foreach ($tmp_reds as $tmp_red) {
				if(in_array($tmp_red, $curReds)){
					$miss_win_num[$miss_num]++;
				}
			}
		}

		$red_miss_sort = serialize($red_miss_arr);
		$miss_win_num  = serialize($miss_win_num);

		$sql = "INSERT INTO `#@__caipiao_red_space_periods` (cp_dayid,opencode,red_num, red_miss_sort, miss_win_num, isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$red_miss_sort."','".$miss_win_num."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_space_periods='1' WHERE id=".$row['id']);
		}
	}
	exit(1);
}

else if($action == 'percent'){
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>=2003011 AND is_percent='0' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cp_dayid   = $row['cp_dayid'];
		$opencode   = $row['opencode'];
		$red_num    = $row['red_num'];
		$redBall    = explode(',', $row['red_num']);
		$redBalls05 = array();
		$redBalls10 = array();

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 10", "10");
		while($row2 = $dosql->GetArray("10")){
			$redBalls10[] = explode(',', $row2['red_num']);
		}
		$redBalls05 = array_slice($redBalls10, 0, 5);

		$reds10 = array();
		$reds05 = array();
		foreach ($redBalls10 as $reds) {
			foreach ($reds as $red) {
				if(!isset($reds10[$red])){
					$reds10[$red] = 0;
				}
				$reds10[$red]++;
			}
		}

		foreach ($redBalls05 as $reds) {
			foreach ($reds as $red) {
				if(!isset($reds05[$red])){
					$reds05[$red] = 0;
				}
				$reds05[$red]++;
			}
		}

		$tuice_reds = array();
		foreach ($reds10 as $red => $num) {
			if($num < 2){
				continue;
			}
			if(isset($reds05[$red]) && $reds05[$red] > 0){
				$tuice_reds[] = $red;
			}
		}
		sort($tuice_reds);
		$win_num = 0;
		foreach ($tuice_reds as $red) {
			if(in_array($red, $redBall)){
				$win_num++;
			}
		}
		$tuice_reds = implode(',', $tuice_reds);

		$sql = "INSERT INTO `#@__caipiao_red_percent` (cp_dayid,opencode,red_num, tuice_reds, win_num, isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$tuice_reds."','".$win_num."', '1')";
		if($dosql->ExecNoneQuery($sql)){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_history` SET is_percent='1' WHERE id=".$row['id']);
		}
	}
}

else if($action == 'repeat_win'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = array();
	}

	$allWinReds = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE is_repeat='0' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$allWinReds[$row['cp_dayid']] = explode(',', $row['red_num']);
	}

	foreach ($allWinReds as $cp_dayid => $win_red) {
		$sqlArr = array();
		foreach ($allRed as $red => $v) {
			if(in_array($red, $win_red)){
				$sqlArr[] = '("'.$cp_dayid.'", "'.$red.'", "1")';
			}else{
				$sqlArr[] = '("'.$cp_dayid.'", "'.$red.'", "0")';
			}
		}
		if($sqlArr){
			$sql = "INSERT INTO `#@__caipiao_red_33_win` (cp_dayid, redball, is_win) VALUES " . implode(',', $sqlArr);
			if($dosql->ExecNoneQuery($sql)){
				$sql = "UPDATE `#@__caipiao_history` SET is_repeat='1' WHERE cp_dayid='$cp_dayid'";
				$dosql->ExecNoneQuery($sql);
			}
		}
	}
}

else if($action == 'get_repeat_win'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = $i;
	}

	$data = array();
	foreach ($allRed as $key => $redball) {
		$data[$redball] = array();
		$allWinReds = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_33_win` WHERE redball='$redball' ORDER BY cp_dayid ASC");
		while($row = $dosql->GetArray()){
			if(!isset($allWinReds[$row['redball']])){
				$allWinReds[$row['redball']] = array();
			}
			$allWinReds[$row['redball']][] = $row['is_win'];
		}

		$redArr = $allWinReds[$redball];

		$win = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0);
		foreach ($redArr as $key => $is_win) {
			isset($redArr[$key+1]) && isset($redArr[$key+2]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 0 && $win[1]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 0 && $win[2]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 0 && $win[3]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 0 && $win[4]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 0 && $win[5]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && isset($redArr[$key+7]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 1 && $redArr[$key+7] == 0 && $win[6]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && isset($redArr[$key+7]) && isset($redArr[$key+8]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 1 && $redArr[$key+7] == 1 && $redArr[$key+8] == 0 && $win[7]++;

		}
		$wingt2 = 0;
		foreach ($win as $key => $value) {
			if($key == 1) continue;
			$wingt2 += $value;
		}
		$win['double_win'] = round($win[1] / $wingt2, 1);
		$data[$redball] = $win;
	}

	$sum = array();
	foreach ($data as $redball => $rows) {
		foreach ($rows as $key => $v) {
			if(!isset($sum[$key])){
				$sum[$key] = 0;
			}
			$sum[$key] += $v;
		}
	}
	$avg = array();
	foreach ($sum as $key => $v) {
		$avg[$key] = round($v / count($data), 1);
	}
	$data['avg'] = $avg;
	$data['sum'] = $sum;

	$sqlArr = array();
	foreach ($data as $counttype => $row) {
		$sqlArr[] = '("'.$counttype.'", "'.$row[1].'", "'.$row[2].'", "'.$row[3].'", "'.$row[4].'", "'.$row[5].'", "'.$row[6].'", "'.$row[7].'", "'.$row['double_win'].'")';
	}
	$dosql->ExecNoneQuery("TRUNCATE TABLE `#@__caipiao_red_double_win`");
	if($sqlArr){
		$sql = "INSERT INTO `#@__caipiao_red_double_win` (counttype, win_1, win_2, win_3, win_4, win_5, win_6, win_7, double_win) VALUES " . implode(',', $sqlArr);
		$dosql->ExecNoneQuery($sql);
	}
	
}

else if($action == 'miss_win'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = $i;
	}

	$missWin = array();
	for ($i=1; $i <= 35; $i++) { 
		$fmt = '1';
		for ($j=1; $j <= $i; $j++) { 
			$fmt .= '0';
		}
		$fmt .= '1';
		$missWin[$i] = $fmt;
	}

	$data = array();
	foreach ($allRed as $key => $redball) {
		$data[$redball] = array();
		$allWinReds = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_33_win` WHERE redball='$redball' ORDER BY cp_dayid ASC");
		while($row = $dosql->GetArray()){
			if(!isset($allWinReds[$row['redball']])){
				$allWinReds[$row['redball']] = array();
			}
			$allWinReds[$row['redball']][] = $row['is_win'];
		}

		$redArr = $allWinReds[$redball];
		$redWinMiss = array();
		foreach ($redArr as $key => $is_win) {
			if($is_win != 1) continue;

			for ($i=1; $i <= 35; $i++) { 
				$fmt = '1';
				for ($j=1; $j <= $i; $j++) { 
					if(isset($redArr[$key+$j])){
						$fmt .= $redArr[$key+$j];
					}
				}
				if(isset($redArr[$key+$j+1])){
					$fmt .= $redArr[$key+$j+1];
				}
				if($missWin[$i] == $fmt){
					if(!isset($redWinMiss[$i])){
						$redWinMiss[$i] = 0;
					}
					$redWinMiss[$i]++;
				}else{
					if(!isset($redWinMiss[$i])){
						$redWinMiss[$i] = 0;
					}
				}
			}
		}
		ksort($redWinMiss);
		$data[$redball] = $redWinMiss;
	}

	print_r($data);die;


	
}