<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__) . '/../wxpage/core/choosered.func.php';

for ($i = 1; $i <= 80; $i++) {
    $red1 = $i;
    if ($red1 < 10) {
        $red1 = $red1;
    }
    $qunue[] = $red1;
}

$num = isset($num) ? $num : 30;
$redball = isset($redball) ? $redball : 'all';

$start = 1;
$end = $redball;
if ($redball == '20') {
    $start = $redball - 20 + 1;
} else if ($redball == '40') {
    $start = $redball - 20 + 1;
} else if ($redball == '60') {
    $start = $redball - 20 + 1;
} else if ($redball == '80') {
    $start = $redball - 20 + 1;
} else {
    $end = 80;
}
$tmp_qunue = array();
foreach ($qunue as $val) {
    if ($val < $start || $val > $end) {
        continue;
    }
    $tmp_qunue[] = $val;
}
$qunue = $tmp_qunue;
$where = "";
if (!empty($id)) {
    $where = " WHERE cp_dayid<=" . $id;
}
$dosql->Execute("SELECT * FROM `#@__happy8_history` {$where} order by id DESC LIMIT {$num}");

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

$fenge = 16;
$fenge_blue = 8;


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
    <title>常规走势图</title>
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
                <li>
                    <a href="?redball=20&num=<?php echo $num ?>" class="btn-sc <?php echo $redball == '20' ? 'btn-sc-cur' : ''; ?>">20</a>
                </li>
                <li>
                    <a href="?redball=40&num=<?php echo $num ?>" class="btn-sc <?php echo $redball == '40' ? 'btn-sc-cur' : ''; ?>">40</a>
                </li>
                <li>
                    <a href="?redball=60&num=<?php echo $num ?>" class="btn-sc <?php echo $redball == '60' ? 'btn-sc-cur' : ''; ?>">60</a>
                </li>
                <li>
                    <a href="?redball=80&num=<?php echo $num ?>" class="btn-sc <?php echo $redball == '80' ? 'btn-sc-cur' : ''; ?>">80</a>
                </li>
                <li>
                    <a href="?num=30&redball=<?php echo $redball ?>" class="btn-sc <?php echo $num == '30' ? 'btn-sc-cur' : ''; ?>">近30期</a>
                </li>
                <li>
                    <a href="?num=50&redball=<?php echo $redball ?>" class="btn-sc <?php echo $num == '50' ? 'btn-sc-cur' : ''; ?>">近50期</a>
                </li>
                <li>
                    <a href="?num=100&redball=<?php echo $redball ?>" class="btn-sc <?php echo $num == '100' ? 'btn-sc-cur' : ''; ?>">近100期</a>
                </li>
            </ul>
            <!-- <strong>标注：</strong>
            <label><input type="checkbox" name="options" val="sj">&nbsp;遗漏数据&nbsp;</label>
            <label><input type="checkbox" name="options" val="fc" checked="checked">&nbsp;遗漏分层&nbsp;</label>
            <label><input type="checkbox" name="options" val="zx" checked="checked">&nbsp;折线&nbsp;</label>
            <label><input type="checkbox" name="options" val="lh">&nbsp;邻号&nbsp;</label>
            <label><input type="checkbox" name="options" val="ch">&nbsp;重号&nbsp;</label>
            <label><input type="checkbox" name="options" val="lx">&nbsp;连号&nbsp;</label> -->
        </div>
        <div class="chart-tab" id="chart-tab">
            <table class="chart-table">
                <thead class="zhfb">
                    <tr>
                        <th rowspan="2">
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
                            <!-- 蓝球结束 -->
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
        })(window);

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
    </script>
</body>

</html>