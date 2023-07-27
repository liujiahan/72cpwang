<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_newkill_blue2`");
$index = 0;
$allRedList = array();
while($row = $dosql->GetArray()){
    $tmp['blue_num'] = $row['blue_num'];
    $tmp['kill_win'] = $row['kill_win'];
    $tmp['kill_json'] = json_decode($row['kill_json'],true);

	$allRedList[$index] = $tmp;
	$index++;
}

$total_win = 0;
$perwin = array();
foreach ($allRedList as $row) {
    $row['kill_win'] == 1 && $total_win++;

    foreach ($row['kill_json'] as $kill => $kblue) {
        if(!isset($perwin[$kill])){
            $perwin[$kill] = 0;
        }
        $kblue['kill'] == 1 && $perwin[$kill]++;
    }
}

echo $total = count($allRedList);
echo "<br/>";

echo '整体命中'.$total_win.'次 占比：' . round($total_win / $total, 4) * 100 . '%';
echo "<br/>";

foreach ($perwin as $kill => $num) {
    echo '杀法'.$kill.'杀中 '.$num.'次 占比：' . round($num / $total, 4) * 100 . '%';
    echo "<br/>";
}
die;

echo "<pre>";
echo $total_win;
print_R($perwin);
echo "</pre>";die;

$nextData = array();
$tailCount = array();
foreach ($allRedList as $k => $tmpReds) {
    $tailArr = array();
    foreach ($tmpReds as $tmpred) {
        $tail = $tmpred % 10;
        if(!in_array($tail, $tailArr)){
            $tailArr[] = $tail;
        }
    }
    $tailnum = count($tailArr);
    $nextData[$k] = $tailnum;
    if(!isset($tailCount[$tailnum])){
        $tailCount[$tailnum] = 0;
    }
    $tailCount[$tailnum]++;
}

arsort($tailCount);

echo $total = count($nextData);
echo "<br/>";
foreach ($tailCount as $tail => $num) {
echo '开出'.$tail.'个尾数 '.$num.'次 占比：' . round($num / $total, 4) * 100 . '%';
echo "<br/>";
}

// echo "<pre>";
// print_R($tailCount);
// print_r($nextData);
// echo "</pre>";
die;

// foreach ($allRedList as $k => $tmpReds) {
// 	if($k!=2115){
// 		continue;
// 	}
// 	foreach ($tmpReds as $tmpred) {
// 		if(!isset($nextData[$tmpred])){
// 		    $nextData[$tmpred] = array();
// 		}
// 		foreach ($allRedList as $k2 => $tmpReds2) {
// 			if($k2 >= $k){
// 				continue;
// 			}
// 			foreach ($tmpReds2 as $tmpred2) {
// 				if(!isset($nextData[$tmpred][$tmpred2])){
// 				    $nextData[$tmpred][$tmpred2] = 0;
// 				}
// 				$nextData[$tmpred][$tmpred2]++;
// 			}
// 		}
// 	}
// }

$red33 = array();
for ($i=1; $i < 34; $i++) { 
    $i<10 && $i = '0' . $i;
    $red33[$i] = 0;

    foreach ($nextData as $tmpred => $redCount) {
        $red33[$i] += $redCount[$i];
    }
}

arsort($red33);

echo "<pre>";
print_r($red33);die;
echo "</pre>";


$winRed = array();
if(isset($cp_dayid)){
    $cp_dayid = $cp_dayid + 1;
    $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid=".$cp_dayid);
    if(isset($row['id'])){
        $winRed = explode(",", $row['red_num']);
    }
}