<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot`");
$result = array();
$total = 0;

$result2 = array();
$result3 = array();
while($row = $dosql->GetArray()){
	if(!isset($result[$row['hot_num']])){
		$result[$row['hot_num']] = 0;
	}
	$result[$row['hot_num']]++;
	$total++;

	if(!isset($result2[$row['warm_num']])){
		$result2[$row['warm_num']] = 0;
	}
	$result2[$row['warm_num']]++;

	if(!isset($result3[$row['cool_num']])){
		$result3[$row['cool_num']] = 0;
	}
	$result3[$row['cool_num']]++;
}
arsort($result);
echo $total;
echo "<br/>";
// print_r($result);die;

foreach ($result as $hot_num => $num) {
    echo '出'.$hot_num.'个热球 '.$num.'次 占比：' . round($num / $total, 4) * 100 . '%';
    echo "<br/>";
}

arsort($result2);
echo "<br/>";

foreach ($result2 as $warm_num => $num) {
    echo '出'.$warm_num.'个温球 '.$num.'次 占比：' . round($num / $total, 4) * 100 . '%';
    echo "<br/>";
}

arsort($result3);
echo "<br/>";

foreach ($result3 as $cool_num => $num) {
    echo '出'.$cool_num.'个冷球 '.$num.'次 占比：' . round($num / $total, 4) * 100 . '%';
    echo "<br/>";
}

