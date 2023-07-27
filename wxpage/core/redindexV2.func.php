<?php

function SSQ500W($cp_dayid, $tabname, $limitnum=20){
	global $dosql;

	$total=0;
	$newSSQ = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_500w{$tabname}` WHERE cp_dayid='$cp_dayid'");
	while($row = $dosql->GetArray()){
		$tmp = explode("+", $row['ssq']);

		$reds = $tmp[0];

		$blue = getWinBlue($cp_dayid);

		$ssq = $reds . '+' . $blue;
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_500w{$tabname}` SET ssq='$ssq' WHERE id=".$row['id']);
		$total++;
		$newSSQ[] = $ssq;
	}

	shuffle($newSSQ);
	$newSSQ = array_slice($newSSQ, 0, $limitnum);

	$allSSQ = splitArray($newSSQ, ceil(count($newSSQ)/5));

	$newSSQ2 = array();
	foreach ($allSSQ as $key => $tmpSSq) {
		$blue = getWinBlue($cp_dayid);
		foreach ($tmpSSq as $ssq) {

			$tmp = explode("+", $ssq);

			$reds = $tmp[0];

			$ssq = $reds . '+' . $blue;
			$newSSQ2[] = $ssq;
		}
	}

	// shuffle($newSSQ);
	foreach ($newSSQ2 as $k => $red) {
		$tmp = $k+1;

		if($tmp % 5 == 0){
			echo $red;
			echo "<br/>";
			echo "<br/>";
		}else{
			echo $red;
			echo "<br/>";
		}
	}
}

function SSQ500W_Style($cp_dayid, $tabname, $limitnum=20){
	global $dosql;

	$total=0;
	$newSSQ = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_500w{$tabname}` WHERE cp_dayid='$cp_dayid' AND status='0'");
	while($row = $dosql->GetArray()){
		$tmp = explode("+", $row['ssq']);

		$reds = $tmp[0];

		$blue = getWinBlue($cp_dayid);

		$ssq = $reds . '+' . $blue;
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_500w{$tabname}` SET ssq='$ssq' WHERE id=".$row['id']);
		$total++;
		$newSSQ[] = $ssq;
	}

	shuffle($newSSQ);
	$newSSQ = array_slice($newSSQ, 0, $limitnum);

	$allSSQ = splitArray($newSSQ, ceil(count($newSSQ)/5));

	$newSSQ2 = array();
	foreach ($allSSQ as $key => $tmpSSq) {
		$blue = getWinBlue($cp_dayid);
		foreach ($tmpSSq as $ssq) {

			$tmp = explode("+", $ssq);

			$reds = $tmp[0];
			$reds = explode('.', $reds);

			$text = '';
			foreach ($reds as $red) {
			    $text .= '<span class="red_ball active">'.$red.'</span>';   
			}
			$text .= '<span class="blue_ball active">'.$blue.'</span>';

			$newSSQ2[] = $text;
		}
	}

	// shuffle($newSSQ);
	foreach ($newSSQ2 as $k => $red) {
		$tmp = $k+1;

		if($tmp % 5 == 0){
			echo $red;
			echo "<br/>";
			echo "<br/>";
		}else{
			echo $red;
			echo "<br/>";
		}
	}
}

function RandBlue($maxid='', $num = 2, $bluearr = array()){
	if(count($bluearr) < $num){
		$blue = getWinBlue($maxid);
		if(!in_array($blue, $bluearr)) $bluearr[] = $blue;

		return RandBlue($maxid, $num, $bluearr);
	}

	return $bluearr;
}

/**
 * 
 * 把数组按指定的个数分隔
 * @param array $array 要分割的数组
 * @param int $groupNum 分的组数
 */
function splitArray($array, $groupNum){
    if(empty($array)) return array();

    //数组的总长度
    $allLength = count($array);

    //个数
    $groupNum = intval($groupNum);

    //开始位置
    $start = 0;

    //分成的数组中元素的个数
    $enum = (int)($allLength/$groupNum);

    //结果集
    $result = array();

    if($enum > 0){
        //被分数组中 能整除 分成数组中元素个数 的部分
        $firstLength = $enum * $groupNum;
        $firstArray = array();
        for($i=0; $i<$firstLength; $i++){
            array_push($firstArray, $array[$i]);
            unset($array[$i]);
        }
        for($i=0; $i<$groupNum; $i++){

            //从原数组中的指定开始位置和长度 截取元素放到新的数组中
            $result[] = array_slice($firstArray, $start, $enum);

            //开始位置加上累加元素的个数
            $start += $enum;
        }
        //数组剩余部分分别加到结果集的前几项中
        $secondLength = $allLength - $firstLength;
        for($i=0; $i<$secondLength; $i++){
            array_push($result[$i], $array[$i + $firstLength]);
        }
    }else{
        for($i=0; $i<$allLength; $i++){
            $result[] = array_slice($array, $i, 1);
        }
    }
    return $result;
}

function RedMatchBlue($cp_dayid, $num = 1){
	$blue = getWinBlue($cp_dayid);

}

function getWinBlue($cp_dayid, $hasrest = false, $hasmatch=false){
	global $dosql;

	$brow = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_blue` WHERE cp_dayid='$cp_dayid'");

	if($hasrest && !isset($brow['id'])) return false;

	if($hasmatch && isset($brow['bluematch'])){
		if($brow['bluematch']){
			return true;
		}else{
			return false;
		}
	}

	if(!isset($brow['id'])) return false;

	$bluenum = $brow['bluenum'];
	$blueinfo = unserialize($brow['blueinfo']);
	$gailvarr = unserialize($brow['gailvarr']);

	if(count($blueinfo) > $bluenum){
		foreach ($blueinfo as $pos => $blue) {
			if($pos > $bluenum) unset($blueinfo[$pos]);
		}

		foreach ($gailvarr as $pos => $blue) {
			if($pos > $bluenum) unset($gailvarr[$pos]);
		}
	}

	$gailv = array();
	foreach ($gailvarr as $index => $num) {
		for ($i=1; $i < $num+1; $i++) { 
			$gailv[] = $index;
		}
	}
	shuffle($gailv);
	$gailv_index = $gailv[rand(0, count($gailv)-1)];
	$blue = $blueinfo[$gailv_index];
	
	return $blue;
}

function RedLocationKill2($cp_dayid=''){
	global $dosql;

	if(!empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT 2");
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 2");
	}
	$ssqRed = array();
	$ssqIds = array();
	$index = 0;
	while($row = $dosql->GetArray()){
		$ssqIds[$index] = $row['cp_dayid'];
		$ssqRed[$index] = explode(",", $row['red_num']);
		$index++;
	}

	$ssq_2 = $ssqRed[1];
	$ssq_1 = $ssqRed[0];

	$killtail = array();
	foreach ($ssq_2 as $ridx => $num) {
		if(!isset($killtail[$ridx])) $killtail[$ridx] = array();

		$redtail_2 = $num % 10;
		$redtail_1 = $ssq_1[$ridx] % 10;

		$plus_tail  = ($redtail_2 + $redtail_1) % 10;
		$minus_tail = abs($redtail_2 - $redtail_1) % 10;
		if(!in_array($plus_tail, $killtail[$ridx])) array_push($killtail[$ridx], $plus_tail);
		if(!in_array($minus_tail, $killtail[$ridx])) array_push($killtail[$ridx], $minus_tail);
	}

	return $killtail;

	
}


function RedLocationKill($cp_dayid=''){
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
	$ssqRed = array();
	$ssqIds = array();
	$index = 0;
	while($row = $dosql->GetArray()){
		$ssqIds[$index] = $row['cp_dayid'];
		$ssqRed[$index] = explode(",", $row['red_num']);
		$index++;
	}

	$result = array();
	foreach ($ssqRed as $index => $reds) {
		if(!isset($ssqRed[$index-2]) || !isset($ssqRed[$index-1])) continue;

		$ssq_2 = $ssqRed[$index-2];
		$ssq_1 = $ssqRed[$index-1];

		$killtail = array();
		foreach ($ssq_2 as $ridx => $num) {
			if(!isset($killtail[$ridx])) $killtail[$ridx] = array();

			$redtail_2 = $num % 10;
			$redtail_1 = $ssq_1[$ridx] % 10;

			$plus_tail  = ($redtail_2 + $redtail_1) % 10;
			$minus_tail = abs($redtail_2 - $redtail_1) % 10;
			if(!in_array($plus_tail, $killtail[$ridx])) array_push($killtail[$ridx], $plus_tail);
			if(!in_array($minus_tail, $killtail[$ridx])) array_push($killtail[$ridx], $minus_tail);
		}

		$killrest = array();
		foreach ($reds as $ridx => $red) {
			$redtail = $red % 10;
			if(in_array($redtail, $killtail[$ridx]))
				$killrest[$ridx] = 0;
			else
				$killrest[$ridx] = 1;
		}

		$result[$index]['id']       = $ssqIds[$index];
		$result[$index]['red']      = $reds;
		$result[$index]['killtail'] = $killtail;
		$result[$index]['killrest'] = $killrest;
	}

	return $result;
}


function RedSectionKill($ssqRed=array()){
	global $dosql;

	if(empty($ssqRed)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
		$ssqRed = array();
		$ssqIds = array();
		$index = 0;
		while($row = $dosql->GetArray()){
			// $ssqIds[$index] = $row['cp_dayid'];
			$ssqRed[$index] = explode(",", $row['red_num']);
			$index++;
		}
	}

	$redSection = array(
		1 => array(1, 5),//5
		2 => array(6, 11),//6
		3 => array(12, 16),//5
		4 => array(17, 22),//6
		5 => array(23, 27),//5
		6 => array(28, 33),//6
	);

	$sectionAssoc = array('05', '06', '11', '12', '16', '17', '22', '23', '27', '28');

	$result = array();
	foreach ($ssqRed as $index => $reds) {
		$sectionkill = array();
		$sectionHas  = 0;
		$sectionNum  = 0;
		$thisSection = array(1=>1, 2=>1, 3=>1, 4=>1, 5=>1, 6=>1);
		foreach ($reds as $red) {
			foreach ($redSection as $sid => $section) {
				if($thisSection[$sid] == 0) continue;
				if($red >= $section[0] && $red <= $section[1]) $thisSection[$sid] = 0;
			}

			if(in_array($red, $sectionAssoc) && $sectionHas == 0) $sectionHas = 1;
			if(in_array($red, $sectionAssoc)) $sectionNum++;

		}
		// $result[$index]['id']         = $ssqIds[$index];
		$result[$index]['reds']       = $reds;
		$result[$index]['section']    = $thisSection;
		$result[$index]['sectionHas'] = $sectionHas;
		$result[$index]['sectionNum'] = $sectionNum;
	}

	return $result;
}


function Red7CodeWin($ssqRed=array()){
	global $dosql;

	if(empty($ssqRed)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
		$ssqRed = array();
		$ssqIds = array();
		$index = 0;
		while($row = $dosql->GetArray()){
			$ssqIds[$index] = $row['cp_dayid'];
			$ssqRed[$index] = explode(",", $row['red_num']);
			$index++;
		}
	}

	$red7code = array('07', '14', '16', '17', '18', '21', '25', '27', '28', '29');

	$result = array();
	foreach ($ssqRed as $index => $reds) {
		$red7codeHas  = 0;
		$red7codeNum  = 0;
		foreach ($reds as $red) {
			if(in_array($red, $red7code) && $red7codeHas == 0) $red7codeHas = 1;
			if(in_array($red, $red7code)) $red7codeNum++;
		}
		// $result[$index]['id']          = $ssqIds[$index];
		$result[$index]['reds']        = $reds;
		$result[$index]['red7codeHas'] = $red7codeHas;
		$result[$index]['red7codeNum'] = $red7codeNum;
	}

	return $result;
}

//百合500红球指标
function RedIndex22($reds = array(), $redmiss = array(), $killtail = array(), $allSSQ=array(), $blue=''){
	$prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);

	$data = array();
	$data['hotcool']  = array(1=>0, 2=>0, 3=>0);
	$data['oddeven']  = array(1=>0, 2=>0);
	$data['prime']    = array(1=>0, 2=>0);
	$data['bigsmall'] = array(1=>0, 2=>0);
	$data['redarea']  = array(1=>0, 2=>0, 3=>0);
	$data['red012']   = array(0=>0, 1=>0, 2=>0);
	$data['tail012']  = array(0=>0, 1=>0, 2=>0);
	$data['tailbigs'] = array(1=>0, 2=>0);

	$data['killtail'] = 0;
	$data['ac']       = 0;
	$data['sum']      = 0;
	$data['lianhao']  = 0;
	$data['dvalue']   = 0;
	$data['tailsum']  = 0;
	$data['sumtail']  = 0;
	$data['tailnum']  = 0;

	foreach ($reds as $index => $red) {
		$tail = $red % 10;

		$red = trim($red);

		if(isset($redmiss[$red])){
			$tmp_miss = $redmiss[$red];
			if($tmp_miss >= 0 && $tmp_miss <= 4){
				$data['hotcool'][1]++;
			}else if($tmp_miss >= 5 && $tmp_miss <= 9){
				$data['hotcool'][2]++;
			}else if($tmp_miss > 9){
				$data['hotcool'][3]++;
			}
		}

		if(isset($killtail[$index]) && !in_array($tail, $killtail[$index])){
			$data['killtail']++; //定位杀对
		}

		$red % 2 == 1 && $data['oddeven'][1]++;
	    $red % 2 == 0 && $data['oddeven'][2]++;

	    if(in_array($red, $prime)){
	    	$data['prime'][1]++;
	    }else{
	    	$data['prime'][2]++;
	    }

	    $red > 16 && $data['bigsmall'][1]++;
	    $red < 17 && $data['bigsmall'][2]++;

	    $red < 12 && $data['redarea'][1]++;
	    $red > 11 && $red < 23 && $data['redarea'][2]++;
		$red > 22 && $data['redarea'][3]++;

	    $red % 3 == 0 && $data['red012'][0]++;
	    $red % 3 == 1 && $data['red012'][1]++;
	    $red % 3 == 2 && $data['red012'][2]++;

	    $tail % 3 == 0 && $data['tail012'][0]++;
	    $tail % 3 == 1 && $data['tail012'][1]++;
	    $tail % 3 == 2 && $data['tail012'][2]++;

	    $tail > 4 && $data['tailbigs'][1]++;
	    $tail < 5 && $data['tailbigs'][2]++;

	    if(!isset($tailarr)){
	    	$tailarr = array();
	    }

	    !in_array($tail, $tailarr) && $tailarr[] = $tail;
		$data['tailsum'] += $tail;
		$data['sumtail'] = $data['tailsum'] % 10;

		$data['tailnum'] = $index == 5 ? count($tailarr) : 0;
	}

	$wins = array();
	foreach ($allSSQ as $cpdid => $row) {
		$jjarr = array_intersect($reds, $row['red_num']);
		$jjnum = count($jjarr);
		$bluewin = $blue == $row['blue_num'] ? 1 : 0;
		if($bluewin == 1 && $jjnum >= 3  || $bluewin == 0 && $jjnum > 3){
			$arrindex = $jjnum . '+' . $bluewin;
			if(!isset($wins[$arrindex])){
				$wins[$arrindex] = 0;
			}
			$wins[$arrindex]++;
		}
	}

	$data['wins']    = $wins;
	
	$data['ac']      = getAC($reds);
	$data['sum']     = array_sum($reds);
	$data['lianhao'] = 0;
	$data['dvalue']  = $reds[5]-$reds[0];

	// 7关系码
	$tmp7 = Red7CodeWin(array($reds));
	$tmp7 = current($tmp7);
	$data['7guanxima'] = $tmp7['red7codeNum'];

	// 6区间码命中
	$tmp6 = RedSectionKill(array($reds));
	$tmp6 = current($tmp6);
	$data['6qujianma'] = $tmp6['sectionNum'];

	foreach ($data as $key => &$v) {
		if($key == 'wins') continue;
		if(is_array($v)) $v = implode(":", $v);
	}

	return $data;
}