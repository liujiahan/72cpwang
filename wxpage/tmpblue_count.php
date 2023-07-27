<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';

set_time_limit(0);
ini_set('memory_limit', '512M');

$s1 = 2019001;
$e1 = 2020038;

$idss = '2004015,2004120,2005021,2005023,2005033,2005071,2005107,2005114,2006027,2006061,2006110,2007081,2008038,2008104,2008152,2009023,2009072,2009124,2009128,2010009,2010028,2010079,2010152,2011002,2011034,2011060,2011061,2011101,2011119,2011124,2012061,2012092,2012124,2013042,2013089,2013100,2013131,2013140,2014092,2014111,2015026,2015043,2015114,2016059,2016060,2016091,2017031,2017039,2017040,2017097,2018044,2018071,2018108,2018151,2019004,2019035,2019100,2020036,2020037';
// echo count(explode(',', $idss));die;

$ids = array();
// $dosql->Execute("SELECT * FROM `#@__caipiao_history` where cp_dayid>0 and cp_dayid>$s1 and cp_dayid<=$e1 ORDER BY cp_dayid asc");
$dosql->Execute("SELECT * FROM `#@__caipiao_history` where cp_dayid in ($idss) ORDER BY cp_dayid asc");
while ($row = $dosql->GetArray()) {
	$cp_dayid = $row['cp_dayid'];
	$blue_num = $row['blue_num'];

	$info = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid asc");
	if(empty($info)) continue;

	if($info['blue_num'] == $blue_num){
		$ids[$info['cp_dayid']] = 1;
	}
	continue;

	// $blueMiss = blueMissing($info['cp_dayid'], $info['cp_dayid']);
	// echo $info['cp_dayid'];
	// echo "<br/>";
	// print_r($blueMiss);die;

	foreach ($blueMiss as $blue => $num) {
		if($blue == $blue_num && $num >= 60){
			$ids[$info['cp_dayid']] = array();
			$ids[$info['cp_dayid']]['blue'] = $blue_num;
			$ids[$info['cp_dayid']]['num'] = $num;
		}
	}
}
echo "<pre>";
print_r($ids);
die;



$idss = '2004015,2004120,2005021,2005023,2005033,2005071,2005107,2005114,2006027,2006061,2006110,2007081,2008038,2008104,2008152,2009023,2009072,2009124,2009128,2010009,2010028,2010079,2010152,2011002,2011034,2011060,2011061,2011101,2011119,2011124,2012061,2012092,2012124,2013042,2013089,2013100,2013131,2013140,2014092,2014111,2015026,2015043,2015114,2016059,2016060,2016091,2017031,2017039,2017040,2017097,2018044,2018071,2018108,2018151,2019004,2019035,2019100,2020036,2020037';

$ids = array();
// $dosql->Execute("SELECT * FROM `#@__caipiao_history` where cp_dayid>0 and cp_dayid>$s1 and cp_dayid<=$e1 ORDER BY cp_dayid asc");
$dosql->Execute("SELECT * FROM `#@__caipiao_history` where cp_dayid in ($idss) ORDER BY cp_dayid asc");
while ($row = $dosql->GetArray()) {
	$cp_dayid = $row['cp_dayid'];
	$blue_num = $row['blue_num'];

	// $info = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>0 AND cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
	$info = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>0 AND cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
	if(empty($info)) continue;

	$blueMiss = blueMissing($info['cp_dayid'], $info['cp_dayid']);
	// echo $info['cp_dayid'];
	// echo "<br/>";
	// print_r($blueMiss);die;

	foreach ($blueMiss as $blue => $num) {
		if($blue == $blue_num && $num >= 60){
			$ids[$info['cp_dayid']] = array();
			$ids[$info['cp_dayid']]['blue'] = $blue_num;
			$ids[$info['cp_dayid']]['num'] = $num;
		}
	}
}
echo "<pre>";
print_r(implode(',',array_keys($ids)));
die;