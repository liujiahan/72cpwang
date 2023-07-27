<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_history`");
$index = 0;
$allRedList = array();
while($row = $dosql->GetArray()){
	$allRedList[$index] = explode(",", $row['red_num']);
	$index++;
}
// echo $index;die;

$nextData = array();
foreach ($allRedList as $k => $tmpReds) {
    foreach ($tmpReds as $tmpred) {
    	if(isset($allRedList[$k+1])){
    		$nextReds = $allRedList[$k+1];
    		if(in_array($tmpred, $nextReds)){
    		    if(!isset($nextData[$tmpred])){
    		        $nextData[$tmpred] = array();
    		    }
    		    foreach ($nextReds as $tmpred2) {
    		        if(!isset($nextData[$tmpred][$tmpred2])){
    		            $nextData[$tmpred][$tmpred2] = 0;
    		        }
    		        $nextData[$tmpred][$tmpred2]++;
    		    }
    		}
    	}
    }
}

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