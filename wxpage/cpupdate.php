<?php


die('123');
error_reporting(E_ALL);
ini_set('display_errors','On');

require_once dirname(__FILE__).'/../include/config.inc.php';

$row = '2019094 2019-08-13 05 10 12 18 19 27 06 10 27 12 05 19 18 318739184 870773310 8 6822140 301 60536 1930 3000 90607 200 1519430 10 8838174 5';
$row = explode(' ', $row);

$cp_dayid  = $row[0];
$cp_day    = $row[1];
$opencode  = $row[2] . ',' . $row[3] . ','. $row[4] . ','. $row[5] . ','. $row[6] . ','. $row[7] . '+'. $row[8];
$red_num   = $row[2] . ',' . $row[3] . ','. $row[4] . ','. $row[5] . ','. $row[6] . ','. $row[7];
$blue_num  = $row[8];
$red_order = $row[9] . ',' . $row[10] . ','. $row[11] . ','. $row[12] . ','. $row[13] . ','. $row[14];

$exist = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
if(isset($exist['id'])){
	continue;
}

$sql = "INSERT INTO `#@__caipiao_history` (cp_dayid, cp_day, opencode, red_num, blue_num, red_order) 
VALUES 
('".$cp_dayid."', '".$cp_day."', '".$opencode."', '".$red_num."', '".$blue_num."', '".$red_order."')";

$dosql->ExecNoneQuery($sql);