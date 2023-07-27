<?php

function allSSQData($sort = 'asc'){
	global $dosql;

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid $sort");
	$data = array();
	while($row = $dosql->GetArray()){
		$data[$row['cp_dayid']] = array();
		$data[$row['cp_dayid']] = explode(",", $row['red_num']);
	}
	return $data;
}

function missCount(){
	global $dosql, $allSSQ;

	$data = array();
	foreach ($allSSQ as $cp_dayid => $reds) {
		$curmiss = getCurMiss($cp_dayid);
		$miss = implode(":", $curmiss['cool_hot']);
		if(!isset($data[$miss])){
			$data[$miss] = 0;
		}
		$data[$miss]++;
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $miss => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $miss;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}

	return $result;
}

function bigsmallCount(){
	global $dosql, $allSSQ;

	$data = array();
	foreach ($allSSQ as $cp_dayid => $reds) {
		$redarr = array(0=>0, 1=>0);
		foreach ($reds as $key => $red) {
			if($red > 16)
				$redarr[0]++;
			else
				$redarr[1]++;
		}
		$redarr = implode(":", $redarr);
		if(!isset($data[$redarr])){
			$data[$redarr] = 0;
		}
		$data[$redarr]++;
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}

function oddevenCount(){
	global $dosql, $allSSQ;

	$data = array();
	foreach ($allSSQ as $cp_dayid => $reds) {
		$redarr = array(0=>0, 1=>0);
		foreach ($reds as $key => $red) {
			if($red % 2 == 1)
				$redarr[0]++;
			else
				$redarr[1]++;
		}
		$redarr = implode(":", $redarr);
		if(!isset($data[$redarr])){
			$data[$redarr] = 0;
		}
		$data[$redarr]++;
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}

function primenumCount(){
	global $dosql, $allSSQ;

	$data = array();
    $prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);
	foreach ($allSSQ as $cp_dayid => $reds) {
		$redarr = array(0=>0, 1=>0);
		foreach ($reds as $key => $red) {
			if(in_array($red, $prime))
				$redarr[0]++;
			else
				$redarr[1]++;
		}
		$redarr = implode(":", $redarr);
		if(!isset($data[$redarr])){
			$data[$redarr] = 0;
		}
		$data[$redarr]++;
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}

function redareaCount(){
	global $dosql, $allSSQ;

	$data = array();
	foreach ($allSSQ as $cp_dayid => $reds) {
		$redarr = array(0=>0, 1=>0, 2=>0);
		foreach ($reds as $key => $red) {
			if($red >= 1 && $red <= 11)
				$redarr[0]++;
			else if($red >= 12 && $red <= 22)
				$redarr[1]++;
			else
				$redarr[2]++;
		}
		$redarr = implode(":", $redarr);
		if(!isset($data[$redarr])){
			$data[$redarr] = 0;
		}
		$data[$redarr]++;
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}

function repeatCount(){
	global $dosql, $allSSQ;

	$data = array(0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
	// $allSSQ = array_reverse($allSSQ);
	foreach ($allSSQ as $cp_dayid => $reds) {
		$one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<".$cp_dayid);
		if(isset($one['id'])){
			$before_reds = explode(',', $one['red_num']);
			$num = 0;
			foreach ($reds as $key => $red) {
				if(in_array($red, $before_reds)){
					$num++;
				}
			}
			$data[$num]++;
		}
	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}

function tail43Count(){
	global $dosql, $allSSQ;

	$data = array();
	$tail4 = array(1, 2, 3);
	foreach ($allSSQ as $cp_dayid => $reds) {
		$redarr = array(0=>0, 1=>0, 2=>0);
		foreach ($reds as $key => $red) {
			$tail = $red % 10;
			if(in_array($tail, $tail4))
				$redarr[0]++;
			else
				$redarr[1]++;
		}
		$redarr = implode(":", $redarr);
		if(!isset($data[$redarr])){
			$data[$redarr] = 0;
		}
		$data[$redarr]++;

	}
	arsort($data);

	$sum = array_sum($data);
	$result = array();
	foreach ($data as $index => $count) {
		$tmp = array();
		$percent = round($count / $sum, 4) * 100 . '%';
		$tmp['index']   = $index;
		$tmp['count']   = $count;
		$tmp['percent'] = $percent;
		$tmp['total']   = $sum;
		$result[] = $tmp;
	}
	return $result;
}