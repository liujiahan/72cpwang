<?php
require_once dirname(__FILE__).'/core.func.php';

//计算获得几等奖
function calcPrize($red_win_num, $blue_win_num){
	$prizelevel = 0;
	if($blue_win_num == 1 && $red_win_num < 3){
		$prizelevel = 6;
	}else if(($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)){
		$prizelevel = 5;
	}else if(($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)){
		$prizelevel = 4;
	}else if($red_win_num == 5 && $blue_win_num == 1){
		$prizelevel = 3;
	}else if($red_win_num == 6 && $blue_win_num == 0){
		$prizelevel = 2;
	}else if($red_win_num == 6 && $blue_win_num == 1){
		$prizelevel = 1;
	}
	return $prizelevel;
}

/**
 * 随机红球
 */
function RandReds($max=6){
	$rednum = array();

	while(count($rednum) < $max){
		$num = mt_rand(1, 33);
		$num = $num < 10 ? '0' . $num : $num;

		if(!in_array($num, $rednum)){
			$rednum[] = $num;
		}
	}

	return $rednum;
}

/**
 * 随机蓝球
 */
function RandBlues($max=1){
	$bluenum = array();

	while(count($bluenum) < $max){
		$num = mt_rand(1, 16);
		$num = $num < 10 ? '0' . $num : $num;

		if(!in_array($num, $bluenum)){
			$bluenum[] = $num;
		}
	}

	return $bluenum;
}