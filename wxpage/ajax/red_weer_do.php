<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/weer.config.php';
require_once dirname(__FILE__) . '/../core/redindexV2.func.php';

/*LoginCheck();

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}*/

if($action == 'red_weer'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_weer`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cydayid = $row['cp_dayid'];

		//开奖红球数组
		$red_num = explode(',', $row['red_num']);

		$weer_data = GetWeerData($red_num);
		foreach ($weer_data as $weindex => &$arr) {
			$arr = serialize($arr);
		}

		$sql = "INSERT INTO `#@__caipiao_weer` 
		(cp_dayid, opencode, red_num, weer1, weer2, weer3, weer4, weer5, weer6, weer7, weer8) 
		VALUES 
		('".$row['cp_dayid']."', '".$row['opencode']."', '".$row['red_num']."', '".$weer_data['weer1']."', '".$weer_data['weer2']."', '".$weer_data['weer3']."', '".$weer_data['weer4']."', '".$weer_data['weer5']."', '".$weer_data['weer6']."', '".$weer_data['weer7']."', '".$weer_data['weer8']."')";
		
		$dosql->ExecNoneQuery($sql);
	}
	exit('1');
}

else if($action == 'weerlist'){
	$cpnum = isset($cpnum) ? $cpnum : 10;

	$data = array();
	if(isset($cp_dayid) && !empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_weer` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC LIMIT {$cpnum}");
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_weer` ORDER BY cp_dayid DESC LIMIT {$cpnum}");
	}
	while($row = $dosql->GetArray()){
		$tmp = array();
		$tmp['cp_dayid'] = $row['cp_dayid'];
		$tmp['opencode'] = $row['opencode'];
		$tmp['weer1']    = mb_unserialize($row['weer1']);
		$tmp['weer2']    = mb_unserialize($row['weer2']);
		$tmp['weer3']    = mb_unserialize($row['weer3']);
		$tmp['weer4']    = mb_unserialize($row['weer4']);
		$tmp['weer5']    = mb_unserialize($row['weer5']);
		$tmp['weer6']    = mb_unserialize($row['weer6']);
		$tmp['weer7']    = mb_unserialize($row['weer7']);
		$tmp['weer8']    = mb_unserialize($row['weer8']);

		$data[] = $tmp;
	}
	$data = array_reverse($data);

	$rest = array('table_th'=>'', 'table_td'=>'');
	if($weerindex){
		$weerCfg1 = GetWeerCfg($weerindex);
		$weerCfg1 = array_keys($weerCfg1);

		if(!in_array($weerindex, array(5,6,7,8))){
			$rest['table_th'] = '<tr><th style="color: blue;">期号</th><th style="color: red;">开奖号</th>';
			foreach ($weerCfg1 as $v) {
				$v = str_replace('-', '', $v);
				$rest['table_th'] .= '<th>'.$v.'位</th>';
			}
			$rest['table_th'] .= '</tr>';
		}else{
			$cfg = array(
				5 => array('12位', '34位', '56位'),
				6 => array('大和值奇偶', '小和值奇偶'),
				7 => array('首尾和值的012路', '首尾间距012路', '尾数和值012路'),
				8 => array('12位', '34位', '56位'),
			);
			$weerCfg1 = $cfg[$weerindex];
			$rest['table_th'] = '<tr><th style="color: blue;">期号</th><th style="color: red;">开奖号</th>';
			foreach ($weerCfg1 as $v) {
				$rest['table_th'] .= '<th>'.$v.'</th>';
			}
			$rest['table_th'] .= '</tr>';
		}

		if(!in_array($weerindex, array(5,6,7,8))){
			foreach ($data as $v) {
				$rest['table_td'] .= '<tr><td style="color: blue;">'.$v['cp_dayid'].'</td><td style="color: red;">'.$v['opencode'].'</td>';
				foreach ($v['weer'.$weerindex] as $vv) {
					$rest['table_td'] .= '<td>'.$vv.'</td>';
				}
			}
		}else{
			$cfg = array(
				5 => array('12', '34', '56'),
				6 => array('big', 'small'),
				7 => array('sum1', 'sum2', 'sum3'),
				8 => array('12', '34', '56'),
			);
			$weerVal = $cfg[$weerindex];
			foreach ($data as $v) {
				$rest['table_td'] .= '<tr><td style="color: blue;">'.$v['cp_dayid'].'</td><td style="color: red;">'.$v['opencode'].'</td>';
				foreach ($weerVal as $vv) {
					$rest['table_td'] .= '<td>'.$v['weer'.$weerindex][$vv].'</td>';
				}
			}
		}

		exit(json_encode($rest));
	}

	// echo $rest['table-td'];
}
 
else if($action == 'vipcode'){
	if($code){
		$maxid = maxDayid();
		$maxid = nextCpDayId($maxid);

		$ipinfo = GetIP();

		$result = array();

		$curw = date("w");

		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE code='$code' ORDER BY id DESC");
		if(!isset($row['id'])){
			$result['errcode'] = 2;
			$result['errmsg']  = "领取码无效！";
			exit(json_encode($result));
		}
		$curssq = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='{$row['cp_dayid']}'");
		if(isset($curssq['id'])){
			$maxid = $curssq['cp_dayid'];
		}else{
			$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND code='$code'");
			if(!isset($row['id'])){
				$result['errcode'] = 2;
				$result['errmsg']  = "领取码无效！";
				exit(json_encode($result));
			}
		}

		$bluedata = $dosql->GetOne("SELECT * FROM `lz_caipiao_weermy_blue` WHERE cp_dayid='$maxid'");
		$bluedanma = array();
		if(isset($bluedata['blueinfo'])){
			$blueinfo = unserialize($bluedata['blueinfo']);
			$blueinfo = array_slice($blueinfo, 0, 5);
			$bluedanma = $blueinfo;
		}

		$cfg = array(
			'ssq' => $maxid.'期推荐号码'.($bluedanma ? "【蓝号推荐：".implode(">", $bluedanma)."】" : ''),
		);

		if($row['status'] == 1){
			$myssq = explode("|", $row['ssqinfo']);
			$rest = array('table_th'=>'', 'table_td'=>'');
			$rest['table_th'] = '<tr>';
			foreach ($cfg as $field => $name) {
				$rest['table_th'] .= '<th>'.$name.'</th>';
			}
			$rest['table_th'] .= '</tr>';

			$cfg = array_keys($cfg);
			foreach ($myssq as $v) {
				$wininfo = '';
				if(isset($curssq['id'])){
					$tmpv = explode("+", $v);
					$tmpvreds = explode(".", $tmpv[0]);
					$tmpvblue = $tmpv[1];

					$curssqreds = explode(',', $curssq['red_num']);
					$red_win_num = count(array_intersect($tmpvreds, $curssqreds));

					$blue_win_num = $curssq['blue_num'] == $tmpvblue ? 1 : 0;

					if($blue_win_num == 1 && $red_win_num < 3){
						$wininfo = '【恭喜您，中得六等奖】';
					}else if(($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)){
						$wininfo = '【恭喜您，中得五等奖】';
					}else if(($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)){
						$wininfo = '【恭喜您，中得四等奖】';
					}else if($red_win_num == 5 && $blue_win_num == 1){
						$wininfo = '【恭喜您，中得三等奖】';
					}else if($red_win_num == 6 && $blue_win_num == 0){
						$wininfo = '【恭喜您，中得二等奖】';
					}else if($red_win_num == 6 && $blue_win_num == 1){
						$wininfo = '【恭喜您，中得一等奖】';
					}
				}
				$rest['table_td'] .= '<tr>';
				foreach ($cfg as $vv) {
					$class = '';
					if($vv == 'ssq'){
						$class = 'style="color:#f36;"';
					}
					
					$rest['table_td'] .= '<td '.$class.'>'.$v.$wininfo.'</td>';
				}
				$rest['table_td'] .= '</tr>';
			}

			$result = array();
			$result['errcode'] = 1;
			$result['errmsg'] = $rest;

			exit(json_encode($result));
		}

		$ssqRest0 = array();
		$ssqRest1 = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_500w` WHERE cp_dayid='$maxid'");
		while($list = $dosql->GetArray()){
			if($list['status'] == 0){
				$ssqRest0[] = $list['ssq'];
			}else{
				$ssqRest1[] = $list['ssq'];
			}
		}

		$hasnum0 = count($ssqRest0);
		$hasnum1 = count($ssqRest1);

		$getssqnum = 2;
		if($row['codetype'] == 2){
			$getssqnum = 5;
		}else if($row['codetype'] == 3){
			$getssqnum = 10;
		}

		$need_update = 0;
		$myssq = array();
		if($hasnum0 >= $getssqnum){
			shuffle($ssqRest0);
			$myssq = array_slice($ssqRest0, 0, $getssqnum);
			$need_update = 1;
		}else if($hasnum0 > 0 && $hasnum0 < $getssqnum){
			shuffle($ssqRest0);
			$myssq = $ssqRest0;

			shuffle($ssqRest1);
			$myssq2 = array_slice($ssqRest1, 0, $getssqnum-$hasnum0);

			$myssq = array_merge($myssq, $myssq2);
			$need_update = 1;
		}else{
			shuffle($ssqRest1);
			$myssq = array_slice($ssqRest1, 0, $getssqnum);
		}

		if($need_update){
			foreach ($myssq as $ssq) {
				$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_500w` SET status='1' WHERE cp_dayid='$maxid' AND ssq='$ssq'");
			}
		}

		if(!$myssq){
			$result['errcode'] = 2;
			$result['errmsg']  = "暂无号码可领取！";
			exit(json_encode($result));
		}

		//重新分配蓝号
		$rest = getWinBlue($maxid, true);
		if($rest){
			$bluematch = getWinBlue($maxid, false, true);
			if($bluematch){
				$groupnum = ceil(count($myssq)/5);
				$bluearr = RandBlue($maxid, $groupnum, $bluearr=array());

				$tmpSSQ = splitArray($myssq, $groupnum);

				$newssq = array();
				foreach ($tmpSSQ as $key => $tmpssq) {
					$blue = $bluearr[$key];

					foreach ($tmpssq as $ssq) {
						$tmp = explode("+", $ssq);

						$reds = $tmp[0];

						$ssq = $reds . '+' . $blue;
						$newssq[] = $ssq;
					}
				}
				$myssq = $newssq;
			}else{
				$newssq = array();
				foreach ($myssq as $ssq) {
					$tmp = explode("+", $ssq);

					$reds = $tmp[0];

					$blue = getWinBlue($maxid);

					$ssq = $reds . '+' . $blue;
					$newssq[] = $ssq;
				}
				$myssq = $newssq;
			}
		}

		$ssqinfo = implode("|", $myssq);
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_code` SET status='1', usetime='".time()."', ssqinfo='".$ssqinfo."', ipinfo='".$ipinfo."' WHERE cp_dayid='$maxid' AND code='$code'");

		$rest = array('table_th'=>'', 'table_td'=>'');
		$rest['table_th'] = '<tr>';
		foreach ($cfg as $field => $name) {
			$rest['table_th'] .= '<th>'.$name.'</th>';
		}
		$rest['table_th'] .= '</tr>';

		$cfg = array_keys($cfg);
		foreach ($myssq as $v) {
			$rest['table_td'] .= '<tr>';
			foreach ($cfg as $vv) {
				$class = '';
				if($vv == 'ssq'){
					$class = 'style="color:#f36;"';
				}
				
				$rest['table_td'] .= '<td '.$class.'>'.$v.'</td>';
			}
			$rest['table_td'] .= '</tr>';
		}

		$result = array();
		$result['errcode'] = 1;
		$result['errmsg'] = $rest;

		exit(json_encode($result));
	}
}


else if($action == 'code'){
	if($code){
		$maxid = maxDayid();
		$maxid = nextCpDayId($maxid);

		$cfg = array(
			'ssq' => $maxid.'期推荐号码',
		);

		$ipinfo = GetIP();

		$result = array();

		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND code='$code'");
		if(!isset($row['id'])){
			$result['errcode'] = 2;
			$result['errmsg']  = "领取码无效！";
			exit(json_encode($result));
		}

		if($row['status'] == 1){
			$myssq = explode("|", $row['ssqinfo']);
			$rest = array('table_th'=>'', 'table_td'=>'');
			$rest['table_th'] = '<tr>';
			foreach ($cfg as $field => $name) {
				$rest['table_th'] .= '<th>'.$name.'</th>';
			}
			$rest['table_th'] .= '</tr>';

			$cfg = array_keys($cfg);
			foreach ($myssq as $v) {
				$rest['table_td'] .= '<tr>';
				foreach ($cfg as $vv) {
					$class = '';
					if($vv == 'ssq'){
						$class = 'style="color:#f36;"';
					}
					
					$rest['table_td'] .= '<td '.$class.'>'.$v.'</td>';
				}
				$rest['table_td'] .= '</tr>';
			}

			$result = array();
			$result['errcode'] = 1;
			$result['errmsg'] = $rest;

			exit(json_encode($result));

			// $result['errcode'] = 2;
			// $result['errmsg']  = "领取码已使用！";
			// exit(json_encode($result));
		}

		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND ipinfo='$ipinfo'");
		if(isset($row['id'])){
			$result['errcode'] = 2;
			$result['errmsg']  = "您无法再次领取！";
			exit(json_encode($result));
		}

		$ssqRest0 = array();
		$ssqRest1 = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_500w` WHERE cp_dayid='$maxid'");
		while($list = $dosql->GetArray()){
			if($list['status'] == 0){
				$ssqRest0[] = $list['ssq'];
			}else{
				$ssqRest1[] = $list['ssq'];
			}
		}

		$hasnum0 = count($ssqRest0);
		$hasnum1 = count($ssqRest1);

		$need_update = 0;
		$myssq = array();
		if($hasnum0 >= 5){
			shuffle($ssqRest0);
			$myssq = array_slice($ssqRest0, 0, 5);
			$need_update = 1;
		}else if($hasnum0 > 0 && $hasnum0 < 5){
			shuffle($ssqRest0);
			$myssq = $ssqRest0;

			shuffle($ssqRest1);
			$myssq2 = array_slice($ssqRest1, 0, 5-$hasnum0);

			$myssq = array_merge($myssq, $myssq2);
			$need_update = 1;
		}else{
			shuffle($ssqRest1);
			$myssq = array_slice($ssqRest1, 0, 5);
		}

		if($need_update){
			foreach ($myssq as $ssq) {
				$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_500w` SET status='1' WHERE cp_dayid='$maxid' AND ssq='$ssq'");
			}
		}

		if(!$myssq){
			$result['errcode'] = 2;
			$result['errmsg']  = "暂无号码可领取！";
			exit(json_encode($result));
		}

		$ssqinfo = implode("|", $myssq);
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_code` SET status='1', usetime='".time()."', ssqinfo='".$ssqinfo."', ipinfo='".$ipinfo."' WHERE cp_dayid='$maxid' AND code='$code'");

		$rest = array('table_th'=>'', 'table_td'=>'');
		$rest['table_th'] = '<tr>';
		foreach ($cfg as $field => $name) {
			$rest['table_th'] .= '<th>'.$name.'</th>';
		}
		$rest['table_th'] .= '</tr>';

		$cfg = array_keys($cfg);
		foreach ($myssq as $v) {
			$rest['table_td'] .= '<tr>';
			foreach ($cfg as $vv) {
				$class = '';
				if($vv == 'ssq'){
					$class = 'style="color:#f36;"';
				}
				
				$rest['table_td'] .= '<td '.$class.'>'.$v.'</td>';
			}
			$rest['table_td'] .= '</tr>';
		}

		$result = array();
		$result['errcode'] = 1;
		$result['errmsg'] = $rest;

		exit(json_encode($result));
	}
}

else if($action == 'schemes'){
	$cpnum = isset($cpnum) ? $cpnum : 10;

	$data = array();
	if(isset($cp_dayid) && !empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy` WHERE cp_dayid='$cp_dayid'", 'a');
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy` ORDER BY id DESC LIMIT {$cpnum}", 'a');
	}
	
	$schemes = array();
	while($row = $dosql->GetArray('a')){
		$row['posttime'] = date("Y-m-d H:i", $row['updatetime']);

		$ssqnum = $dosql->GetOne("SELECT count(*) as num FROM `#@__caipiao_weermy_cpdata` WHERE sid='{$row['id']}'");
		$row['num'] = "查看（" . $ssqnum['num'] . "）";
	    $schemes[] = $row;
	}

	$cfg = array(
		'id'          => '编号',
		'cp_dayid'    => '期数',
		'scheme_name' => '方案名称',
		'posttime'    => '更新时间',
		'url'         => '方案号码',
	);

	$rest = array('table_th'=>'', 'table_td'=>'');
	$rest['table_th'] = '<tr>';
	foreach ($cfg as $field => $name) {
		$rest['table_th'] .= '<th>'.$name.'</th>';
	}
	$rest['table_th'] .= '</tr>';

	$cfg = array_keys($cfg);
	foreach ($schemes as $v) {
		$rest['table_td'] .= '<tr>';
		foreach ($cfg as $vv) {
			$class = '';
			if($vv == 'id'){
				$class = 'style="color:blue;"';
			}else if($vv == 'cp_dayid'){
				$class = 'style="color:#f36;"';
			}
			if($vv == 'scheme_name'){
				$rest['table_td'] .= '<td '.$class.'><a href="amazeweer_scheme.php?id='.$v['id'].'">'.$v[$vv].'</a></td>';
			}else if($vv == 'url'){
				$rest['table_td'] .= '<td><a href="amazeweer_scheme_ssq.php?id='.$v['id'].'">'.$v['num'].'</a></td>';
			}else{
				$rest['table_td'] .= '<td '.$class.'>'.$v[$vv].'</td>';
			}
		}
		$rest['table_td'] .= '</tr>';
	}

	exit(json_encode($rest));
}

else if($action == 'schemes_ssq_index'){
	$curid = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` WHERE id='$id'");

	$killtail = RedLocationKill2($curid['cp_dayid']);
	$redmiss  = redMissing($curid['cp_dayid']);


	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$curid['cp_dayid']} ORDER BY cp_dayid DESC");
	$allSSQ = array();
	while($row = $dosql->GetArray()){
		$allSSQ[$row['cp_dayid']]['cp_dayid'] = $row['cp_dayid'];
		$allSSQ[$row['cp_dayid']]['red_num'] = explode(",", $row['red_num']);
		$allSSQ[$row['cp_dayid']]['blue_num'] = $row['blue_num'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_cpdata` WHERE sid='$id' AND status=0");
	while($row = $dosql->GetArray()){
		$row['ssq'] = trim($row['ssq']);
		$tmp = explode("+", $row['ssq']);
		// $reds = substr($row['ssq'], 0, -3);
		// $blue = substr($row['ssq'], -2);
		$reds = explode('.', $tmp[0]);
		$blue = $tmp[1];
		
		$ssqindex = RedIndex22($reds, $redmiss, $killtail, $allSSQ, $blue);
		$ssqindex = serialize($ssqindex);
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_cpdata` SET ssqindex='$ssqindex', status='1' WHERE id=".$row['id']);
	}

	exit(1);

}

else if($action == 'schemes_ssq'){
	$cpnum = isset($cpnum) ? $cpnum : 30;

	$data = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_cpdata` WHERE sid='$id' ORDER BY id ASC LIMIT {$cpnum}", 'a');
		

	$curid = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` WHERE id='$id'");

	$schemes = array();
	$index = 1;
	$killtail = RedLocationKill2($curid['cp_dayid']);
	while($row = $dosql->GetArray('a')){
		$tmp = array();
		$tmp['ssq'] = $row['ssq'];
		$tmp['idx'] = $index;

		$tmptmp = explode("+",$row['ssq']);
		// $reds = substr($row['ssq'], 0, -3);
		$reds = explode('.', $tmptmp[0]);

		//热温冷比例
		$missArr = array('hot'=>0, 'warm'=>0, 'cool'=>0);
		$all_red_miss = redMissing($curid['cp_dayid']);

		$killrest = array();
		$tailnum = array();
		foreach ($reds as $ridx => $red) {
			$redtail = $red % 10;
			if(in_array($redtail, $killtail[$ridx]))
				$killrest[$ridx] = 0; //定位杀错
			else
				$killrest[$ridx] = 1; //定位杀对

			if(!in_array($redtail, $tailnum)) $tailnum[] = $redtail;

			$red = trim($red);

			$red < 10 && strlen($red) < 2 && $red = '0'.$red;

			if(isset($all_red_miss[$red])){
				$tmp_miss = $all_red_miss[$red];
				if($tmp_miss >= 0 && $tmp_miss <= 4){
					$missArr['hot']++;
				}else if($tmp_miss >= 5 && $tmp_miss <= 9){
					$missArr['warm']++;
				}else if($tmp_miss > 9){
					$missArr['cool']++;
				}
			}
		}
		
		$tmp['dwkill']   = array_sum($killrest);
		$tmp['hot_cool'] = implode(":", $missArr);
		$tmp['tailnum']  = count($tailnum);

		$dwkillArr = array(4, 5, 6);
		// if(!in_array($tmp['dwkill'], $dwkillArr)) continue;

		// if($missArr['hot']<3) continue;
		// if($missArr['warm']>1) continue;

		// if($missArr['cool']==0 || $missArr['cool']>2) continue;

		// if(count($tailnum) == 5) continue;

	    $schemes[] = $tmp;
	    $index++;
	}

	

	$cfg = array(
		'idx'      => '序号',
		'ssq'      => '号码',
		'dwkill'   => '定位杀',
		'hot_cool' => '热温冷比',
		'tailnum'  => '尾数',
	);

	$rest = array('table_th'=>'', 'table_td'=>'');
	$rest['table_th'] = '<tr>';
	foreach ($cfg as $field => $name) {
		$rest['table_th'] .= '<th>'.$name.'</th>';
	}
	$rest['table_th'] .= '</tr>';

	$cfg = array_keys($cfg);
	foreach ($schemes as $v) {
		$rest['table_td'] .= '<tr>';
		foreach ($cfg as $vv) {
			$class = '';
			if($vv == 'id'){
				$class = 'style="color:blue;"';
			}else if($vv == 'ssq'){
				$class = 'style="color:#f36;"';
			}
			$rest['table_td'] .= '<td '.$class.'>'.$v[$vv].'</td>';
		}
		$rest['table_td'] .= '</tr>';
	}

	exit(json_encode($rest));
}

else if($action == 'weer_scheme_update'){
	$myweercfg = $_POST;
	$id = $myweercfg['id'];

	$blue_comb = array();
	$blue_comb['blue']      = $myweercfg['blue'];
	$blue_comb['blue_comb'] = $myweercfg['blue_comb'];

	$blue_comb = serialize($blue_comb);

	unset($myweercfg['action']);
	unset($myweercfg['id']);
	unset($myweercfg['blue_comb']);
	unset($myweercfg['blue']);

	$restcfg = FormatWeerCfg($myweercfg);
	$restcfg = serialize($restcfg);

	$result = array();
	if($dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy` SET weercfg='$restcfg', blue_comb='$blue_comb', updatetime='".time()."' WHERE id='$id'")){
		$result['errcode'] = 1;
		$result['errmsg']  = '方案更新成功！';
		exit(json_encode($result));
	}
}

else if($action == 'weer_scheme'){
	$myweercfg = $_POST;
	$scheme_name = $myweercfg['scheme_name'];

	$blue_comb = array();
	$blue_comb['blue']      = isset($myweercfg['blue']) ? $myweercfg['blue'] : array();
	$blue_comb['blue_comb'] = isset($myweercfg['blue_comb']) ? $myweercfg['blue_comb'] : '';

	$blue_comb = serialize($blue_comb);

	unset($myweercfg['action']);
	unset($myweercfg['scheme_name']);
	unset($myweercfg['blue']);
	unset($myweercfg['blue_comb']);

	$nextid = maxDayid() + 1;

	$restcfg = FormatWeerCfg($myweercfg);
	$restcfg = serialize($restcfg);

	$sql = "INSERT INTO `#@__caipiao_weermy` 
		(cp_dayid, scheme_name, weercfg, blue_comb, posttime, updatetime) 
		VALUES 
		('".$nextid."', '".$scheme_name."', '".$restcfg."', '".$blue_comb."', '".time()."', '".time()."')";
		
	$result = array();
	if($dosql->ExecNoneQuery($sql)){
		$id = $dosql->GetLastID();
		$result['errcode'] = 1;
		$result['id']      = $id;
		$result['errmsg']  = $scheme_name . '方案保存成功！';
		exit(json_encode($result));
	}
}

else if($action == 'weer_save'){
	exec("php ../weer8step.php", $outdata, $status);
	if($status == 1){
		exit('程序出错！');
	}

	$sid = $outdata[0];
	$timecost = $outdata[1];
	unset($outdata[0]);
	unset($outdata[1]);

	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` ORDER BY updatetime DESC");
	$sid       = $row['id'];
	$blue_comb = unserialize($row['blue_comb']);
	$blue      = $blue_comb['blue'];
	$combtype  = $blue_comb['blue_comb'];//1随机 2顺序插入

	$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_weermy_cpdata` WHERE sid='$sid'");

	$content = '';
	$content .= '<p>共'.count($outdata).'注，耗时：'.$timecost.'秒</p>';
	$index = 1;
	$bindex = 0;

	$ddblues = array(
		'1' => '14',
		'2' => '04',
		'3' => '10',
		'4' => '08',
		'5' => '11',
	);

	$gailv = array(1,1,1,1,2,2,2,3,4,5);
	shuffle($gailv);

	foreach ($outdata as $key => $red) {
		$tmpblue = '';
		if($combtype == 1){
			// $tmpblue = $blue[rand(0, count($blue)-1)];
			$gailv_index = $gailv[rand(0, count($gailv)-1)];
			$tmpblue = $ddblues[$gailv_index];
		}else{
			if($bindex == count($blue)-1){
				$bindex = 0;
			}
			$tmpblue = $blue[$bindex];
			$bindex++;
		}
		$ssq = !empty($tmpblue) ? $red . '+' . $tmpblue : $red;
		$content .= '<p>'.$index.'、'.$ssq.'</p>';
		$index++;

		$ssq = trim($ssq);

		$sql = "INSERT INTO `#@__caipiao_weermy_cpdata` (sid, ssq, winlevel) VALUES ('".$sid."', '".$ssq."', '0')";

		$dosql->ExecNoneQuery($sql);
	}

	exit($content);


	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` WHERE id='$id'");
	$myweercfg = unserialize($row['weercfg']);
	exit(json_encode($myweercfg));

	$redis = new Redis();
	$redis->connect('127.0.0.1',6379);

	$namekey = 'SSQ110W';
	//获取彩票模拟大数据总数
	$cpzhnum = $redis->lSize($namekey);
	$pernum  = 10000 * 10;
	$pgnum   = ceil($cpzhnum / $pernum);

	$t1 = time();
	$result = array();

	$total = 0;
	$filterData = array();
	for ($pg=1; $pg <= $pgnum; $pg++) { 
		$s_index = ($pg - 1) * $pernum;
		$e_index = ($pg - 1) * $pernum + $pernum;

		$list = $redis->lRange($namekey, $s_index, $e_index);

		//开始计算
		foreach ($list as $i => $cpinfo) {
			$cpinfo = json_decode($cpinfo,true);
			$redarr = $cpinfo['red'];

			$rest = RedWeerFilter($myweercfg, $redarr);

			if($rest === false) continue;

			$filterData[] = $rest;

			$total++;
		}
	}

	$t2 = time();
	$result['timecost'] = $timecost = ($t2 - $t1);
	$result['filterdata'] = $filterData;

	$content = '';

	foreach ($filterData as $index => $red) {
		$index = $index + 1;
		// $red = implode('.', $red);
		$content .= '<p>共'.$total.'注，耗时：'.$timecost.'秒</p>';
		$content .= '<p>'.$index.'、'.$red.'</p>';
	}
	echo $content;
	exit();
}