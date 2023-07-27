<?php

header("Access-Control-Allow-Origin: *");

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

$legend = array(
    '龙头',
    '红2',
    '红3',
    '红4',
    '红5',
    '凤尾',
    '跨度',
);

$action = isset($action)?$action:'all';
if($action == 'kuadu'){
    $legend = array('跨度');
}

$num = isset($num)?$num:30;
$dosql->Execute("SELECT * FROM `#@__caipiao_history` order by id DESC LIMIT {$num}");
$rows = array();
$lottory_no = array();
while ($row = $dosql->GetArray()) {
    $reds = explode(",",$row['red_num']);
    $kuadu = $reds[5] - $reds[0];
    if($action == 'kuadu'){
        $rows[] = array($kuadu);
    }else{
        $reds[] = $kuadu;
        $rows[] = $reds;
    }
    $lottory_no[] = intval(substr($row['cp_dayid'],-3)).'期';
}
$rows = array_reverse($rows);
$lottory_no = array_reverse($lottory_no);

$legend_data = array();
foreach ($rows as $reds) {
    foreach ($reds as $key => $red) {
        if(!isset($legend_data[$key])){
            $legend_data[$key] = array();
        }
        $legend_data[$key][] = $red;
    }
}
if($action == 'kuadu'){
    $data = array(
        'legend'=>$legend,
        'lottory_no'=>$lottory_no,
        'legend_data_0'=>$legend_data[0],
    );
}else{
    $data = array(
        'legend'=>$legend,
        'lottory_no'=>$lottory_no,
        'legend_data_0'=>$legend_data[0],
        'legend_data_1'=>$legend_data[1],
        'legend_data_2'=>$legend_data[2],
        'legend_data_3'=>$legend_data[3],
        'legend_data_4'=>$legend_data[4],
        'legend_data_5'=>$legend_data[5],
        'legend_data_6'=>$legend_data[6],
    );
}

$ret = array('code'=>200, 'data'=>$data);
echo json_encode($ret);