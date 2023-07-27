<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';
require_once dirname(__FILE__) . '/../core/core.func.php';

LoginCheck();

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}

if($action == 'new_kill_blue'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_newkill_blue`');
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
		$kill_11_blue = NewkillBlue7($sqblue1, 2);
		$kill_12_blue = NewkillBlue7($sqblue1, 6);

		$kill_list = array();

		$kill_json = array();
		for ($i=1; $i <=12 ; $i++) { 
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

		$sql = "INSERT INTO `#@__caipiao_newkill_blue` 
		(cp_dayid, opencode, blue_num, kill_json, kill_list, kill_win, isdo) VALUES ('".$cp_dayid."', '".$opencode."', '".$blue_num."', 
		'".$kill_json."', '".$kill_list."', '".$kill_win."', '1')";

		$oknum++;
	}
	echo $oknum;
}

else if($action == 'near_num'){
	$two_num_near = array();
	$three_num_near = array();
	$four_num_near = array();
	$five_num_near = array();
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[] = $i;
	}

	foreach ($allRed as $key => $red) {
		if(isset($allRed[$key+1])){
			$two_num_near[$allRed[$key].'_'.$allRed[$key+1]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if(isset($allRed[$key+1]) && isset($allRed[$key+2])){
			$three_num_near[$allRed[$key].'_'.$allRed[$key+1].'_'.$allRed[$key+2]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if(isset($allRed[$key+1]) && isset($allRed[$key+2]) && isset($allRed[$key+3])){
			$four_num_near[$allRed[$key].'_'.$allRed[$key+1].'_'.$allRed[$key+2].'_'.$allRed[$key+3]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if(isset($allRed[$key+1]) && isset($allRed[$key+2]) && isset($allRed[$key+3]) && isset($allRed[$key+4])){
			$five_num_near[$allRed[$key].'_'.$allRed[$key+1].'_'.$allRed[$key+2].'_'.$allRed[$key+3].'_'.$allRed[$key+4]] = 0;
		}
	}
	// print_r($three_num_near);
	// print_r($four_num_near);
	// print_r($five_num_near);
	// die;

	$allWinReds = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC limit 1");
	while($row = $dosql->GetArray()){
		$allWinReds[$row['cp_dayid']] = $winReds = explode(',', $row['red_num']);
		foreach ($winReds as $key => $red) {
			if(isset($winReds[$key+1]) && isset($two_num_near[$red.'_'.$winReds[$key+1]])){
				$two_num_near[$red.'_'.$winReds[$key+1]]++;
			}
		}
		foreach ($winReds as $key => $red) {
			if(isset($winReds[$key+1]) && isset($winReds[$key+2]) && isset($three_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2]])){
				$three_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2]]++;
			}
		}
		foreach ($winReds as $key => $red) {
			if(isset($winReds[$key+1]) && isset($winReds[$key+2]) && isset($winReds[$key+3]) && isset($four_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2].'_'.$winReds[$key+3]])){
				$four_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2].'_'.$winReds[$key+3]]++;
			}
		}
		foreach ($winReds as $key => $red) {
			if(isset($winReds[$key+1]) && isset($winReds[$key+2]) && isset($winReds[$key+3]) && isset($winReds[$key+4]) && isset($five_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2].'_'.$winReds[$key+3].'_'.$winReds[$key+4]])){
				$five_num_near[$red.'_'.$winReds[$key+1].'_'.$winReds[$key+2].'_'.$winReds[$key+3].'_'.$winReds[$key+4]]++;
			}
		}
	}
	arsort($two_num_near);
	arsort($three_num_near);
	arsort($four_num_near);
	arsort($five_num_near);
	print_r($two_num_near);
	print_r($three_num_near);
	print_r($four_num_near);
	print_r($five_num_near);
}

