<?php

header("Access-Control-Allow-Origin: *");

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

$legend = array(
    '跨度',
    '蓝号',
    '蓝和值',
);

$num = isset($num)?$num:30;
$dosql->Execute("SELECT * FROM `#@__caipiao_history` order by id DESC LIMIT {$num}");
$rows = array();
$lottory_no = array();
$blue_span = array();
$blue = array();
$blue_sum = array();
while ($row = $dosql->GetArray()) {
    $blue_num = $row['blue_num'];
    $prerow = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'{$row["cp_dayid"]}' ORDER BY cp_dayid DESC");
    $blue[] = $blue_num;
    $blue_span[] = abs($prerow['blue_num']-$blue_num);
    $blue_sum[] = $prerow['blue_num']+$blue_num;
    $lottory_no[] = intval(substr($row['cp_dayid'],-3)).'期';
}
$lottory_no = array_reverse($lottory_no);
$blue = array_reverse($blue);
$blue_span = array_reverse($blue_span);
$blue_sum = array_reverse($blue_sum);

$data = array(
    'legend'=>$legend,
    'lottory_no'=>$lottory_no,
    'blue'=>$blue,
    'blue_span'=>$blue_span,
    'blue_sum'=>$blue_sum,
);
$ret = array('code'=>200, 'data'=>$data);
echo json_encode($ret, JSON_UNESCAPED_UNICODE);