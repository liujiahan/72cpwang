<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';

if(!isset($token) || $token != md5('72cpwang')){
	LoginCheck();

	if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
		ShowMsg("Permission denied","-1");
	    exit;
	}
}

if($action == 'offset_rowcolumn'){
	//读取当前行列出球数
	if($cp_dayid){
		$cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	}else{
		$cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	}

	$curReds  = explode(",", $cur['red_num']);
	
	$rowColumn = getRowColumn();
	$curWinRC = getWinRowCol($curReds, $rowColumn);

	$nextWinRc = array();
	if($cp_dayid){
		$nextid = $cp_dayid+1;
		$next = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$nextid'");
		if(isset($next['id'])){
			$nextReds  = explode(",", $next['red_num']);
			$nextWinRc = getWinRowCol($nextReds, $rowColumn);
		}
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 100");
	$data = array();
	while($row = $dosql->GetArray()){
	    $data[$row['cp_dayid']] = $row;
	}
	$data = array_reverse($data);

	$winrowMiss = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
	$columnMiss = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);

	$rowData = array();
	$colData = array();
	foreach ($data as $cp_dayid => $row) {
		$reds = explode(',', $row['red_num']);

		$winrow = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
		foreach ($rowColumn['row'] as $rownum => $rowreds) {
		    foreach ($reds as $red) {
		        if(in_array($red, $rowreds)){
		            $winrow[$rownum]++;
		        }
		    }
		}
		foreach ($winrow as $row => $rownum) {
			if(!isset($rowData[$row])){
				$rowData[$row] = array();
			}
			$rowData[$row][] = $rownum;
		}

		$wincolumn = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
		foreach ($rowColumn['col'] as $columnnum => $columnreds) {
		    foreach ($reds as $red) {
		        if(in_array($red, $columnreds)){
		            $wincolumn[$columnnum]++;
		        }
		    }
		}
		foreach ($wincolumn as $col => $colnum) {
			if(!isset($colData[$row])){
				$colData[$col] = array();
			}
			$colData[$col][] = $colnum;
		}
	}

	$next_row_trend = array();
	foreach ($curWinRC['row'] as $row => $rownum) {
		if(!isset($next_row_trend[$row])){
			$next_row_trend[$row] = array();
		}
		if(isset($rowData[$row])){
			$tmpRowData = $rowData[$row];
			foreach ($tmpRowData as $key => $tmp_rownum) {
				if($tmp_rownum == $rownum && isset($tmpRowData[$key+1])){
					$next_rownum = $tmpRowData[$key+1];
					if(!isset($next_row_trend[$row][$next_rownum])){
						$next_row_trend[$row][$next_rownum] = 0;
					}
					$next_row_trend[$row][$next_rownum]++;
				}
			}
		}
	}
	foreach ($next_row_trend as $key => &$value) {
		arsort($value);
	}
	// print_r($next_row_trend);die;

	$next_col_trend = array();
	foreach ($curWinRC['col'] as $col => $colnum) {
		if(!isset($next_col_trend[$col])){
			$next_col_trend[$col] = array();
		}
		if(isset($colData[$col])){
			$tmpColData = $colData[$col];
			foreach ($tmpColData as $key => $tmp_colnum) {
				if($tmp_colnum == $colnum && isset($tmpColData[$key+1])){
					$next_colnum = $tmpColData[$key+1];
					if(!isset($next_col_trend[$col][$next_colnum])){
						$next_col_trend[$col][$next_colnum] = 0;
					}
					$next_col_trend[$col][$next_colnum]++;
				}
			}
		}
	}
	foreach ($next_col_trend as $key => &$value) {
		arsort($value);
	}

	foreach ($next_row_trend as $row => $result) {
		$sum = array_sum($result);
		echo "第{$row}行出球个数{$curWinRC['row'][$row]}个，";
		echo "近100期共出现{$sum}期\n";
		foreach ($result as $miss => $count) {
			echo "下期出{$miss}个球的次数{$count}，概率是： ".(round($count / $sum, 4)*100).'%';
			if(isset($nextWinRc['row'][$row]) && $nextWinRc['row'][$row] == $miss){
				echo "【中】";
			}
			echo "\n";
		}
		echo "\n";
	}

	foreach ($next_col_trend as $col => $result) {
		$sum = array_sum($result);
		echo "第{$col}列出球个数{$curWinRC['col'][$col]}个，";
		echo "近100期共出现{$sum}期\n";
		foreach ($result as $miss => $count) {
			echo "下期出{$miss}个球的次数{$count}，概率是： ".(round($count / $sum, 4)*100).'%';
			if(isset($nextWinRc['col'][$col]) && $nextWinRc['col'][$col] == $miss){
				echo "【中】";
			}
			echo "\n";
		}
		echo "\n";
	}
}

else if($action == 'offset_red_percent'){
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_percent`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$cp_dayid   = $row['cp_dayid'];
		$opencode   = $row['opencode'];
		$red_num    = $row['red_num'];
		$redBall    = explode(',', $row['red_num']);
		$redBalls05 = array();
		$redBalls10 = array();

		$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 10", "10");
		while($row2 = $dosql->GetArray("10")){
			$redBalls10[] = explode(',', $row2['red_num']);
		}
		$redBalls05 = array_slice($redBalls10, 0, 5);

		$reds10 = array();
		$reds05 = array();
		foreach ($redBalls10 as $reds) {
			foreach ($reds as $red) {
				if(!isset($reds10[$red])){
					$reds10[$red] = 0;
				}
				$reds10[$red]++;
			}
		}

		foreach ($redBalls05 as $reds) {
			foreach ($reds as $red) {
				if(!isset($reds05[$red])){
					$reds05[$red] = 0;
				}
				$reds05[$red]++;
			}
		}

		$tuice_reds = array();
		foreach ($reds10 as $red => $num) {
			if($num < 2){
				continue;
			}
			if(isset($reds05[$red]) && $reds05[$red] > 0){
				$tuice_reds[] = $red;
			}
		}
		sort($tuice_reds);
		$win_num = 0;
		foreach ($tuice_reds as $red) {
			if(in_array($red, $redBall)){
				$win_num++;
			}
		}
		$tuice_reds = implode(',', $tuice_reds);

		$sql = "INSERT INTO `#@__caipiao_red_percent` (cp_dayid,opencode,red_num, tuice_reds, win_num, isdo) VALUES ('".$cp_dayid."','".$opencode."','".$red_num."','".$tuice_reds."','".$win_num."', '1')";
		$dosql->ExecNoneQuery($sql);
	}
}

else if($action == 'red_double_win'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = $i;
	}

	$data = array();
	foreach ($allRed as $key => $redball) {
		$data[$redball] = array();
		$allWinReds = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_33_win` WHERE redball='$redball' ORDER BY cp_dayid ASC");
		while($row = $dosql->GetArray()){
			if(!isset($allWinReds[$row['redball']])){
				$allWinReds[$row['redball']] = array();
			}
			$allWinReds[$row['redball']][] = $row['is_win'];
		}

		$redArr = $allWinReds[$redball];

		$win = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0);
		foreach ($redArr as $key => $is_win) {
			isset($redArr[$key+1]) && isset($redArr[$key+2]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 0 && $win[1]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 0 && $win[2]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 0 && $win[3]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 0 && $win[4]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 0 && $win[5]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && isset($redArr[$key+7]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 1 && $redArr[$key+7] == 0 && $win[6]++;

			isset($redArr[$key+1]) && isset($redArr[$key+2]) && isset($redArr[$key+3]) && isset($redArr[$key+4]) && isset($redArr[$key+5]) && isset($redArr[$key+6]) && isset($redArr[$key+7]) && isset($redArr[$key+8]) && 
			$is_win == 0 && $redArr[$key+1] == 1 && $redArr[$key+2] == 1 && $redArr[$key+3] == 1 && $redArr[$key+4] == 1 && $redArr[$key+5] == 1 && $redArr[$key+6] == 1 && $redArr[$key+7] == 1 && $redArr[$key+8] == 0 && $win[7]++;

		}
		$wingt2 = 0;
		foreach ($win as $key => $value) {
			if($key == 1) continue;
			$wingt2 += $value;
		}
		$win['double_win'] = round($win[1] / $wingt2, 1);
		$data[$redball] = $win;
	}

	$sum = array();
	foreach ($data as $redball => $rows) {
		foreach ($rows as $key => $v) {
			if(!isset($sum[$key])){
				$sum[$key] = 0;
			}
			$sum[$key] += $v;
		}
	}
	$avg = array();
	foreach ($sum as $key => $v) {
		$avg[$key] = round($v / count($data), 1);
	}
	$data['avg'] = $avg;
	$data['sum'] = $sum;

	$sqlArr = array();
	foreach ($data as $counttype => $row) {
		$sqlArr[] = '("'.$counttype.'", "'.$row[1].'", "'.$row[2].'", "'.$row[3].'", "'.$row[4].'", "'.$row[5].'", "'.$row[6].'", "'.$row[7].'", "'.$row['double_win'].'")';
	}
	$dosql->ExecNoneQuery("TRUNCATE TABLE `#@__caipiao_red_double_win`");
	if($sqlArr){
		$sql = "INSERT INTO `#@__caipiao_red_double_win` (counttype, win_1, win_2, win_3, win_4, win_5, win_6, win_7, double_win) VALUES " . implode(',', $sqlArr);
		$dosql->ExecNoneQuery($sql);
	}
}

else if($action == 'count_double_num'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = array();
	}

	$allWinReds = array();
	$max = $dosql->GetOne('SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_red_33_win`');
	$cp_dayid = 2003000;
	if(!empty($max['cp_dayid'])){
		$cp_dayid = $max['cp_dayid'];
	}

	$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>'$cp_dayid' ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$allWinReds[$row['cp_dayid']] = explode(',', $row['red_num']);
	}

	foreach ($allWinReds as $cp_dayid => $win_red) {
		$sqlArr = array();
		foreach ($allRed as $red => $v) {
			if(in_array($red, $win_red)){
				$sqlArr[] = '("'.$cp_dayid.'", "'.$red.'", "1")';
			}else{
				$sqlArr[] = '("'.$cp_dayid.'", "'.$red.'", "0")';
			}
		}
		if($sqlArr){
			$sql = "INSERT INTO `#@__caipiao_red_33_win` (cp_dayid, redball, is_win) VALUES " . implode(',', $sqlArr);
			$dosql->ExecNoneQuery($sql);
		}
	}
}

else if($action == 'red_miss_win'){
	$allRed = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $allRed[$i] = $i;
	}

	$missWin = array();
	for ($i=1; $i <= 45; $i++) { 
		$fmt = '1';
		for ($j=1; $j <= $i; $j++) { 
			$fmt .= '0';
		}
		$fmt .= '1';
		$missWin[$i] = $fmt;
	}

	$data = array();
	foreach ($allRed as $key => $redball) {
		$data[$redball] = array();
		$allWinReds = array();
		$dosql->Execute("SELECT * FROM `#@__caipiao_red_33_win` WHERE redball='$redball' ORDER BY cp_dayid ASC");
		while($row = $dosql->GetArray()){
			if(!isset($allWinReds[$row['redball']])){
				$allWinReds[$row['redball']] = array();
			}
			$allWinReds[$row['redball']][] = $row['is_win'];
		}

		$redArr = $allWinReds[$redball];
		$redWinMiss = array();
		foreach ($redArr as $key => $is_win) {
			if($is_win != 1) continue;

			for ($i=1; $i <= 45; $i++) { 
				$fmt = '1';
				for ($j=1; $j <= $i; $j++) { 
					if(isset($redArr[$key+$j])){
						$fmt .= $redArr[$key+$j];
					}
				}
				if(isset($redArr[$key+$j+1])){
					$fmt .= $redArr[$key+$j+1];
				}
				if($missWin[$i] == $fmt){
					if(!isset($redWinMiss[$i])){
						$redWinMiss[$i] = 0;
					}
					$redWinMiss[$i]++;
				}else{
					if(!isset($redWinMiss[$i])){
						$redWinMiss[$i] = 0;
					}
				}
			}
		}
		ksort($redWinMiss);
		$data[$redball] = $redWinMiss;
	}

	foreach ($data as $redball => $redWinMiss) {
		foreach ($redWinMiss as $key => $value) {
			if($key > 35 && $value == 0){
				unset($data[$redball][$key]);
			}
		}
	}

	$dosql->ExecNoneQuery("TRUNCATE TABLE `#@__caipiao_red_miss_win`");
	foreach ($data as $redball => $redWinMiss) {
		$redWinMiss = serialize($redWinMiss);
		$sql = "INSERT INTO `#@__caipiao_red_miss_win` (redball, miss_win) VALUES ('".$redball."', '".$redWinMiss."')";
		$dosql->ExecNoneQuery($sql);
	}
}

else if($action == 'red_partner_num'){
	$partner = array();
	for ($i=1; $i < 34; $i++) { 
		$i < 10 && $i = '0' . $i;
		$partner[$i] = array();
		for ($j=1; $j < 34; $j++) { 
			$j < 10 && $j = '0' . $j;
			if($i == $j){
				$partner[$i][$j] = '*	*';
			}else{
				$partner[$i][$j] = 0;
			}
		}
	}

	$allWinReds = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
	while($row = $dosql->GetArray()){
		$allWinReds[$row['cp_dayid']] = $winReds = explode(',', $row['red_num']);
		foreach ($winReds as $red1) {
			foreach ($winReds as $red2) {
				if($red1 == $red2) continue;
				$partner[$red1][$red2]++;
				$partner[$red2][$red1]++;
			}
		}
	}

	$data = array();
	foreach ($partner as $red => $nums) {
		$data[$red] = array();
		$data[$red]['num'] = $red;
		foreach ($nums as $k => $tmpred) {
			$data[$red][$k] = $tmpred;
		}
	}

	require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');
	$excel = new ExcelData();
	
	$allReds = array();
	for ($i=1; $i <=33 ; $i++) { 
		$i < 10 && $i = '0' . $i;
		$allReds[$i] = $i;
	}
	$headerColumn = array();
	$widthCfg     = array();
	$widthCfg['num']     = 5;
	$headerColumn['num'] = '数字';
	foreach ($allReds as $red) {
		$widthCfg[$red]     = 4;
		$headerColumn[$red] = $red;
	}
	$data[] = $headerColumn;

	$excelConfig = array(
		'filename'  => '伴侣数字',
		'sheetname' => '伴侣数字',
		'format'    => 'xlsx',
	);
	$excel->export($data, $headerColumn, $excelConfig, $widthCfg);
}


else if($action == 'red_next_num'){
	$cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
	$curReds = explode(",", $cur['red_num']);

	$dosql->Execute("SELECT * FROM `#@__caipiao_history`");
	$allRed = array();
	while($row = $dosql->GetArray()){
		$allRed[$row['cp_dayid']] = explode(",", $row['red_num']);
	}

	$nextData = array();
	foreach ($allRed as $cp_dayid => $tmpReds) {
		foreach ($tmpReds as $tmpred) {
			if(in_array($tmpred, $curReds) && isset($allRed[$cp_dayid+1])){
				if(!isset($nextData[$tmpred])){
					$nextData[$tmpred] = array();
				}
				foreach ($allRed[$cp_dayid+1] as $tmpred2) {
					if(!isset($nextData[$tmpred][$tmpred2])){
						$nextData[$tmpred][$tmpred2] = 0;
					}
					$nextData[$tmpred][$tmpred2]++;
				}
			}
		}
	}

	$red33 = array();
	for ($i=1; $i < 34; $i++) { 
	    $i<10 && $i = '0' . $i;
	    $red33[$i] = 0;

		foreach ($nextData as $tmpred => $redCount) {
			$red33[$i] += $redCount[$i];
		}
	}

	arsort($red33);




	print_r($nextData);
	print_r($red33);die;
}