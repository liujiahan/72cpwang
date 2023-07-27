<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/core.func.php';
require_once dirname(__FILE__) . '/core/tail.func.php';

$bigarr = array(
    array(2, 5, 8), array(1, 4, 7), array(3, 6, 9)
);

foreach ($bigarr as $code) {
    echo "尾码: " . implode(".", $code);
    echo "<br/>";
    $curmiss = 0;
    $data = test($code, 300, $curmiss);
    echo "<p style='word-wrap:break-word;'>间隔期数: " . implode("=>", $data) . "</p>";
    echo "<br/>";
    echo "平均出号间隔期数: " . round(array_sum($data) / count($data));
    echo "<br/>";
    echo "当前间隔期数：" . $curmiss;
    echo "<br/>";
    echo "<br/>";
}

function test($codeArr, $num = 100, &$curmiss)
{
    global $dosql;

    $result = array();
    $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT {$num}", "aaa");
    $data = array();
    while ($row = $dosql->GetArray('aaa')) {
        $reds = explode(',', $row['red_num']);
        $tmpCode = array();
        foreach ($reds as $key => $red) {
            $code = $red % 9 == 0 ? 9 : $red % 9;
            if (!isset($tmpCode[$code])) {
                $tmpCode[$code] = 1;
            }
            $tmpCode[$code]++;
        }
        $ok = 1;
        foreach ($codeArr as $val) {
            if (!isset($tmpCode[$val])) {
                $ok = 0;
                break;
            }
        }
        if ($ok == 0 && !isset($end)) {
            $curmiss++;
        }
        if ($ok == 1) $end = 1;

        $data[$row['cp_dayid']] = array();
        $data[$row['cp_dayid']]['cp_dayid'] = $row['cp_dayid'];
        $data[$row['cp_dayid']]['ok'] = $ok;
        $data[$row['cp_dayid']]['code'] = $tmpCode;
    }
    $data = array_reverse($data);
    $data = array_column($data, 'ok');

    // print_r($data);

    $result = array();
    for ($i = 0; $i <= count($data) - 1; $i++) {
        if ($data[$i] == 0) continue;
        if (!isset($index)) {
            $index = 0;
        }
        $result[$index] = 0;
        for ($j = $i + 1; $j <= count($data) - 1; $j++) {
            if ($data[$j] == 0) {
                $result[$index]++;
            } else {
                $index++;
                // $i = $j;
                break;
            }
        }
    }
    // 11
    return $result;
}
