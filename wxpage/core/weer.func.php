<?php

require_once(dirname(__FILE__).'/weer.config.php');

function WeerColor($num){
	if($num >= 0 && $num <= 10){
		return 'red';
	}else if($num >= 11 && $num <= 18){
		return 'orange';
	}else{
		return 'blue';
	}
}

/**
 * 微尔算法八步各指标遗漏统计
 */
function WeerMissing($cp_dayid = 0){
	global $dosql;

	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[] = $i;
	}

	$sql = "SELECT * FROM `#@__caipiao_history` ";
	if(!empty($cp_dayid)){
		$sql .= " WHERE cp_dayid<$cp_dayid";
	}
	$sql .= " ORDER BY cp_dayid DESC LIMIT 200";

	$resIndex = 'index_' . rand(10000, 99999);

	$dosql->Execute($sql, $resIndex);

	$redlist = array();
	while($row = $dosql->GetArray($resIndex)){
		$red_num = explode(',', $row['red_num']);
		$tmp = array();
		$tmp['cp_dayid'] = $row['cp_dayid'];
		$tmp['red_num']  = $red_num;
		$tmp['redweer']  = GetWeerData($red_num);

		$redlist[] = $tmp;
	}

	$weer_missing = array();
	foreach (array(1,2,3,5,6,7,8) as $arrid) {
		$weer_key = 'weer'.$arrid;
		$weer_missing[$weer_key] = array();

		$wee123_bli = Weer8Ratio($arrid);
		$wee123_cfg = GetWeerCfg($arrid);
		if($arrid <= 3){
			$wee123_cfg = array_keys($wee123_cfg);
		}else{
			$wee123_cfg = array_values($wee123_cfg);
			$wee123_cfg = $wee123_cfg[0];
		}
		foreach ($wee123_cfg as $pos) {
			if($arrid <= 3){
				$weer_missing[$weer_key][$pos] = array();
				foreach ($wee123_bli as $bli) {
					if(!isset($weer_missing[$weer_key][$pos][$bli])){
						$weer_missing[$weer_key][$pos][$bli] = 0;
					}
					foreach ($redlist as $v) {
						$cur_bli = $v['redweer'][$weer_key][$pos];
						if($cur_bli == $bli) break;

						$weer_missing[$weer_key][$pos][$bli]++;
					}
				}
			}else{
				$weer_missing[$weer_key][$pos] = 0;
				foreach ($redlist as $v) {
					$cur_bli = $v['redweer'][$weer_key]['code'];
					if($cur_bli == $pos) break;

					$weer_missing[$weer_key][$pos]++;
				}
			}
		}
	}

	$redlist_tmp = array_slice($redlist, 0, 10);
	foreach (array(4) as $arrid) {
		$weer_key = 'weer'.$arrid;
		$weer_missing[$weer_key] = array();

		$wee123_bli = Weer8Ratio($arrid);
		$wee123_cfg = GetWeerCfg($arrid);
		$wee123_cfg = array_keys($wee123_cfg);

		foreach ($wee123_cfg as $pos) {
			$weer_missing[$weer_key][$pos] = array();
			foreach ($wee123_bli as $bli) {
				if(!isset($weer_missing[$weer_key][$pos][$bli])){
					$weer_missing[$weer_key][$pos][$bli] = 0;
				}
				foreach ($redlist_tmp as $v) {
					$cur_bli = $v['redweer'][$weer_key][$pos];
					if($cur_bli == $bli){
						$weer_missing[$weer_key][$pos][$bli]++;
					}
				}
			}
		}
	}

	$miss = array();
	foreach (array(1,2,3) as $arrid) {
		$weer_key = 'weer'.$arrid;
		$missarr = $weer_missing[$weer_key];

		$miss[$weer_key] = array();
		foreach ($missarr as $pos => $arr) {
			$miss[$weer_key][$pos] = array(
				'hot'  => array('num'=>0, 'misssum'=>0),
				'warm' => array('num'=>0, 'misssum'=>0),
				'cool' => array('num'=>0, 'misssum'=>0),
			);
			foreach ($arr as $v) {
				if($v>=0 && $v<=10){
					$miss[$weer_key][$pos]['hot']['num']++;
					$miss[$weer_key][$pos]['hot']['misssum'] += $v;
				}else if($v>=11 && $v<=18){
					$miss[$weer_key][$pos]['warm']['num']++;
					$miss[$weer_key][$pos]['warm']['misssum'] += $v;
				}else if($v>=19){
					$miss[$weer_key][$pos]['cool']['num']++;
					$miss[$weer_key][$pos]['cool']['misssum'] += $v;
				}
			}
		}
	}
	$weer_missing['miss'] = $miss;

	return $weer_missing;
}