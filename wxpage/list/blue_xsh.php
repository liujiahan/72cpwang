<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_history` order by cp_dayid asc");
$index = 0;
$allRedList = array();
while($row = $dosql->GetArray()){
    $allRedList[$index]['red_num']  = explode(",", $row['red_num']);
    $allRedList[$index]['blue_num'] = $row['blue_num'];
    $allRedList[$index]['cp_dayid'] = $row['cp_dayid'];
	$index++;
}
// $allRedList = array_reverse($allRedList);
// $allRedList = array_slice($allRedList, 0, 10);

$result = array();
$total_1 = 0;
$total_1_win = 0;
foreach ($allRedList as $index => $v) {
	$tmp_1 = array();
	if(isset($allRedList[$index-1]) && isset($allRedList[$index-2]) && isset($allRedList[$index-3]) && isset($allRedList[$index-4])){
		$bluesum = $allRedList[$index]['blue_num'] + 
		            $allRedList[$index-1]['blue_num'] + 
		            $allRedList[$index-2]['blue_num'] + 
		            $allRedList[$index-3]['blue_num'] + 
		            $allRedList[$index-4]['blue_num'];

		$blueavg = intval($bluesum / 5);

		$blue1 = $blueavg - 5 > 0 ? $blueavg - 5 : 0;
		$blue2 = $blueavg + 5 < 17 ? $blueavg + 5 : 16;

		$tmp_1['area'] = array($blue1, $blue2);
		$tmp_1['is_win'] = 0;
		$tmp_1['open'] = 0;
		$tmp_1['blue'] = 0;
		if(isset($allRedList[$index+1])){
			$tmp_1['open'] = 1;
			$curblue = $allRedList[$index+1]['blue_num'];
			$tmp_1['blue'] = $curblue;

			if($curblue >= $blue1 && $curblue <= $blue2){
			    // $total_55_win++;
				$tmp_1['is_win'] = 1;
			}
		}
	}


    $red_num = $v['red_num'];
    $red_num = array_reverse($red_num);
    $diffArr = array();
    foreach ($red_num as $k1 => $red1) {
        foreach ($red_num as $k2 => $red2) {
            if($k2 > $k1){
                $val = abs($red1 - $red2);
                // echo $red1 . ' - ' . $red2 . ' = ' . $val;
                // echo "	";
                if($val <= 16){
                    $diffArr[] =  $val;
                }
            }
        }
    }
    $diffArr = array_unique($diffArr);
    sort($diffArr);
    // echo implode(" ", $diffArr);die;

    $tmp_2 = array();
    $tmp_2['blue_list'] = $diffArr;
    $tmp_2['is_win'] = 0;
    $tmp_2['open'] = 0;
	$tmp_2['blue'] = 0;
	$tmp_2['id'] = 0;

    if(isset($allRedList[$index+1])){
    	$nextblue = $allRedList[$index+1]['blue_num'];
		$tmp_2['blue'] = $nextblue;
		$tmp_2['id'] = $allRedList[$index+1]['cp_dayid'];
    	if(in_array($nextblue, $diffArr)){
		    $tmp_2['is_win'] = 1;
		    $total_1_win++;
    	}
    	// echo $tmp_2['is_win'] . ' ';
	    $tmp_2['open'] = 1;
    	$total_1++;
    }

    $tmp['tmp1'] = $tmp_1;
    $tmp['tmp2'] = $tmp_2;

    $result[] = $tmp;
}


$sqlList = array();

foreach ($result as $key => $v) {
	if($key < 4){
		continue;
	}

	if($v['tmp2']['open'] == 1){
		$cp_dayid       = $v['tmp2']['id'];
		$blue_num       = $v['tmp2']['blue'];
		$blue_range     = implode(',', $v['tmp1']['area']);
		$blue_range_win = $v['tmp1']['is_win'];
		
		$blue_list      = implode(',',$v['tmp2']['blue_list']);
		$blue_list_win  = $v['tmp2']['is_win'];
		$sqlList[] = '("'.$cp_dayid.'", "'.$blue_num.'", "'.$blue_range.'", "'.$blue_range_win.'", "'.$blue_list.'", "'.$blue_list_win.'")';
	}
}

if($sqlList){
	$dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_blue_xsh` (cp_dayid,blue_num,blue_range,blue_range_win,blue_list,blue_list_win) VALUES " . implode(',', $sqlList));
}
echo count($sqlList);die;

echo "<pre>";
print_r($sqlList);
die;
echo "</pre>";
die;

// 1、五期平均正负5锁定下期蓝球范围正确率
// 2、最大-最小成功率
$oneinfo     = array(0=>0, 1=>0);
$twoinfo = array(0=>0, 1=>0);
$threeinfo = array(0=>0, 1=>0);
$fourinfo = array(0=>0, 1=>0);
foreach ($result as $index => $tmp) {
	$tmp1 = $tmp['tmp1'];
	if(isset($tmp1['open']) && $tmp1['open'] == 1){
		$oneinfo[0]++;
		if($tmp1['is_win']){
			$oneinfo[1]++;
		}
		// echo $tmp1['is_win'] . ' ';
	}

	$tmp2 = $tmp['tmp2'];
	if($tmp2['open'] == 1){
		$twoinfo[0]++;
		if($tmp2['is_win']){
			$twoinfo[1]++;
		}
	}

	if(isset($tmp1['open']) && isset($tmp2['open']) && $tmp1['open'] == 1){
		$threeinfo[0]++;
		$tmp1['is_win'] && $tmp2['is_win'] && $threeinfo[1]++;
	}

	if(isset($tmp1['open']) && $tmp1['is_win']){
		$fourinfo[0]++;
		if($tmp2['is_win'] == 1){
			$fourinfo[1]++;
		}
	}
}

echo "<br/>";
echo '五期平均正负5锁定下期蓝球范围'.$oneinfo[0].'正确率'.$oneinfo[1].'次 占比：' . round($oneinfo[1] / $oneinfo[0], 4) * 100 . '%';
echo "<br/>";

echo '最大-最小命中数'.$twoinfo[0].'正确'.$twoinfo[1].'次 占比：' . round($twoinfo[1] / $twoinfo[0], 4) * 100 . '%';
echo "<br/>";

echo '都命中次数'.$threeinfo[0].'正确'.$threeinfo[1].'次 占比：' . round($threeinfo[1] / $threeinfo[0], 4) * 100 . '%';
echo "<br/>";

echo '都命中次数'.$fourinfo[0].'正确'.$fourinfo[1].'次 占比：' . round($fourinfo[1] / $fourinfo[0], 4) * 100 . '%';
echo "<br/>";


echo "<pre>";
print_r($oneinfo);
print_r($twoinfo);
print_r($threeinfo);
die;
echo "</pre>";

echo "<br/>";
echo '最大-最小命中数'.$total_1_win.'次 占比：' . round($total_1_win / $total_1, 4) * 100 . '%';
echo "<br/>";
die;

$total_55 = 0;
$total_55_win = 0;
foreach ($allRedList as $index => $v) {
    // if($index < 5)
    //     continue;

    if(isset($allRedList[$index+1]) && isset($allRedList[$index+2]) && isset($allRedList[$index+3])
        && isset($allRedList[$index+4]) && isset($allRedList[$index+5])){
        $bluesum = $allRedList[$index+1]['blue_num'] + 
                    $allRedList[$index+2]['blue_num'] + 
                    $allRedList[$index+3]['blue_num'] + 
                    $allRedList[$index+4]['blue_num'] + 
                    $allRedList[$index+5]['blue_num'];

        $blueavg = intval($bluesum / 5);

        $blue1 = $blueavg - 5 > 0 ? $blueavg - 5 : 0;
        $blue2 = $blueavg + 5 < 17 ? $blueavg + 5 : 16;

        $curblue = $v['blue_num'];

        if($curblue >= $blue1 && $curblue <= $blue2){
            $total_55_win++;
        }
        $total_55++;
    }
}

echo '五期平均+—5命中数'.$total_55_win.'次 占比：' . round($total_55_win / $total_55, 4) * 100 . '%';
echo "<br/>";
die;

echo $total_55_win;
echo "<br/>";
echo $total_55;
die;

// $nextData = array();
// foreach ($allRedList as $k => $tmpReds) {
//     foreach ($tmpReds as $tmpred) {
//     	if(isset($allRedList[$k+1])){
//     		$nextReds = $allRedList[$k+1];
//     		if(in_array($tmpred, $nextReds)){
//     		    if(!isset($nextData[$tmpred])){
//     		        $nextData[$tmpred] = array();
//     		    }
//     		    foreach ($nextReds as $tmpred2) {
//     		        if(!isset($nextData[$tmpred][$tmpred2])){
//     		            $nextData[$tmpred][$tmpred2] = 0;
//     		        }
//     		        $nextData[$tmpred][$tmpred2]++;
//     		    }
//     		}
//     	}
//     }
// }