<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/weer.config.php';
require_once dirname(__FILE__) . '/../core/redindexV2.func.php';

LoginCheck();
if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}

if($action == 'code'){
	if($code){
		$maxid = maxDayid();
		$maxid = nextCpDayId($maxid);

		$cfg = array(
			'ssq' => $maxid.'期推荐号码',
		);

		$ipinfo = GetIP();

		$result = array();

		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND code='$code' AND codetype='2'");
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

		$member_num = 8;

		$need_update = 0;
		$myssq = array();
		if($hasnum0 >= $member_num){
			shuffle($ssqRest0);
			$myssq = array_slice($ssqRest0, 0, $member_num);
			$need_update = 1;
		}else if($hasnum0 > 0 && $hasnum0 < $member_num){
			shuffle($ssqRest0);
			$myssq = $ssqRest0;

			shuffle($ssqRest1);
			$myssq2 = array_slice($ssqRest1, 0, $member_num-$hasnum0);

			$myssq = array_merge($myssq, $myssq2);
			$need_update = 1;
		}else{
			shuffle($ssqRest1);
			$myssq = array_slice($ssqRest1, 0, $member_num);
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
				$bluearr = RandBlue($maxid, $groupnum, $bluearr);

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
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_code` SET status='1', usetime='".time()."', ssqinfo='".$ssqinfo."', ipinfo='".$ipinfo."' WHERE cp_dayid='$maxid' AND code='$code' AND codetype='2'");

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

else if($action == 'getcode'){
	if($codetype){

		$maxid = maxDayid();
		$maxid = nextCpDayId($maxid);

		$ssqcode = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND codetype='$codetype' AND status IN (0, 2)");
		while($list = $dosql->GetArray()){
			$tmp = array();
			$tmp['code']       = $list['code'];
			$tmp['codeid']     = $list['id'];
			$tmp['statusinfo'] = $list['status'] == 2 ? '领取中' : '';
			$ssqcode[] = $tmp;
		}

		$money = array(1=>2, 2=>5, 3=>10);

		$cfg = array(
			'code'       => $money[$codetype].'元领取码',
			'statusinfo' => '领取状态',
			'codeid'     => '操作',
		);

		$rest = array('table_th'=>'', 'table_td'=>'');
		$rest['table_th'] = '<tr>';
		foreach ($cfg as $field => $name) {
			$rest['table_th'] .= '<th>'.$name.'</th>';
		}
		$rest['table_th'] .= '</tr>';

		$bluedata = $dosql->GetOne("SELECT * FROM `lz_caipiao_weermy_blue` WHERE cp_dayid='$maxid'");
		$bluedanma = array();
		if(isset($bluedata['blueinfo'])){
			$blueinfo = unserialize($bluedata['blueinfo']);
			$blueinfo = array_slice($blueinfo, 0, 5);
			$bluedanma = $blueinfo;
		}

		$cfg = array_keys($cfg);
		foreach ($ssqcode as $v) {
			$rest['table_td'] .= '<tr>';
			foreach ($cfg as $vv) {
				$class = '';
				if($vv == 'ssq'){
					$class = 'style="color:#f36;"';
				}
				
				$cpyurl = '双色球'.$maxid.'期[發]【您的'.$money[$codetype].'元权益数据】：打开链接->http://72cp.wang/wxpage/taobao.php?code='.$v['code']."点击底部“访问原网页”查看。";
				if($bluedanma){
					$cpyurl .= '
蓝号推荐：'.implode(">", $bluedanma);
				}

				$fuli_cpyurl = '【福利[礼物]】
'.$cpyurl;
				$song_cpyurl = '【赠送[礼物]】
'.$cpyurl;
				if($vv == 'codeid'){
					$rest['table_td'] .= '<td '.$class.'>
					<a href="javascript:;" class="udtBtn" data-id="'.$v[$vv].'">修改</a>
					<a href="javascript:;" class="cpyBtn" id="cpyID'.$v['codeid'].'" data-id="'.$v['codeid'].'" data-copytext="'.$cpyurl.'">刷新</a>
					<a href="javascript:;" class="fuli_cpyID" id="fuli_cpyID'.$v['codeid'].'" data-id="'.$v['codeid'].'" data-copytext="'.$fuli_cpyurl.'">福利</a>
					<a href="javascript:;" class="song_cpyID" id="song_cpyID'.$v['codeid'].'" data-id="'.$v['codeid'].'" data-copytext="'.$song_cpyurl.'">赠送</a>
					</td>';
				}else if($vv == 'code'){
					$rest['table_td'] .= '<td '.$class.'><a href="taobao.php?code='.$v[$vv].'" target="_blank">'.$v[$vv].'</a></td>';
				}else{
					$rest['table_td'] .= '<td '.$class.'>'.$v[$vv].'</td>';
				}
			}
			$rest['table_td'] .= '</tr>';
		}

		$result = array();
		$result['errcode'] = 1;
		$result['errmsg'] = $rest;

		exit(json_encode($result));
	}
}

else if($action == 'chgcode_status'){
	if($codeid){
		// $maxid = maxDayid();
		// $maxid = $maxid + 1;

		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_code` WHERE id='$codeid'");
		if(!isset($row['id'])){
			$result['errcode'] = 2;
			$result['errmsg']  = "领取码不存在！";
			exit(json_encode($result));
		}

		if($row['status'] == 1){
			$result['errcode'] = 2;
			$result['errmsg']  = "领取码已被领取！";
			exit(json_encode($result));
		}

		if($row['status'] == 2){
			$result['errcode'] = 2;
			$result['errmsg']  = "领取码在领取中！";
			exit(json_encode($result));
		}

		if($row['status'] == 0){
			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy_code` SET status='2' WHERE id='$codeid'");
			$result['errcode'] = 1;
			$result['errmsg']  = "状态变更！";
			exit(json_encode($result));
		}
	}
}