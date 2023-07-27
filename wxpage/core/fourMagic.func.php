<?php

/*
04 09 05 16
14 07 11 02
15 06 10 03
01 12 08 13
*/
//反四幻图
$FM_row = array(
	1 => array('10', '03', '15', '06'),
	2 => array('08', '13', '01', '12'),
	3 => array('05', '16', '04', '09'),
	4 => array('11', '02', '14', '07'),
);

$FM_column = array(
	1 => array('10', '08', '05', '11'),
	2 => array('03', '13', '16', '02'),
	3 => array('15', '01', '04', '14'),
	4 => array('06', '12', '09', '07'),
);

//行列图
$FM_row = array(
	1 => array('13', '09', '05', '01'),
	2 => array('14', '10', '06', '02'),
	3 => array('15', '11', '07', '03'),
	4 => array('16', '12', '08', '04'),
);

$FM_column = array(
	1 => array('13', '14', '15', '16'),
	2 => array('09', '10', '11', '12'),
	3 => array('05', '06', '07', '08'),
	4 => array('01', '02', '03', '04'),
);

//四幻图
$FM_row = array(
	1 => array('04', '09', '05', '16'),
	2 => array('14', '07', '11', '02'),
	3 => array('15', '06', '10', '03'),
	4 => array('01', '12', '08', '13'),
);

$FM_column = array(
	1 => array('04', '14', '15', '01'),
	2 => array('09', '07', '06', '12'),
	3 => array('05', '11', '10', '08'),
	4 => array('16', '02', '03', '13'),
);

function get_row_col($blue){
	global $FM_row, $FM_column;

	$b_row = 0;
	foreach ($FM_row as $rownum => $rowblues) {
		if(!in_array($blue, $rowblues)) continue;

		$b_row = $rownum;
		break;
	}

	$b_column = 0;
	foreach ($FM_column as $columnnum => $columnblues) {
		if(!in_array($blue, $columnblues)) continue;

		$b_column = $columnnum;
		break;
	}

	return array($b_row, $b_column);
}

function get_blue_by_row_col($b_row, $b_col){
	global $FM_row, $FM_column;

	$rownum = $FM_row[$b_row];
	$colnum = $FM_column[$b_col];

	$blue = array_intersect($rownum, $colnum);
	$blue = array_values($blue);

	return $blue[0];
}

function get_rcblue($blue){
	global $FM_row, $FM_column;

	$rowcol = get_row_col($blue);

	$rownum = $rowcol[0];
	$colnum = $rowcol[1];

	$data = array();
	$rcblue = array();
	if(in_array($rownum, array(1,4)) && in_array($colnum, array(1,4))){
		if($rownum == 1){
			$rcblue[] = get_blue_by_row_col($rownum+1, $colnum);
			$rcblue[] = get_blue_by_row_col($rownum, $colnum == 1 ? $colnum+1 : $colnum-1);
			$rcblue[] = get_blue_by_row_col($rownum+1, $colnum == 1 ? $colnum+1 : $colnum-1);
		}else{
			if($colnum == 1){
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum);
				$rcblue[] = get_blue_by_row_col($rownum, $colnum+1);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum+1);
			}else{
				$rcblue[] = get_blue_by_row_col($rownum, $colnum-1);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum-1);
			}
		}
		$data['type'] = 1;
		$data['rcblue'] = $rcblue;
		return $data;
	}
	
	if(in_array($rownum, array(2,3)) && in_array($colnum, array(2,3))){
		$rcblue[] = get_blue_by_row_col($rownum-1, $colnum);
		$rcblue[] = get_blue_by_row_col($rownum+1, $colnum);
		$rcblue[] = get_blue_by_row_col($rownum, $colnum-1);
		$rcblue[] = get_blue_by_row_col($rownum, $colnum+1);

		$data['type'] = 0;
		$data['rcblue'] = $rcblue;
		return $data;
	}

	if(in_array($rownum, array(1,4)) && in_array($colnum, array(2,3)) || in_array($rownum, array(2,3)) && in_array($colnum, array(1,4))){
		if($rownum < 3){
			$d1 = $FM_row[$rownum+2];
		}else{
			$d1 = $FM_row[$rownum-2];
		}

		if($colnum < 3){
			$d2 = $FM_column[$colnum+2];
		}else{
			$d2 = $FM_column[$colnum-2];
		}

		$rcblue = array_merge($d1, $d2);
		$rcblue = array_unique($rcblue);

		$data['type'] = 2;
		$data['rcblue'] = $rcblue;
		$data['focus'] = array_intersect($d1, $d2);
		return $data;
	}
}

// $a = get_rcblue('15');
// print_r($a);die;