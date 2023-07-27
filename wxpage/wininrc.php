<?php  

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/core.func.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
LoginCheck();

set_time_limit(0);
// ini_set('memory_limit', '1024M');

$list = getRowColumn();
$rowarr = $list['row'];
$colarr = $list['col'];

$killRow = array(6);
$killCol = array(1, 3, 4);

$rowWinCfg = array(1=>1, 2=>1, 3=>1, 4=>1, 5=>2);
$colWinCfg = array(2=>2, 5=>2, 6=>2);

$killRow   = array();
$killCol   = array();
$rowWinCfg = array();
$colWinCfg = array();

//杀行及行出号个数配置
foreach ($row_win_num as $rownum => $winnum) {
	if($winnum == 0){
		$killRow[] = $rownum;
	}else{
		$rowWinCfg[$rownum] = $winnum;
	}
}
//杀列及列出号个数配置
foreach ($col_win_num as $colnum => $winnum) {
	if($winnum == 0){
		$killCol[] = $colnum;
	}else{
		$colWinCfg[$colnum] = $winnum;
	}
}

$myrow = $rowarr;
$mycol = $colarr;

//杀掉行
foreach ($myrow as $row => $arr) {
	if(in_array($row, $killRow)){
		unset($myrow[$row]);
	}
}

//杀掉列
foreach ($mycol as $col => $arr) {
	if(in_array($col, $killCol)){
		unset($mycol[$col]);
	}
}

//排除行中已杀掉的列红球
foreach ($myrow as $row => $rowReds) {
	foreach ($rowReds as $tmpcol => $tmpred) {
		$tmpcol2 = $tmpcol + 1;
		if(!isset($mycol[$tmpcol2])){
			unset($myrow[$row][$tmpcol]);
		}
	}
}

//排除列中已杀掉的行红球
foreach ($mycol as $col => $colReds) {
	foreach ($colReds as $tmprow => $tmpred) {
		$tmprow2 = $tmprow + 1;
		if(!isset($myrow[$tmprow2])){
			unset($mycol[$col][$tmprow]);
		}
	}
}

//剩余红球
$myReds = array();
foreach ($myrow as $row => $rowReds) {
	foreach ($rowReds as $tmpred) {
		$myReds[] = $tmpred;
	}
}

//把剩余的红球进行排列组合
$myRedList = combination($myReds, 6);

$myWinRedList = array();
foreach ($myRedList as $redList) {
	$tmpRowWinCfg = array();
	foreach ($myrow as $row => $rowReds) {
		foreach ($redList as $red) {
			if(in_array($red, $rowReds)){
				if(!isset($tmpRowWinCfg[$row])){
					$tmpRowWinCfg[$row] = 0;
				}
				$tmpRowWinCfg[$row]++;
			}
		}
	}
	//把行出球个数和预测不一致的过滤掉
	$diff1 = array_diff_assoc($rowWinCfg, $tmpRowWinCfg);
	if(!empty($diff1)){
		continue;		
	}

	$tmpColWinCfg = array();
	foreach ($mycol as $col => $colReds) {
		foreach ($redList as $red) {
			if(in_array($red, $colReds)){
				if(!isset($tmpColWinCfg[$col])){
					$tmpColWinCfg[$col] = 0;
				}
				$tmpColWinCfg[$col]++;
			}
		}
	}
	//把列出球个数和预测不一致的过滤掉
	$diff2 = array_diff_assoc($colWinCfg, $tmpColWinCfg);
	if(!empty($diff2)){
		continue;
	}

	//剩余就是符合筛选的排列组合
	$myWinRedList[] = $redList;
}

$curmiss = redMissing();
$killReds = explode('-', $killReds);

$prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);
$tableInfo = array();
foreach ($myWinRedList as $redList) {
	$bigsmall = array(0=>0, 1=>0);
	$oddeven  = array(0=>0, 1=>0);
	$primehe  = array(0=>0, 1=>0);
	$redarea  = array(0=>0, 1=>0, 2=>0);
	$miss     = array(0=>0, 1=>0, 2=>0);

	$hasKill = false;
	foreach ($redList as $red) {
		if(in_array($red, $killReds)){
			$hasKill = true;
			break;
		}
		$red > 16 && $bigsmall[0]++;
		$red < 17 && $bigsmall[1]++;
		$red % 2 == 1 && $oddeven[0]++;
		$red % 2 == 0 && $oddeven[1]++;
		$red < 12 && $redarea[0]++;
		$red >= 12 && $red <= 22 && $redarea[1]++;
		$red > 22 && $redarea[2]++;
		in_array($red, $prime) ? $primehe[0]++ : $primehe[1]++;

		$curmiss[$red] < 5 && $miss[0]++;
		$curmiss[$red] >= 5 && $curmiss[$red] < 10 && $miss[1]++;
		$curmiss[$red] >= 10 && $miss[2]++;
	}
	if($hasKill){
		continue;
	}

	$sum = array_sum($redList);

	$tmp = array();
	$tmp['reds']     = implode(" ", $redList);
	$tmp['bigsmall'] = implode(":", $bigsmall);
	$tmp['oddeven']  = implode(":", $oddeven);
	$tmp['primehe']  = implode(":", $primehe);
	$tmp['redarea']  = implode(":", $redarea);
	$tmp['miss']     = implode(":", $miss);
	$tmp['sum']      = $sum;

	if(isset($filter_hotcool) && $tmp['miss'] != implode(":", $win_hotcoll)){
		continue;
	}
	if(isset($filter_redarea) && $tmp['redarea'] != implode(":", $win_areanum)){
		continue;
	}
	if(isset($filter_sum) && ($sum < $win_sum[0] || $sum > $win_sum[1])){
		continue;
	}
	if(isset($filter_bigball) && $tmp['bigsmall'] != implode(":", array($win_bignum, 6-$win_bignum))){
		continue;
	}
	if(isset($filter_odd) && $tmp['oddeven'] != implode(":", array($win_oddnum, 6-$win_oddnum))){
		continue;
	}
	if(isset($filter_prime) && $tmp['primehe'] != implode(":", array($win_primenum, 6-$win_primenum))){
		continue;
	}

	$tableInfo[] = $tmp;
}


if(isset($filter_ssq)){
	$str = "";
	$index = 0;
	foreach ($myWinRedList as $winReds) {
		$index++;
		// if($index % 2 == 0){
			$str .= implode(" ", $winReds);
			$str .= "<br/>";
		// }else{
		// 	$str .= implode(" ", $winReds);
		// 	$str .= "-";
		// }
	}
	echo $str;
	die;
}

$table = '
<table class="table table-bordered">
    <thead>
        <tr>
            <th>预测红球（'.count($tableInfo).'注）</th>
            <th>大小比</th>
            <th>奇偶比</th>
            <th>质合比</th>
            <th>三区比</th>
            <th>热温冷</th>
            <th>和数值</th>
        </tr>
    </thead>
    <tbody>';

foreach ($tableInfo as $v) {
	$table .= '<tr>
        <td>'.$v['reds'].'</td>
        <td>'.$v['bigsmall'].'</td>
        <td>'.$v['oddeven'].'</td>
        <td>'.$v['primehe'].'</td>
        <td>'.$v['redarea'].'</td>
        <td>'.$v['miss'].'</td>
        <td>'.$v['sum'].'</td>
    </tr>';
}
$table .= '</tbody></table>';
echo $table;