<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('yaojian');

//初始化参数
$tbname = '#@__caipiao_weermy_code';
$gourl  = 'amazeweer_code.php';
$action = isset($action) ? $action : '';

//添加管理员
if($action == 'create_code')
{
	$row = $dosql->GetOne("SELECT COUNT(*) AS total FROM `$tbname` WHERE cp_dayid='$cp_dayid' AND codetype='$codetype'");
	$num = $row['total'];

	if($num >= $codenum){
		ShowMsg("领取码已超限额！", '-1');
		exit;
	}

	$create_num = $codenum - $num;

	$total = 0;
	for ($i=0; $i < $create_num; $i++) { 
		$code = rand(100000, 999999);
		$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_weermy_code` (cp_dayid, code, codetype) VALUES ('$cp_dayid', '$code', '$codetype')");
		$total++;
	}
	$typeinfo = '';
	if($codetype == 1){
		$typeinfo = '2元码';
	}else if($codetype == 2){
		$typeinfo = '5元码';
	}else if($codetype == 3){
		$typeinfo = '10元码';
	}
	$msg = '生成'.$create_num.'个'.$typeinfo;

	ShowMsg($msg, $gourl);
	exit;
}

else if($action == 'update')
{
	if(!empty($card_roleid) || !empty($house_roleid) || !empty($bankcard_roleid))
	{
		$card_roleid = !empty($card_roleid) ? $card_roleid : 0;
		$house_roleid = !empty($house_roleid) ? $house_roleid : 0;
		$bankcard_roleid = !empty($bankcard_roleid) ? $bankcard_roleid : 0;
		$dosql->ExecNoneQuery("UPDATE `#@__p_yj_user` SET card_roleid='$card_roleid', house_roleid='$house_roleid', bankcard_roleid='$bankcard_roleid', online='$online' WHERE userid='$userid'");
	}
	header("location:$gourl");
	exit();
}

else if($action == 'duijiang')
{
	if($cp_dayid) {
		$cur_ssq = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='".$cp_dayid."'");
		if(!isset($cur_ssq['id'])){
			ShowMsg($cp_dayid."期还未开奖！", '-1');
			exit;
		}

		//开奖号
		$opencode = $cur_ssq['opencode'];

		$cur_reds = explode(',', $cur_ssq['red_num']);
		$cur_blue = $cur_ssq['blue_num'];

		$total = 0;
		$dosql->Execute("SELECT * FROM `#@__caipiao_weermy_code` WHERE cp_dayid='".$cp_dayid."' AND is_run = '0'");
		while($row = $dosql->GetArray()){
			if(empty($row['ssqinfo'])) continue;
			//下单的红球 篮球
			$ssqinfo = explode("|", $row['ssqinfo']);

			$ssqArr = array();
			foreach ($ssqinfo as $k => $ssq) {
				$ssq = explode("+", $ssq);

				$ssqArr[$k] = array();
				$ssqArr[$k]['reds'] = explode(".", trim($ssq[0]));
				$ssqArr[$k]['blue'] = $ssq[1];
			}

			$winArr = array();
			foreach ($ssqArr as $v) {
				$reds = $v['reds'];
				$blue = $v['blue'];

				$red_win_num  = count(array_intersect($reds, $cur_reds));
				$blue_win_num = $cur_blue == $blue ? 1 : 0;

				if($blue_win_num == 1 && $red_win_num < 3){
					if(!isset($winArr[6])){
						$winArr[6] = array();
						$winArr[6]['num']    = 0;
						$winArr[6]['prizes'] = 0;
					}

					$winArr[6]['num']++;
					$winArr[6]['prizes'] += 5;
				}else if(($blue_win_num == 0 && $red_win_num == 4) || ($red_win_num == 3 && $blue_win_num == 1)){
					if(!isset($winArr[5])){
						$winArr[5] = array();
						$winArr[5]['num']    = 0;
						$winArr[5]['prizes'] = 0;
					}

					$winArr[5]['num']++;
					$winArr[5]['prizes'] += 10;
				}else if(($blue_win_num == 0 && $red_win_num == 5) || ($red_win_num == 4 && $blue_win_num == 1)){
					if(!isset($winArr[4])){
						$winArr[4] = array();
						$winArr[4]['num']    = 0;
						$winArr[4]['prizes'] = 0;
					}

					$winArr[4]['num']++;
					$winArr[4]['prizes'] += 200;
				}else if($red_win_num == 5 && $blue_win_num == 1){
					if(!isset($winArr[3])){
						$winArr[3] = array();
						$winArr[3]['num']    = 0;
						$winArr[3]['prizes'] = 0;
					}

					$winArr[3]['num']++;
					$winArr[3]['prizes'] += 3000;
				}else if($red_win_num == 6 && $blue_win_num == 0){
					if(!isset($winArr[2])){
						$winArr[2] = array();
						$winArr[2]['num']    = 0;
						$winArr[2]['prizes'] = 0;
					}

					$winArr[2]['num']++;
					$winArr[2]['prizes'] += 100000;
				}else if($red_win_num == 6 && $blue_win_num == 1){
					if(!isset($winArr[1])){
						$winArr[1] = array();
						$winArr[1]['num']    = 0;
						$winArr[1]['prizes'] = 0;
					}

					$winArr[1]['num']++;
					$winArr[1]['prizes'] += 5000000;
				}
			}

			$wininfo = !empty($winArr) ? serialize($winArr) : '';
			$is_run  = 1;

			$updateSql = "UPDATE `#@__caipiao_weermy_code` SET wininfo='$wininfo', is_run  ='$is_run' WHERE id=".$row['id'];
			
			if($dosql->ExecNoneQuery($updateSql)){
				$total++;
			}
		}

		ShowMsg("计算".$total."条数据！", '-1');
	}
}

else if($action == 'del')
{
	$dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `userid`='$userid' ");
	header("location:$gourl");
	exit();
	
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>