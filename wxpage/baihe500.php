<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/core.func.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

// $wintail = array(1,2,6,5,8,3);
$wintail = array();

$killtail = array(2,3,4,5,8);
$killtail = array(0,4,6,9);
$killtail = array(0,4,6,7,9);
$killtail = array(0,4,6,8,9);
$killtail = array(1,6,8);
$killtail = array(5,8,9,0);

//6
$yu_win = 0; //余集命中
$jj_win = 2; //交集命中
$ba_win = 2; //百减和命中
$he_win = 2; //和减百命中

//5
$yu_win = 1; //余集命中
$jj_win = 2; //交集命中
$ba_win = 2; //百减和命中
$he_win = 1; //和减百命中

//5
// $yu_win = 1; //余集命中
// $jj_win = 1; //交集命中
// $ba_win = 2; //百减和命中
// $he_win = 2; //和减百命中

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

echo "百合余出{$yu_win}个，组合数：{$yu_combnum}<br/>";
echo "百合交出{$jj_win}个，组合数：{$jj_combnum}<br/>";
echo "百减和出{$ba_win}个，组合数：{$ba_combnum}<br/>";
echo "和减百出{$he_win}个，组合数：{$he_combnum}<br/>";

$total_combnum = ($yu_combnum > 0 ? $yu_combnum : 1) * 
($jj_combnum > 0 ? $jj_combnum : 1) * 
($ba_combnum > 0 ? $ba_combnum : 1) * 
($he_combnum > 0 ? $he_combnum : 1);
echo "双色球6球组合数：{$total_combnum}<br/>";


$yuReds = $yu_win ? combination($baiheYu, $yu_win) : array();
$jjReds = $jj_win ? combination($baiheJJ, $jj_win) : array();
$baReds = $ba_win ? combination($baiheBa, $ba_win) : array();
$heReds = $he_win ? combination($baiheHe, $he_win) : array();

if(isset($debug)){
	die;
}

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


echo "<pre>";
// print_r($data);die;
// print_r($jjReds);
// print_r($baReds);
// print_r($heReds);

$allReds = array();

foreach ($data as $onekey => $oneval) {
	foreach ($oneval['list'] as $Red1) {
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
											break;
										}
									}
									if(!$haswintail) continue;
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
										break;
									}
								}
								if(!$haswintail) continue;
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
								break;
							}
						}
						if(!$haswintail) continue;
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
						break;
					}
				}
				if(!$haswintail) continue;
			}
			sort($tmp);
			$allReds[] = $tmp;
		}
	}
}

// echo count($allReds);die;

// $cp_dayid = 2018083;
$cp_dayid = maxDayid()+1;
$total = BaiHe500W($cp_dayid, $allReds);
echo "最终缩出：{$total}注<br/>";
echo '<a href="amazeweer_scheme_ssq.php?id=2" target="_blank">百合500万</a>';

// echo "<pre>";
// print_r($allReds);die;

function BaiHe500W($cp_dayid, $winReds, $blue=8){
	global $dosql;

	$total=0;
	$sid = 2;

	$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy` SET cp_dayid='$cp_dayid' WHERE id='$sid'");
	$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_weermy_cpdata`  WHERE sid='$sid'");

	foreach ($winReds as $red) {
		$red = implode('.', $red);

		$ssq = $red . '+' . $blue;
		$sql = "INSERT INTO `#@__caipiao_weermy_cpdata` (sid, ssq, winlevel) VALUES ('".$sid."', '".$ssq."', '0')";
		$dosql->ExecNoneQuery($sql);
		$total++;
	}

	return $total;
}