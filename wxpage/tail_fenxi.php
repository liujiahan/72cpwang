<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
// echo GetIP();die;

// require_once dirname(__FILE__).'/core/wuxing.func.php';
// require_once dirname(__FILE__).'/core/choosered.func.php';
// require_once dirname(__FILE__).'/core/wuxing.func.php';
// require_once dirname(__FILE__).'/core/fourMagic.func.php';
// // require_once dirname(__FILE__).'/core/weer.config.php';
// require_once dirname(__FILE__).'/core/suanfa.func.php';
// require_once dirname(__FILE__).'/core/redindexV2.func.php';
// require_once dirname(__FILE__).'/core/weer.func.php';
require_once dirname(__FILE__).'/core/core.func.php';
require_once dirname(__FILE__).'/core/tail.func.php';

function tailfun($_tail=6, $cp_dayid=''){
	global $dosql;

	echo "<pre>";
	echo "尾数据分析：<br/>";
	$tails = array(0,1,2,3,4,5,6,7,8,9);
	$alltail = combination($tails, $_tail);
	foreach ($alltail as &$v) {
		$v = implode("", $v);
	}
	// print_r($alltail);die;
	echo "{$_tail}尾组合数：" . count($alltail) . "<br/>";

	$data = array('total'=>0,'tail6'=>0,'tail5'=>0,'tail4'=>0,'tail3'=>0);

	$taildata = array();
	if(!empty($cp_dayid)){
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_tail` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid ASC");
	}else{
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_tail` ORDER BY cp_dayid ASC");
	}
	while($row = $dosql->GetArray()){
	    $red_tail_detail = unserialize($row['red_tail_detail']);
		$tail_num = $red_tail_detail['tail_num'];
		$data['total']++;
		if($tail_num==6) $data['tail6']++;
		if($tail_num==5) $data['tail5']++;
		if($tail_num==4) $data['tail4']++;
		if($tail_num==3) $data['tail3']++;

		$tmptail = array();

		$red_tail = unserialize($row['red_tail']);
		foreach ($red_tail as $tail => $num) {
			if($num) $tmptail[] = $tail;
		}

		if($tail_num==$_tail) 
			$taildata[] = implode("",$tmptail);

	}
	echo "{$_tail}尾开出期数：" . count($taildata) . "——";
	// print_r($taildata);die;

	$taildatanum = array();
	foreach ($taildata as $tailstr) {
		if(!isset($taildatanum[$tailstr])){
			$taildatanum[$tailstr] = 0;
		}
		$taildatanum[$tailstr]++;
	}
	echo "{$_tail}尾开出期数中重复的组合：" . count($taildatanum) . "<br/>";
	
	arsort($taildatanum);

	$noshow = array();
	foreach ($alltail as $v) {
		if(!isset($taildatanum[$v])){
			$noshow[] = $v;
		}
	}
	echo "{$_tail}尾未开出的组合数量：" . count($noshow) . "<br/>";
	echo "#################未出的尾数组合#################<br/>";
	print_r($noshow);
	echo "#################已出的尾数组合及数量#################<br/>";
	// print_r($taildatanum);
	$taildatanum2 = array_keys($taildatanum);

	$pgnum = 5;
	$arrsize = ceil(count($taildatanum)/$pgnum);
	// echo $arrsize;die;
	for ($pg=1; $pg <= $arrsize; $pg++) { 
		$tmpv = array_slice($taildatanum2, ($pg-1)*$pgnum, $pgnum);
		foreach ($tmpv as $tailstr) {
			echo $tailstr . '=>' . $taildatanum[$tailstr];
			echo "    ";
		}
		echo "<br/>";
	}
}

$tailnum = isset($tailnum) ? $tailnum : 6;
tailfun($tailnum);


// 0 5
// 6 10
// 11 15

// (1-1)*5,5*1
// (2-1)*5,5*2