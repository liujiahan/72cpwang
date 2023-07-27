<?php  

require_once dirname(__FILE__) . '/../../include/config.inc.php';
require_once dirname(__FILE__) . '/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';

LoginCheck();

set_time_limit(0);
ini_set('memory_limit', '1024M');

$reds = array();
for ($i=1; $i < 34; $i++) { 
    $i < 10 && $i = '0' . $i;
    $reds[] = $i;
}
$oneReds   = array_slice($reds, 0, 11);
$twoReds   = array_slice($reds, 11, 11);
$threeReds = array_slice($reds, 22);

$column_row = array();
$row_column = array();

$rowcol_num = array(1, 2, 3, 4, 5, 6);

//列数据
foreach ($rowcol_num as $col) {
	foreach ($reds as $key => $red) {
		if($red % 6 == 0 && $col == 6){
			if(!isset($column_row[$col])){
				$column_row[$col] = array();
			}
			$column_row[$col][] = $red;
		}else if($red % 6 == $col){
			if(!isset($column_row[$col])){
				$column_row[$col] = array();
			}
			$column_row[$col][] = $red;
		}
	}
}

//行数据
foreach ($rowcol_num as $row) {
	$row_column[$row] = array_slice($reds, ($row-1)*6, 6);
}

//杀行判断
$killrow = array();
foreach ($row_win_num as $row => $row_rednum) {
	if($row_rednum == 0){
		$killrow[] = $row;
	}
}

//杀列判断
$killcolumn = array();
foreach ($col_win_num as $column => $column_rednum) {
	if($column_rednum == 0){
		$killcolumn[] = $column;
	}
}

$killReds = array();
for ($i=1; $i <= count($rowcol_num); $i++) { 
	for ($j=1; $j <= count($rowcol_num); $j++) { 
		if(in_array($i, $killrow) && isset($row_column[$i][$j-1])){
			$killReds[] = $row_column[$i][$j-1];
		}
		if(in_array($i, $killcolumn) && isset($column_row[$i][$j-1])){
			$killReds[] = $column_row[$i][$j-1];
		}
	}
}
$killReds = array_unique($killReds);
sort($killReds);

$yuceReds = array();
$yuceReds = array_diff($reds, $killReds);
$yuceReds = array_values($yuceReds);

echo "<h3>";
echo implode(" ", $yuceReds);die;
echo "</h3>";
// print_r($yuceReds);die;

//获取组合情况
$yuceReds = combination($yuceReds, 6);

$yuceList = array();
foreach ($yuceReds as $key => $redsGroup) {
	$error = false;
	for ($i=1; $i <= 6; $i++) { 
		if(!in_array($i, $killrow)){
			$jjReds = array_intersect($redsGroup, $row_column[$i]);
			if(count($jjReds)>=4){
				$error = true;
			}
		}
	}

	if($error){
		continue;
	}else{
		for ($i=1; $i <= 6; $i++) { 
			if(!in_array($i, $killcolumn)){
				$jjReds = array_intersect($redsGroup, $column_row[$i]);
				if(count($jjReds)>=4){
					$error = true;
				}
			}
		}
	}

	if($error){
		continue;
	}

	$arr = array_intersect($redsGroup, $oneReds);
	if(count($arr) >= 5){
		continue;
	}
	$arr = array_intersect($redsGroup, $twoReds);
	if(count($arr) >= 5){
		continue;
	}
	$arr = array_intersect($redsGroup, $threeReds);
	if(count($arr) >= 5){
		continue;
	}

	$sum = array_sum($redsGroup);

	if($sum < 90 || $sum > 120){
		continue;
	}

	$one_num    = 0; //一区出球
    $two_num    = 0; //二区出球
    $three_num  = 0; //三区出球

    $bignum     = 0; //大号出球
    $oddnum     = 0; //奇数球
    $primenum   = 0; //质数出球
	foreach ($redsGroup as $red) {
		if($red >=1 && $red <= 11){
            $one_num++;
        }
        if($red >=12 && $red <= 22){
            $two_num++;
        }
        if($red >=23 && $red <= 33){
            $three_num++;
        }

        $red > 16 && $bignum++;
        $red % 2 == 1 && $oddnum++;
        in_array($red, $prime) && $primenum++;
	}

	$filter_redarea = 1;
	$win_areanum = array(0=>3, 1=>1, 2=>2);
	//三区出球判断
    if(isset($filter_redarea) && !($one_num == $win_areanum[0] && $two_num == $win_areanum[1] && $three_num == $win_areanum[2])){
        continue;
    }

    $filter_bigball = 1;
    $win_bignum = 3;
    //大号出球判断
    if(isset($filter_bigball) && $bignum != $win_bignum){
        continue;
    }

    $filter_odd = 1;
    $win_oddnum = 4;
    //奇数出球判断
    if(isset($filter_odd) && $oddnum != $win_oddnum){
        continue;
    }

    // $filter_prime = 1;
    $win_bignum = 3;
    //质数出球判断
    if(isset($filter_prime) && $primenum != $win_primenum){
        continue;
    }

	$yuceList[] = $redsGroup;
}


echo "<pre>";
echo count($yuceReds);
echo "<br>";
echo count($yuceList);
echo "<br>";
print_r($yuceList);die;
echo "</pre>";

