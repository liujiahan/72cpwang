<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

$primes = array(1,2,3,5,7,11,13);

$dosql->Execute("SELECT * FROM `#@__caipiao_blue_fx` ORDER BY cp_dayid ASC","blue_fx");
while($row = $dosql->GetArray('blue_fx')){
	$id       = $row['id'];
	$cp_dayid = $row['cp_dayid'];

	$prime_num   = in_array($row['blue_num'], $primes) ? 1 : 0;

	$sql = "UPDATE `#@__caipiao_blue_fx` SET prime_num='$prime_num' WHERE id='$id'";

	$dosql->ExecNoneQuery($sql);
	echo $cp_dayid . PHP_EOL;
}