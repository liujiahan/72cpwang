<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';
require_once dirname(__FILE__).'/../core/choosered.func.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/weer.config.php';
require_once dirname(__FILE__) . '/../core/redindexV2.func.php';

LoginCheck();

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
	ShowMsg("Permission denied","-1");
    exit;
}

function BaiHe500W($cp_dayid, $winReds, $postdata, $blue=8){
	global $dosql;
	global $sid;

	$total=0;

	if($sid == 0){
		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_baihe_500w` (cp_dayid,params,posttime,updatetime) VALUES ('".$cp_dayid."', '".$postdata."', '".time()."', '".time()."')");
		$sid = $dosql->GetLastID();
	}else{
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_baihe_500w` SET params='$postdata', updatetime='".time()."' WHERE id='$sid'");
		$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_baihe_cpdata` WHERE sid='$sid'");
	}

	foreach ($winReds as $red) {
		$red = implode('.', $red);

		$ssq = $red . '+' . $blue;
		$sql = "INSERT INTO `#@__caipiao_baihe_cpdata` (sid, ssq, winlevel) VALUES ('".$sid."', '".$ssq."', '0')";
		$dosql->ExecNoneQuery($sql);
		$total++;
	}

	return $total;
}

if($action == 'baihe500'){
	$wintail = !empty($wintail) ? $wintail : array();

	foreach ($killtail as $key => $tail) {
		if($tail == '') unset($killtail[$key]);
	}

	foreach ($wintail as $key => $tail) {
		if($tail == '') unset($wintail[$key]);
	}

	$dannum1 = isset($dannum[0]) ? $dannum[0] : 0;
	$dannum2 = isset($dannum[1]) ? $dannum[1] : 0;

	$yu_win = !empty($yu_win) ? $yu_win : 0; //余集命中
	$jj_win = !empty($jj_win) ? $jj_win : 0; //交集命中
	$ba_win = !empty($ba_win) ? $ba_win : 0; //百减和命中
	$he_win = !empty($he_win) ? $he_win : 0; //和减百命中

	$nextBaiHe = nextBaiHe();

	$baiheYu = $nextBaiHe['other_reds'];
	$baiheJJ = $nextBaiHe['jiaoji_reds'];
	$baiheBa = $nextBaiHe['percent_reds'];
	$baiheHe = $nextBaiHe['sum_reds'];

	foreach ($baiheYu as $index => &$red) {
		$tail = $red % 10;
		if(in_array($tail, $killtail)){
			unset($baiheYu[$index]);
		}
	}
	sort($baiheYu);

	foreach ($baiheJJ as $index => &$red) {
		$tail = $red % 10;
		if(in_array($tail, $killtail)){
			unset($baiheJJ[$index]);
		}
	}
	sort($baiheJJ);

	foreach ($baiheBa as $index => &$red) {
		$tail = $red % 10;
		if(in_array($tail, $killtail)){
			unset($baiheBa[$index]);
		}
	}
	sort($baiheBa);

	foreach ($baiheHe as $index => &$red) {
		$tail = $red % 10;
		if(in_array($tail, $killtail)){
			unset($baiheHe[$index]);
		}
	}
	sort($baiheHe);

	$yu_combnum = $yu_win ? C(count($baiheYu), $yu_win) : 0;
	$jj_combnum = $jj_win ? C(count($baiheJJ), $jj_win) : 0;
	$ba_combnum = $ba_win ? C(count($baiheBa), $ba_win) : 0;
	$he_combnum = $he_win ? C(count($baiheHe), $he_win) : 0;

	$str = "";
	$str .= "百合余出{$yu_win}个，组合数：{$yu_combnum}<br/>";
	$str .= "百合交出{$jj_win}个，组合数：{$jj_combnum}<br/>";
	$str .= "百减和出{$ba_win}个，组合数：{$ba_combnum}<br/>";
	$str .= "和减百出{$he_win}个，组合数：{$he_combnum}<br/>";

	$total_combnum = ($yu_combnum > 0 ? $yu_combnum : 1) * 
	($jj_combnum > 0 ? $jj_combnum : 1) * 
	($ba_combnum > 0 ? $ba_combnum : 1) * 
	($he_combnum > 0 ? $he_combnum : 1);
	$str .= "双色球6球组合数：{$total_combnum}<br/>";

	if(isset($getnum)){
		exit($str);
	}

	$yuReds = $yu_win ? combination($baiheYu, $yu_win) : array();
	$jjReds = $jj_win ? combination($baiheJJ, $jj_win) : array();
	$baReds = $ba_win ? combination($baiheBa, $ba_win) : array();
	$heReds = $he_win ? combination($baiheHe, $he_win) : array();

	$data = array();
	if( !empty($yuReds) ){
		$data['child'] = array();
		$data['child']['list'] = $yuReds;
	}

	if( !empty($jjReds) ){
		if(!array_key_exists('child', $data)){
			$data['child'] = array();
			$data['child']['list'] = $jjReds;
		}else{
			$data['child']['child'] = array();
			$data['child']['child']['list'] = $jjReds;
		}
	}

	if( !empty($baReds) ){
		if(!array_key_exists('child', $data)){
			$data['child'] = array();
			$data['child']['list'] = $baReds;
		}else if(!array_key_exists('child', $data['child'])){
			$data['child']['child'] = array();
			$data['child']['child']['list'] = $baReds;
		}else{
			$data['child']['child']['child'] = array();
			$data['child']['child']['child']['list'] = $baReds;
		}
	}

	if( !empty($heReds) ){
		if(!array_key_exists('child', $data)){
			$data['child'] = array();
			$data['child']['list'] = $heReds;
		}else if(!array_key_exists('child', $data['child'])){
			$data['child']['child'] = array();
			$data['child']['child']['list'] = $heReds;
		}else if(!array_key_exists('child', $data['child']['child'])){
			$data['child']['child']['child'] = array();
			$data['child']['child']['child']['list'] = $heReds;
		}else{
			$data['child']['child']['child']['child'] = array();
			$data['child']['child']['child']['child']['list'] = $heReds;
		}
	}

	$allReds = array();

	foreach ($data as $onekey => $oneval) {
		foreach ($oneval['list'] as $Red1) {
			$dannum = 0;
			if(isset($oneval['child']['list'])){
				foreach ($oneval['child']['list'] as $Red2) {
					if(isset($oneval['child']['child']['list'])){
						foreach ($oneval['child']['child']['list'] as $Red3) {
							if(isset($oneval['child']['child']['child']['list'])){
								foreach ($oneval['child']['child']['child']['list'] as $Red4) {
									$tmp = array_merge($Red1, $Red2, $Red3, $Red4);
									if($wintail){
										$haswintail = false;
										foreach ($tmp as $tmpred) {
											$tmptail = $tmpred % 10;
											if(in_array($tmptail, $wintail)){
												$haswintail = true;
												$dannum++;
												break;
											}
										}
										if(!$haswintail || $haswintail && $dannum<$dannum1 && $dannum>$dannum2) continue;
									}
									sort($tmp);
									$allReds[] = $tmp;
								}
							}else{
								$tmp = array_merge($Red1, $Red2, $Red3);
								if($wintail){
									$haswintail = false;
									foreach ($tmp as $tmpred) {
										$tmptail = $tmpred % 10;
										if(in_array($tmptail, $wintail)){
											$haswintail = true;
											$dannum++;
											break;
										}
									}
									if(!$haswintail || $haswintail && $dannum<$dannum1 && $dannum>$dannum2) continue;
								}
								sort($tmp);
								$allReds[] = $tmp;
							}
						}
					}else{
						$tmp = array_merge($Red1, $Red2);
						if($wintail){
							$haswintail = false;
							foreach ($tmp as $tmpred) {
								$tmptail = $tmpred % 10;
								if(in_array($tmptail, $wintail)){
									$haswintail = true;
									$dannum++;
									break;
								}
							}
							if(!$haswintail || $haswintail && $dannum<$dannum1 && $dannum>$dannum2) continue;
						}
						sort($tmp);
						$allReds[] = $tmp;
					}
				}
			}else{
				$tmp = $Red1;
				if($wintail){
					$haswintail = false;
					foreach ($tmp as $tmpred) {
						$tmptail = $tmpred % 10;
						if(in_array($tmptail, $wintail)){
							$haswintail = true;
							$dannum++;
							break;
						}
					}
					if(!$haswintail || $haswintail && $dannum<$dannum1 && $dannum>$dannum2) continue;
				}
				sort($tmp);
				$allReds[] = $tmp;
			}
		}
	}

	$postdata = serialize($_POST);

	$cp_dayid = maxDayid()+1;

	$sid = isset($_POST['sid']) ? $_POST['sid'] : 0;
	$total = BaiHe500W($cp_dayid, $allReds, $postdata);

	$result = array();
	$result['errcode'] = 0;
	$result['total'] = $total;

	exit(json_encode($result));
	// echo "最终缩出：{$total}注<br/>";
	// echo '<a href="amazeweer_scheme_ssq.php?id=2" target="_blank">百合500万</a>';

	
}

else if($action == 'schemes'){
	$cpnum = isset($cpnum) ? $cpnum : 10;

	$data = array();
	if(isset($cp_dayid) && !empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_baihe_500w` WHERE cp_dayid='$cp_dayid'", 'a');
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_baihe_500w` ORDER BY id DESC LIMIT {$cpnum}", 'a');
	}
	
	$schemes = array();
	while($row = $dosql->GetArray('a')){
		$row['posttime'] = date("Y-m-d H:i", $row['updatetime']);
		$params = unserialize($row['params']);
		$row['scheme_name'] = "余集：{$params['yu_win']}、交集：{$params['jj_win']}、百减：{$params['ba_win']}、和减：{$params['he_win']}";

		$ssqnum = $dosql->GetOne("SELECT count(*) as num FROM `#@__caipiao_baihe_cpdata` WHERE sid='{$row['id']}'");
		$row['num'] = "查看（" . $ssqnum['num'] . "）";
		$wininfo = unserialize($row['wininfo']);
		$winstr = '';
		if(is_array($wininfo)&&!empty($wininfo)){
			foreach ($wininfo as $level => $pnum) {
				$winstr .= "{$level}等奖{$pnum}注、";
			}
			$winstr = mb_substr($winstr, 0, -1);
		}
		if($row['iscalc'] && empty($winstr)) $winstr = '再接再厉';
		$row['wininfo'] = $winstr;
	    $schemes[] = $row;
	}

	$cfg = array(
		'id'          => '编号',
		'cp_dayid'    => '期数',
		'scheme_name' => '方案名称',
		'posttime'    => '更新时间',
		'url'         => '方案号码',
		'wininfo'     => '命中情况',
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
				$rest['table_td'] .= '<td '.$class.'><a href="baihe500w_update.php?id='.$v['id'].'">'.$v[$vv].'</a></td>';
			}else if($vv == 'url'){
				$rest['table_td'] .= '<td><a href="amazebaihe_scheme_ssq.php?id='.$v['id'].'">'.$v['num'].'</a></td>';
			}else if($vv == 'posttime'){
				$rest['table_td'] .= '<td><a href="javascript:if(confirm(\'确定要删除吗？\')) location=\'ajax/red_baihe_do.php?action=del&id='.$v['id'].'\'">'.$v[$vv].'【删除】</a></td>';
			}else{
				$rest['table_td'] .= '<td '.$class.'>'.$v[$vv].'</td>';
			}
		}
		$rest['table_td'] .= '</tr>';
	}

	exit(json_encode($rest));
}

else if($action == 'del'){
	if($id){
		$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_baihe_500w` WHERE id='$id'");
		$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_baihe_cpdata` WHERE sid='$id'");
		header("Location:".WEIXIN_BASE.'wxpage/amazebaihe_my.php');
	}
}

else if($action == 'schemes_ssq'){
	$cpnum = isset($cpnum) ? $cpnum : 30;

	$data = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_baihe_cpdata` WHERE sid='$id' AND winlevel='$winlevel' ORDER BY id ASC LIMIT {$cpnum}", 'a');
		

	$curid = $dosql->GetOne("SELECT * FROM `#@__caipiao_baihe_500w` WHERE id='$id'");

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

		$tmp['winlevel'] = $row['winlevel'];
	    $schemes[] = $tmp;
	    $index++;
	}

	

	$cfg = array(
		'idx'      => '序号',
		'ssq'      => '号码',
		'winlevel' => '中奖等奖',
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

else if($action == 'schemes_ssq_index'){
	$curid = $dosql->GetOne("SELECT * FROM `#@__caipiao_baihe_500w` WHERE id='$id'");

	$killtail = RedLocationKill2($curid['cp_dayid']);
	$redmiss  = redMissing($curid['cp_dayid']);


	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$curid['cp_dayid']} ORDER BY cp_dayid DESC");
	$allSSQ = array();
	while($row = $dosql->GetArray()){
		$allSSQ[$row['cp_dayid']]['cp_dayid'] = $row['cp_dayid'];
		$allSSQ[$row['cp_dayid']]['red_num'] = explode(",", $row['red_num']);
		$allSSQ[$row['cp_dayid']]['blue_num'] = $row['blue_num'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_baihe_cpdata` WHERE sid='$id' AND status=0");
	while($row = $dosql->GetArray()){
		$row['ssq'] = trim($row['ssq']);
		$tmp = explode("+", $row['ssq']);
		// $reds = substr($row['ssq'], 0, -3);
		// $blue = substr($row['ssq'], -2);
		$reds = explode('.', $tmp[0]);
		$blue = $tmp[1];
		
		$ssqindex = RedIndex22($reds, $redmiss, $killtail, $allSSQ, $blue);
		$ssqindex = serialize($ssqindex);
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_baihe_cpdata` SET ssqindex='$ssqindex', status='1' WHERE id=".$row['id']);
	}

	exit(1);

}

else if($action == 'schemes_ssq_prize'){
	if($sid){
		$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_baihe_500w` WHERE id='$sid'");
		$cp_dayid = $row['cp_dayid'];
		$ssqinfo = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
		$result = array();
		if(!isset($ssqinfo['id'])){
			$result['errcode'] = 1;
			$result['errmsg']  = '未开奖';
			exit(json_encode($result));
		}

		$red_num = explode(",", $ssqinfo['red_num']);
		$blue_num = $ssqinfo['blue_num'];

		$wininfo = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_baihe_cpdata` WHERE sid='$sid' AND iscalc='0'");
		while ($row = $dosql->GetArray()) {
			$ssq = $row['ssq'];
			$ssq = explode("+", $row['ssq']);
			$tmp_blue_num = $ssq[1];
			$tmp_red_num = explode('.', $ssq[0]);

			$blue_win_num = $blue_num == $tmp_blue_num ? 1 : 0;
			$red_win_num = count(array_intersect($tmp_red_num, $red_num));

			$winlevel = 0;
			if($blue_win_num == 0 && $red_win_num == 3){
				$winlevel = 7;
			}elseif($blue_win_num == 1 && $red_win_num < 3){
				$winlevel = 6;
			}else if(($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)){
				$winlevel = 5;
			}else if(($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)){
				$winlevel = 4;
			}else if($red_win_num == 5 && $blue_win_num == 1){
				$winlevel = 3;
			}else if($red_win_num == 6 && $blue_win_num == 0){
				$winlevel = 2;
			}else if($red_win_num == 6 && $blue_win_num == 1){
				$winlevel = 1;
			}
			if($winlevel){
				if(!isset($wininfo[$winlevel])) $wininfo[$winlevel] = 0;

				$wininfo[$winlevel]++;
			}

			$dosql->ExecNoneQuery("UPDATE `#@__caipiao_baihe_cpdata` SET winlevel='$winlevel', iscalc='1' WHERE id={$row['id']}");
		}

		$wininfo = serialize($wininfo);
		$dosql->ExecNoneQuery("UPDATE `#@__caipiao_baihe_500w` SET wininfo='$wininfo', iscalc='1' WHERE id='$sid'");
	}
	$result['errcode'] = 0;
	$result['errmsg']  = '兑奖完成';
	exit(json_encode($result));
}