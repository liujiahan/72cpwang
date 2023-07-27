<?php

function RongChi500W($cp_dayid, $winReds, $blue=8){
	global $dosql;

	$reds = combination($winReds, 6);

	$total=0;
	$sid = 1;

	$dosql->ExecNoneQuery("UPDATE `#@__caipiao_weermy` SET cp_dayid='$cp_dayid' WHERE id='$sid'");
	$dosql->ExecNoneQuery("DELETE FROM `#@__caipiao_weermy_cpdata`  WHERE sid='$sid'");

	foreach ($reds as $red) {
		$red = implode('.', $red);

		$ssq = $red . '+' . $blue;
		$sql = "INSERT INTO `#@__caipiao_weermy_cpdata` (sid, ssq, winlevel) VALUES ('".$sid."', '".$ssq."', '0')";
		$dosql->ExecNoneQuery($sql);
		$total++;
	}

	return $total;
}