<?php

header("Access-Control-Allow-Origin: *");

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__) . '/../wxpage/core/choosered.func.php';

$action = isset($action) ? $action : '';
$num = isset($num) ? $num : 30;
echo call_user_func($action);

/**
 * 三区趋势图
 */
function three_zone_trend()
{
    global $dosql, $num;

    set_time_limit(0);
    ini_set('momery_limit', '800M');
    $legend = array(
        '一区命中',
        '二区命中',
        '三区命中',
        '一区5期命中',
        '二区5期命中',
        '三区5期命中',
        '一区遗漏和',
        '二区遗漏和',
        '三区遗漏和',
    );

    $num = isset($num) ? $num : 30;
    $dosql->Execute("SELECT * FROM `#@__caipiao_history` order by id DESC LIMIT {$num}");
    $rows = array();
    // 期数
    $lottory_no = array();
    // 三区数量
    $area1 = array();
    $area2 = array();
    $area3 = array();
    $area1_5num = array();
    $area2_5num = array();
    $area3_5num = array();
    $area1_sum = array();
    $area2_sum = array();
    $area3_sum = array();

    while ($row = $dosql->GetArray()) {
        $lottory_no[] = intval(substr($row['cp_dayid'], -3)) . '期';
        $reds = explode(",", $row['red_num']);

        $info = $dosql->GetOne("SELECT * FROM `#@__caipiao_red_location_cross` WHERE cp_dayid=" . $row['cp_dayid']);
        $fenqu3 = array_values(unserialize($info['fenqu3']));
        $fenqu3_5num = array_values(unserialize($info['fenqu3_5num']));

        foreach ($fenqu3 as $key => $num) {
            $area = 'area' . ($key + 1);
            $$area[] = $num;
        }
        foreach ($fenqu3_5num as $key => $num) {
            $area = 'area' . ($key + 1) . '_5num';
            $$area[] = $num;
        }

        $info = $dosql->GetOne("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid=" . $row['cp_dayid']);
        $all_red_miss = unserialize($info['miss_content']);

        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        foreach ($all_red_miss as $tmpred => $miss_num) {
            if (in_array($tmpred, $reds)) {
                if ($tmpred >= 1 and $tmpred <= 11) {
                    $sum1 += $miss_num;
                } else if ($tmpred >= 12 and $tmpred <= 22) {
                    $sum2 += $miss_num;
                } else if ($tmpred >= 23 and $tmpred <= 33) {
                    $sum3 += $miss_num;
                }
            }
        }

        $area1_sum[] = $sum1;
        $area2_sum[] = $sum2;
        $area3_sum[] = $sum3;
    }
    $lottory_no = array_reverse($lottory_no);
    $area1 = array_reverse($area1);
    $area2 = array_reverse($area2);
    $area3 = array_reverse($area3);
    $area1_5num = array_reverse($area1_5num);
    $area2_5num = array_reverse($area2_5num);
    $area3_5num = array_reverse($area3_5num);
    $area1_sum = array_reverse($area1_sum);
    $area2_sum = array_reverse($area2_sum);
    $area3_sum = array_reverse($area3_sum);

    $data = array(
        'legend' => $legend,
        'lottory_no' => $lottory_no,
        'area1' => $area1,
        'area2' => $area2,
        'area3' => $area3,
        'area1_5num' => $area1_5num,
        'area2_5num' => $area2_5num,
        'area3_5num' => $area3_5num,
        'area1_sum' => $area1_sum,
        'area2_sum' => $area2_sum,
        'area3_sum' => $area3_sum,
    );
    $ret = array('code' => 200, 'data' => $data);
    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

/**
 * 冷号“必出趋势图”
 */
function hot_warm_cool()
{
    global $dosql, $num;

    set_time_limit(0);
    ini_set('momery_limit', '800M');
    $legend = array(
        '冷号命中数',
        '待转冷数',
        '冷号数量',
        '遗漏层数',
    );

    $num = isset($num) ? $num : 30;
    $dosql->Execute("SELECT * FROM `#@__caipiao_history` order by id DESC LIMIT {$num}");
    $rows = array();
    // 期数
    $lottory_no = array();
    // 待转冷数量
    $warm9 = array();
    // 遗漏层数
    $miss_layer = array();
    // 冷号数量
    $cool_reds = array();
    // 冷号开出数量
    $cool_wins = array();


    $max = $dosql->GetOne("SELECT max(cp_dayid) as cp_dayid FROM `#@__caipiao_history`");
    $lottory_no[] = intval(substr($max['cp_dayid'] + 1, -3)) . '期';
    $reds = array();
    $all_red_miss = redMissing();

    $miss_arr = array();
    $cool_rednum = 0;
    $cool_winnum = 0;
    $warm9_num = 0;
    foreach ($all_red_miss as $tmpred => $miss_num) {
        if (!isset($miss_arr[$miss_num])) {
            $miss_arr[$miss_num] = 1;
        }
        if ($miss_num > 9) {
            $cool_rednum++;
            if (in_array($tmpred, $reds)) {
                $cool_winnum++;
            }
        }
        if ($miss_num == 9) {
            $warm9_num++;
        }
    }

    $warm9[] = $warm9_num;
    $miss_layer[] = count($miss_arr);
    $cool_reds[] = $cool_rednum;
    $cool_wins[] = $cool_winnum;

    while ($row = $dosql->GetArray()) {
        $lottory_no[] = intval(substr($row['cp_dayid'], -3)) . '期';
        $reds = explode(",", $row['red_num']);

        $info = $dosql->GetOne("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid=" . $row['cp_dayid']);
        $all_red_miss = unserialize($info['miss_content']);

        $miss_arr = array();
        $cool_rednum = 0;
        $cool_winnum = 0;
        $warm9_num = 0;
        foreach ($all_red_miss as $tmpred => $miss_num) {
            if (!isset($miss_arr[$miss_num])) {
                $miss_arr[$miss_num] = 1;
            }
            if ($miss_num > 9) {
                $cool_rednum++;
                if (in_array($tmpred, $reds)) {
                    $cool_winnum++;
                }
            }
            if ($miss_num == 9) {
                $warm9_num++;
            }
        }

        $warm9[] = $warm9_num;
        $miss_layer[] = count($miss_arr);
        $cool_reds[] = $cool_rednum;
        $cool_wins[] = $cool_winnum;
    }
    $lottory_no = array_reverse($lottory_no);
    $warm9 = array_reverse($warm9);
    $miss_layer = array_reverse($miss_layer);
    $cool_reds = array_reverse($cool_reds);
    $cool_wins = array_reverse($cool_wins);

    $data = array(
        'legend' => $legend,
        'lottory_no' => $lottory_no,
        'warm9' => $warm9,
        'miss_layer' => $miss_layer,
        'cool_reds' => $cool_reds,
        'cool_wins' => $cool_wins,
    );
    $ret = array('code' => 200, 'data' => $data);
    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

/**
 * 三码趋势图
 */
function code3_trend()
{
    global $dosql, $num;

    set_time_limit(0);
    ini_set('momery_limit', '800M');
    $legend = array(
        '遗漏期数',
        '该遗漏组数',
        '命中',
        '总命中',
    );

    $limit_num = isset($num) ? $num : 30;
    $cp_dayid = isset($cp_dayid) ? $cp_dayid : '';

    $coolHot = array();
    $coolHot['date']     = array();
    $coolHot['missnum']  = array();
    $coolHot['freq'] = array();
    $coolHot['win'] = array();
    $coolHot['all_win'] = array();

    if (!empty($cp_dayid)) {
        $dosql->Execute("SELECT * FROM `#@__caipiao_3code_chart` WHERE cp_dayid<=$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit_num");
    } else {
        $dosql->Execute("SELECT * FROM `#@__caipiao_3code_chart` ORDER BY cp_dayid DESC LIMIT $limit_num");
    }

    $max_missnum = 0;
    while ($row = $dosql->GetArray()) {
        $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4) . '000');
        if ($xdate == 1) {
            $xdate = substr($row['cp_dayid'], 0, 4);
        }
        $coolHot['date'][]     = $xdate;
        $coolHot['missnum'][]  = $row['missnum'];
        $coolHot['freq'][] = $row['freq'];
        $coolHot['win'][] = $row['win'];
        $coolHot['all_win'][] = $row['all_win'];
    }

    $coolHot['date']     = array_reverse($coolHot['date']);
    $coolHot['missnum']  = array_reverse($coolHot['missnum']);
    $coolHot['freq'] = array_reverse($coolHot['freq']);
    $coolHot['win'] = array_reverse($coolHot['win']);
    $coolHot['all_win'] = array_reverse($coolHot['all_win']);

    $data = array(
        'legend' => $legend,
        'lottory_no' => $coolHot['date'],
        'missnum' => $coolHot['missnum'],
        'freq' => $coolHot['freq'],
        'win' => $coolHot['win'],
        'all_win' => $coolHot['all_win'],
    );
    $ret = array('code' => 200, 'data' => $data);
    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}
