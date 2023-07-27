<?php

/*五行配置*/

/**
 * 中国天干地支
 */
function ChineseEra(){
	$ChineseEra = array();

	// 十天干：
	// 甲（jiǎ）、乙（yǐ）、丙（bǐng）、丁（dīng）、戊（wù）、
	// 己（jǐ）、庚（gēng）、辛（xīn）、壬（rén）、癸（guǐ）；
	$ChineseEra['tiangan'] = array(
		'甲', '乙','丙','丁','戊','己','庚','辛','壬','癸',
	);

	// 十二地支：
	// 子（zǐ）、丑（chǒu）、寅（yín）、卯（mǎo）、辰（chén）、
	// 巳（sì）、午（wǔ）、未（wèi）、申（shēn）、酉（yǒu）、
	// 戌（xū）、亥（hài）；
	// 十二地支对应十二生肖
	// 子：鼠；
	// 丑：牛；
	// 寅：虎；
	// 卯：兔；
	// 辰：龙；
	// 巳：蛇；
	// 午：马；
	// 未：羊；
	// 申：猴；
	// 酉：鸡；
	// 戌：狗；
	// 亥：猪；
	$ChineseEra['dizhi'] = array(
		'子', '丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'
	);

	return $ChineseEra;
}

/**
 * 天干地支与五行的关系
 */
function ChineseEraFiveElements(){
	/* 
		1、五行理论的核心是运动、变化、平衡
	*/
	$config = array(
		'tiangan' => array(
			'庚' => '金', '辛' => '金',
			'甲' => '木', '乙' => '木',
			'壬' => '水', '癸' => '水',
			'丙' => '火', '丁' => '火',
			'戊' => '土', '己' => '土',
		),
		'dizhi' => array(
			'申' => '金', '酉' => '金',
			'寅' => '木', '卯' => '木',
			'子' => '水', '亥' => '水',
			'巳' => '火', '午' => '火',
			'丑' => '土', '辰' => '土', '未' => '土', '戌' => '土'
		),
	);

	return $config;
}

// print_r(calcChineseEraDay($date='2020-10-11'));exit;

/**
 * 算天干地支日
 */
function calcChineseEraDay($date='2020-01-01')
{
	if(empty($date)){
		$nowtime = time();
	}else{
		$nowtime = strtotime($date . " 14:18:28");
	}
	$time = explode(' ', gmstrftime('%C %y %m %d', $nowtime));
	$C = $time[0];
	$y = $time[1];
	$M = $time[2];
	$d = $time[3];
	if($M == 1) {
		$y = $y - 1;
		$M = 13;
	}
	if($M == 2) {
		$y = $y - 1;
		$M = 14;
	}

	$i = 0;
	if($M % 2 == 0) $i = 6;

	$gan = 4 * $C + intval($C / 4) + 5 * $y + intval($y / 4) + intval(3 * ($M + 1) / 5) + $d - 3; 
	$zhi = 8 * $C + intval($C / 4) + 5 * $y + intval($y / 4) + intval(3 * ($M + 1) / 5) + $d + 7 + $i; 

	$gan = $gan % 10;
	$zhi = $zhi % 12;

	$gan = $gan == 0 ? 10 : $gan;
	$zhi = $zhi == 0 ? 12 : $zhi;

	$tiangan = ChineseEra()['tiangan'][$gan - 1];
	$dizhi = ChineseEra()['dizhi'][$zhi - 1];

	$ganzhi = array('tiangan'=>$tiangan, 'dizhi'=>$dizhi);

	$wuxing = array();
	$wuxing['tiangan'] = ChineseEraFiveElements()['tiangan'][$tiangan];
	$wuxing['dizhi'] = ChineseEraFiveElements()['dizhi'][$dizhi];

	return array('ganzhi' => $ganzhi, 'wuxing' => $wuxing);
}

function FiveElements(){
	$FiveElements = array();

	$FiveElements['five_elements'] = array('金', '木', '水', '火', '土');

	//相生关系
	$FiveElements['living'] = array(
		'金' => '水',
		'水' => '木',
		'木' => '火',
		'火' => '土',
		'土' => '金',
	);

	//被生关系
	$FiveElements['be_living'] = array_flip($FiveElements['living']);

	//相克关系
	$FiveElements['restrain'] = array(
		'金' => '木',
		'木' => '土',
		'土' => '水',
		'水' => '火',
		'火' => '金',
	);

	//被克关系
	$FiveElements['be_restrain'] = array_flip($FiveElements['restrain']);

	//相同
	$FiveElements['same'] = array(
		'金' => '金',
		'木' => '木',
		'土' => '土',
		'水' => '水',
		'火' => '火',
	);
	
	return $FiveElements;
}

function DigitFiveElements($digit=''){
	$config = array(
		'4' => '金', '9' => '金',
		'3' => '木', '8' => '木',
		'1' => '水', '6' => '水',
		'2' => '火', '7' => '火',
		'5' => '土', '0' => '土',
	);

	return isset($config[$digit]) ? $config[$digit] : $config;
}

function LivingRestrainConfig(){
	return array(
		'1' => '相生', '2' => '被生',
		'3' => '相克', '4' => '被克',
		'5' => '相同'
	);
}

function LivingRestrain($num1, $num2){
	$num1 = $num1 % 10;
	$num2 = $num2 % 10;

	$num1_five = DigitFiveElements($num1);
	$num2_five = DigitFiveElements($num2);

	$five = FiveElements();
	$living      = $five['living'];
	$be_living   = $five['be_living'];
	$restrain    = $five['restrain'];
	$be_restrain = $five['be_restrain'];
	$same        = $five['same'];

	if(isset($living[$num1_five]) && $living[$num1_five] == $num2_five){
		return '相生';
	}

	if(isset($be_living[$num1_five]) && $be_living[$num1_five] == $num2_five){
		return '被生';
	}

	if(isset($restrain[$num1_five]) && $restrain[$num1_five] == $num2_five){
		return '相克';
	}

	if(isset($be_restrain[$num1_five]) && $be_restrain[$num1_five] == $num2_five){
		return '被克';
	}

	if(isset($same[$num1_five]) && $same[$num1_five] == $num2_five){
		return '相同';
	}
}

function blueWuXing($blue_num){
	$blue_num = $blue_num % 10;
	$blue_five = DigitFiveElements($blue_num);

	$FiveElements = FiveElements();

	$data = array();
	$living_elements      = $FiveElements['living'][$blue_five];
	$be_living_elements   = $FiveElements['be_living'][$blue_five];
	$restrain_elements    = $FiveElements['restrain'][$blue_five];
	$be_restrain_elements = $FiveElements['be_restrain'][$blue_five];
	$same_elements        = $FiveElements['same'][$blue_five];

	$digitFiveEle = DigitFiveElements();
	foreach ($digitFiveEle as $num => $fiveEle) {
		if($fiveEle == $living_elements){
			if(!isset($data['living'])){
				$data['living'] = array();
			}
			$num == 0 && $data['living'][] = 10;
			$num >= 7 && $data['living'][] = $num;
			if($num>=1 && $num <= 6){
				$data['living'][] = $num;
				$data['living'][] = $num+10;
			}
		}
		if($fiveEle == $be_living_elements){
			if(!isset($data['be_living'])){
				$data['be_living'] = array();
			}
			$num == 0 && $data['be_living'][] = 10;
			$num >= 7 && $data['be_living'][] = $num;
			if($num>=1 && $num <= 6){
				$data['be_living'][] = $num;
				$data['be_living'][] = $num+10;
			}
		}
		if($fiveEle == $restrain_elements){
			if(!isset($data['restrain'])){
				$data['restrain'] = array();
			}
			$num == 0 && $data['restrain'][] = 10;
			$num >= 7 && $data['restrain'][] = $num;
			if($num>=1 && $num <= 6){
				$data['restrain'][] = $num;
				$data['restrain'][] = $num+10;
			}
		}
		if($fiveEle == $be_restrain_elements){
			if(!isset($data['be_restrain'])){
				$data['be_restrain'] = array();
			}
			$num == 0 && $data['be_restrain'][] = 10;
			$num >= 7 && $data['be_restrain'][] = $num;
			if($num>=1 && $num <= 6){
				$data['be_restrain'][] = $num;
				$data['be_restrain'][] = $num+10;
			}
		}
		if($fiveEle == $same_elements){
			if(!isset($data['same'])){
				$data['same'] = array();
			}
			$num == 0 && $data['same'][] = 10;
			$num >= 7 && $data['same'][] = $num;
			if($num>=1 && $num <= 6){
				$data['same'][] = $num;
				$data['same'][] = $num+10;
			}
		}
	}

	foreach ($data as $key => &$value) {
		foreach ($value as &$v) {
			$v < 10 && $v = '0' . $v;
		}
		sort($value);
	}
	return $data;
}

function blueWuXingKill($cp_dayid = ''){
	global $dosql;

	$wuhang_blue = array();
	for ($i=1; $i < 17; $i++) { 
		$tail = $i % 10;
		$wuhang = DigitFiveElements($tail);
		if(!isset($wuhang_blue[$wuhang])) $wuhang_blue[$wuhang] = array();

		$wuhang_blue[$wuhang][] = $i;
	}

	if(!empty($cp_dayid)){
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	}else{
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	}

	$reds = explode(",", $row['red_num']);

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
	$wins_blue = array();
	$kill_whnum = 0;
	foreach ($wuhang_reds as $wuhang => $num) {
		$tmpkillblue = $wuhang_blue[$wuhang];
		if($num > 1){
			$kill_blue = array_merge($kill_blue, $tmpkillblue);

			$kill_whnum++;
		}
	}

	foreach ($wuhang_blue as $wuhang => $blues) {
		if(!isset($wuhang_reds[$wuhang]) || $wuhang_reds[$wuhang] < 2)
			$wins_blue = array_merge($wins_blue, $blues);
	}

	return array('kill_whnum'=>$kill_whnum,'kill_blue'=>$kill_blue,'wins_blue'=>$wins_blue,'reds'=>$reds);
}

function wuxingtj($limit_num=300){
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_blue_wuxing` ORDER BY cp_dayid DESC LIMIT $limit_num");
	while($row = $dosql->GetArray()){
	    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
	    if($xdate == 1){
	      $xdate = substr($row['cp_dayid'], 0, 4);
	    }
	    $suanfa['date'][] = $xdate;
	    $suanfa['wuxing'][] = $row['wuxing_key'];
	}

	$wuxing = array_reverse($suanfa['wuxing']);

	$data = array();
	$arr3 = array_slice($wuxing, -3);
	$arr3 = implode("-", $arr3);
	if(!isset($data[$arr3])){
		$data[$arr3] = array();
	}
	$arr2 = array_slice($wuxing, -2);
	$arr2 = implode("-", $arr2);
	if(!isset($data[$arr2])){
		$data[$arr2] = array();
	}

	foreach ($wuxing as $k => $v) {
		if(isset($wuxing[$k+1]) && isset($wuxing[$k+2])){
			if(isset($wuxing[$k+3])){
				$index3 = implode("-",array($wuxing[$k], $wuxing[$k+1], $wuxing[$k+2]));
				$flat = 0;
				if(isset($data[$index3])){
					if(!isset($data[$index3][$wuxing[$k+3]])){
						$data[$index3][$wuxing[$k+3]] = 0;
					}
					$flat = 1;
					$data[$index3][$wuxing[$k+3]]++;
				}
			}			

			$index2 = implode("-",array($wuxing[$k], $wuxing[$k+1]));
			if(isset($data[$index2])&&$flat==0){
				if(!isset($data[$index2][$wuxing[$k+2]])){
					$data[$index2][$wuxing[$k+2]] = 0;
				}
				$data[$index2][$wuxing[$k+2]]++;
			}
		}
	}
	
	foreach ($data as $key => &$value) {
		arsort($value);
	}

	$tmp = array();
	foreach ($data as $index => $vals) {
		foreach ($vals as $pos => $nums) { 
			if(!isset($tmp[$index])){
				$tmp[$index] = array();
			}
			$tmp[$index][] = "路线".$index."-".$pos."出现".$nums.'次';
		}
	}
	foreach ($tmp as $index => $value) {
		$tmp[$index] = implode("、", $value);
	}

	return $tmp;
}