<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__) . '/../wxpage/core/choosered.func.php';


$qunue = array(
    10, 20, 30, 40, 50, 60, 70, 80,
    1, 11, 21, 31, 41, 51, 61, 71,
    2, 12, 22, 32, 42, 52, 62, 72,
    3, 13, 23, 33, 43, 53, 63, 73,
    4, 14, 24, 34, 44, 54, 64, 74,
    5, 15, 25, 35, 45, 55, 65, 75,
    6, 16, 26, 36, 46, 56, 66, 76,
    7, 17, 27, 37, 47, 57, 67, 77,
    8, 18, 28, 38, 48, 58, 68, 78,
    9, 19, 29, 39, 49, 59, 69, 79,
);

$tails = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

$num = isset($num) ? $num : 30;
$redball = isset($redball) ? $redball : 'all';

if ($redball == 'all') {
} else {
    $tmp_qunue = array();
    foreach ($qunue as $val) {
        $tmptails = array($redball, ($redball + 1) % 10, ($redball + 2) % 10);
        if (!in_array($val % 10, $tmptails)) {
            continue;
        }
        $tmp_qunue[] = $val;
    }
    $qunue = $tmp_qunue;
}

$dosql->Execute("SELECT * FROM `#@__happy8_history` order by id DESC LIMIT {$num}");

$data = array();
while ($row = $dosql->GetArray()) {
    $ssq = array();
    // $ssq['no'] = substr($row['cp_dayid'], 0, 4) . '-' . substr($row['cp_dayid'], -3);
    $ssq['no'] = substr($row['cp_dayid'], -3);
    $ssq['opencode'] = str_replace(",", " ", $row['opencode']);

    $info = $dosql->GetOne("SELECT * FROM `#@__happy8_cool_hot` WHERE cp_dayid>" . $row['cp_dayid'] . " order by cp_dayid ASC");
    if (empty($info)) {
        $all_red_miss = happy8RedMissing();
    } else {
        $all_red_miss = unserialize($info['miss_content']);
    }
    new_sort($all_red_miss);
    $ssq['reds'] = $all_red_miss;

    $data[] = $ssq;
}
$data = array_reverse($data);

function new_sort(&$all_red_miss)
{
    global $qunue;
    $data = array();
    foreach ($qunue as $red) {
        $red < 10 && $red = '0' . $red;
        $data[$red] = $all_red_miss[$red];
    }
    $all_red_miss = $data;
}

foreach ($qunue as &$red) {
    $red < 10 && $red = '0' . $red;
}

$fenge = 13;


?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="author" content="看彩72变(72cp.wang)">
    <meta name="copyright" content="Copyright @72cp.wang 版权所有">
    <meta name="Keywords" content="双色球走势图">
    <meta name="Description" content="双色球走势图">
    <title>五行逆向走势图</title>
    <link rel="icon" type="image/vnd.microsoft.icon" href="https://72cp.wang/favicon.ico">
    <link rel="stylesheet" href="./cp360/all.css">
    <link rel="stylesheet" href="./cp360/ssq_new.css">
    <style>
        body {
            zoom: 2;
        }
    </style>
</head>

<body class="tzhfb">
    <div class="wrap">
        <div class="chart-sc">
            <ul style="float: none;">
                <li>
                    <strong>查询：</strong>
                </li>
                <li>
                    <a href="?redball=all&num=<?php echo $num ?>" class="btn-sc <?php echo $redball == 'all' ? 'btn-sc-cur' : ''; ?>">全部</a>
                </li>
                <?php foreach ($tails as $tail) { ?>
                    <li>
                        <a href="?redball=<?php echo $tail ?>&num=<?php echo $num ?>" class="btn-sc <?php echo $redball === $tail ? 'btn-sc-cur' : ''; ?>"><?php echo $tail ?>尾</a>
                    </li>
                <?php } ?>
                <li>
                    <a href="?num=30" class="btn-sc btn-sc-cur">近30期</a>
                </li>
                <li>
                    <a href="?num=50" class="btn-sc">近50期</a>
                </li>
                <li>
                    <a href="?num=100" class="btn-sc">近100期</a>
                </li>
            </ul>
        </div>
        <div class="chart-tab" id="chart-tab">
            <table class="chart-table">
                <thead class="zhfb">
                    <tr>
                        <th rowspan="2" class="w94">
                            期号&nbsp; <a href="#" class="tharr tharr-up"></a>
                        </th>
                        <td rowspan="2" class="tdbdr"></td>
                        <th rowspan="2" class="thide">
                            奖号
                        </th>
                        <td rowspan="2" class="tdbdr thide"></td>
                        <th colspan="81" class="noth">
                            红球号码分布
                        </th>
                        <td class="tdbdr"></td>
                    </tr>
                    <tr>
                        <?php foreach ($qunue as $tmpred) { ?>
                            <td class=" w1_9">
                                <?php echo $tmpred ?>
                            </td>
                            <?php if ($tmpred == $fenge) { ?>
                                <td class="tdbdr tdbdr_nav"></td>
                            <?php } ?>
                        <?php } ?>
                        <td class="tdbdr"></td>
                    </tr>
                </thead>
                <tbody id="data-tab" class="zzhfb">
                    <?php foreach ($data as $k => $row) { ?>
                        <tr>
                            <td class="tdbg_1">
                                <?php echo $row['no'] ?>
                            </td>
                            <td class="tdbdr"></td>
                            <td class="tdbg_1 thide">
                                <strong class="rednum"><?php echo $row['red_num'] ?></strong>+<strong class="bluenum"><?php echo $row['blue_num'] ?></strong>
                            </td>
                            <td class="tdbdr thide"></td>
                            <?php foreach ($row['reds'] as $tmpred => $redmiss) { ?>

                                <?php if ($redmiss == 0) { ?>
                                    <td class="tdbg_5" hit="">
                                        <span class="ball_s3"><?php echo $tmpred ?></span>
                                    </td>
                                    <?php if ($tmpred == $fenge) { ?>
                                        <td class="tdbdr tdbdr_nav"></td>
                                    <?php } ?>
                                <?php } else { ?>
                                    <td class="tdbg_5">
                                        <?php echo $redmiss ?>
                                    </td>
                                    <?php if ($tmpred == $fenge) { ?>
                                        <td class="tdbdr tdbdr_nav"></td>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <!-- 红球结束 -->
                            <td class="tdbdr"></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot id="addedrow">
                    <tr class="addCodeNum">
                        <td class="tdbg_1">
                            <span class="btn-prese"><a href="#" class="add"></a> <a href="#" class="cut"></a></span> 预选1
                        </td>
                        <td class="tdbdr"></td>
                        <?php foreach ($qunue as $tmpred) { ?>
                            <td class="">
                                <span class="ball_x1"><?php echo $tmpred ?></span>
                            </td>
                            <?php if ($tmpred == $fenge) { ?>
                                <td class="tdbdr_nav"></td>
                            <?php } ?>
                        <?php } ?>
                        <td class="tdbdr"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div style="margin-bottom:100px;"></div>
    <!-- <script src="./cp360/libs.js"></script> -->
    <script src="./cp360/jquery-1.8.3.min.js"></script>
    <!-- <script src="./cp360/ssq.js"></script> -->
    <script>
        (function(e) {
            $(".ball_x1").click(function() {
                var a = $(this).html();
                if ($(this).hasClass('ball_x2')) {
                    $(this).removeClass('ball_x2')
                } else {
                    $(this).addClass('ball_x2')
                }
            })
            $(".ball_x4").click(function() {
                var a = $(this).html();
                if ($(this).hasClass('ball_x4')) {
                    $(this).removeClass('ball_x4')
                    $(this).addClass('ball_s10')
                } else {
                    $(this).removeClass('ball_s10')
                    $(this).addClass('ball_x4')
                }
            })
        })(window);
    </script>
</body>

</html>