<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/core.func.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';

/*
04 09 05 16
14 07 11 02
15 06 10 03 
01 12 08 13
*/

$row = array(
	1 => array('04', '09', '05', '16'),
	2 => array('14', '07', '11', '02'),
	3 => array('15', '06', '10', '03'),
	4 => array('01', '12', '08', '13'),
);

$column = array(
	1 => array('04', '14', '15', '01'),
	2 => array('09', '07', '06', '12'),
	3 => array('05', '11', '10', '08'),
	4 => array('16', '02', '03', '13'),
);

function get_row_col($blue){
	global $row, $column;

	$b_row = 0;
	foreach ($row as $rownum => $rowblues) {
		if(!in_array($blue, $rowblues)) continue;

		$b_row = $rownum;
		break;
	}

	$b_column = 0;
	foreach ($column as $columnnum => $columnblues) {
		if(!in_array($blue, $columnblues)) continue;

		$b_column = $columnnum;
		break;
	}

	return array($b_row, $b_column);
}

function get_blue_by_row_col($b_row, $b_col){
	global $row, $column;

	$rownum = $row[$b_row];
	$colnum = $column[$b_col];

	$blue = array_intersect($rownum, $colnum);
	$blue = array_values($blue);

	return $blue[0];
}

function get_rcblue($blue){
	global $row, $column;

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
			if($rowcol == 1){
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum);
				$rcblue[] = get_blue_by_row_col($rownum, $colnum+1);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum+1);
			}else{
				$rcblue[] = get_blue_by_row_col($rownum, $colnum-1);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum);
				$rcblue[] = get_blue_by_row_col($rownum-1, $colnum-1);
			}
			$data['type'] = 1;
			$data['rcblue'] = $rcblue;
			return $data;
		}
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
		if($rownum == 1){
			$d1 = $row[$rownum+2];
		}else{
			$d1 = $row[$rownum-2];
		}

		if($colnum < 3){
			$d2 = $column[$colnum+2];
		}else{
			$d2 = $column[$colnum-2];
		}

		$rcblue = array_merge($d1, $d2);
		$rcblue = array_unique($rcblue);

		$data['type'] = 2;
		$data['rcblue'] = $rcblue;
		$data['focus'] = array_intersect($d1, $d2);
		return $data;
	}
}

$a = get_rcblue('15');
print_r($a);die;