<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';

$nums = array();
$dosql->Execute("SELECT * FROM `#@__memory_training`");
while ($row = $dosql->GetArray()) {
    $nums[$row['digit']] = $row['picture_code'];
}

$count = count($nums);

$indexs = array_keys($nums);
$key = rand(0, $count - 1);
$digit = $indexs[$key];

echo '<h1>' . $digit . '</h1>';
echo "<br/>";
echo "<br/>";
echo "<br/>";
$pic = 'jiyili/' . $digit . '.jpg';
echo '<img src="' . $pic . '" style="width:200px;height:auto;" />';
