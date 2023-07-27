<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
LoginCheck();
$code = isset($code) && !empty($code) ? $code : '';
if (empty($code)) {
    exit;
}

$dosql->Execute("SELECT * FROM `#@__caipiao_3code_missing` WHERE code='{$code}' ORDER BY cp_dayid ASC", 'aaa');
$chart = array();
$chart['lenged'] = array();
$chart['data'] = array();

$missCount = array();
$codeHistory = array();
while ($row = $dosql->GetArray('aaa')) {
    $chart['lenged'][] = $row['cp_dayid'];
    $chart['data'][] = $row['missnum'];
    if (!isset($missCount[$row['missnum']])) {
        $missCount[$row['missnum']] = 0;
    }
    $missCount[$row['missnum']]++;
    $codeHistory[$row['cp_dayid']] = array('cp_dayid' => $row['cp_dayid'], 'missnum' => $row['missnum']);
}
ksort($missCount);

$max = $dosql->GetOne("SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_3code`");
$codemiss = threeCodeMissingOnlyOne($code);
if (isset($codemiss[$code])) {
    $chart['lenged'][] = $max['cp_dayid'] + 1;
    $chart['data'][] = $codemiss[$code];
}

$dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid in (" . implode(',', $chart['lenged']) . ") ORDER BY cp_dayid DESC", 'aaa');

while ($row = $dosql->GetArray('aaa')) {
    $codeHistory[$row['cp_dayid']]['opencode'] = $row['opencode'];
}
$codeHistory = array_reverse($codeHistory);


// print_r($missWinCount);
// die;
// select * from (SELECT missnum,count(missnum) as total FROM `lz_caipiao_3code_missing` GROUP BY missnum) t order by total desc;

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title><?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">



    <link rel="stylesheet" type="text/css" media="all" href="./ssq17500/reset.css">
    <link rel="stylesheet" type="text/css" media="all" href="./ssq17500/common.css">
    <link rel="stylesheet" type="text/css" media="all" href="./ssq17500/style.css">

    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>


    <script src="./ssq17500/sorttable.js" type="text/javascript"></script>
    <script src="./ssq17500/echarts.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        function gourl() {
            window.location.href = "?missnum=" + $("#missnum").val();
        }
    </script>
    <script type="text/javascript" src="/static/js/baidutj.js"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>
            <div class="row">
                <div class="wid fixed">
                    <!-- <div class="tlt fixed">
                        <h2>
                            双色球两码组合查询09,15次数变化表
                        </h2>
                    </div> -->
                    <p class="gjjt">
                        双色球三码遗漏走势图。
                    </p>
                    <!-- <div class="gjhm">
                        <div class="typeyl">
                            <a class="" href="https://www.17500.cn/widget/ssq/lmyl/dtype/redyl.html" one-link-mark="yes">红球</a>
                            <a class="" href="https://www.17500.cn/widget/ssq/lmyl/dtype/blueyl.html" one-link-mark="yes">蓝球</a>
                            <a class="" href="https://www.17500.cn/widget/ssq/lmyl/dtype/2lian.html" one-link-mark="yes">红两连</a>
                            <a class="" href="https://www.17500.cn/widget/ssq/lmyl/dtype/jilian.html" one-link-mark="yes">红奇连</a>
                        </div>
                    </div> -->
                    <div class="fixed">
                        <div class="jgryl">
                            <div id="main" style="width: 740px; height: 400px; -webkit-tap-highlight-color: transparent; user-select: none; position: relative;">
                                <div style="position: relative; width: 740px; height: 400px; padding: 0px; margin: 0px; border-width: 0px; cursor: default;"></div>
                                <div style="position: absolute; display: none; border-style: solid; white-space: nowrap; z-index: 9999999; transition: left 0.4s cubic-bezier(0.23, 1, 0.32, 1) 0s, top 0.4s cubic-bezier(0.23, 1, 0.32, 1) 0s; background-color: rgba(50, 50, 50, 0.7); border-width: 0px; border-color: rgb(51, 51, 51); border-radius: 4px; color: rgb(255, 255, 255); font: 14px / 21px &quot;Microsoft YaHei&quot;; padding: 5px; left: 496px; top: 95px; pointer-events: none;">
                                    2020020<br>
                                    17
                                </div>
                            </div>
                            <div class="ylbtxt">
                                <table class="jstab fontssmall zsta3" style="width:100%;">
                                    <tbody>
                                        <tr>
                                            <td>遗漏值</td>
                                            <?php foreach ($missCount as $miss => $num) { ?>
                                                <td><?php echo $miss ?></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td>出现次数</td>
                                            <?php foreach ($missCount as $miss => $num) { ?>
                                                <td><?php echo $num ?></td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="jglyl">
                            <table class="sortable jstab fontssmall zsta3" style="width:100%;">
                                <thead>
                                    <tr>
                                        <td>
                                            期数 <span id="sorttable_sortfwdind">▾</span>
                                        </td>
                                        <td>
                                            号码
                                        </td>
                                        <td>
                                            遗漏值
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($codeHistory as $key => $row) { ?>
                                        <tr>
                                            <td><?php echo $row['cp_dayid'] ?></td>
                                            <td><?php echo $row['opencode'] ?></td>
                                            <td><?php echo $row['missnum'] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var myChart = echarts.init(document.getElementById('main'));

                    // 指定图表的配置项和数据
                    var option = {
                        title: {
                            text: '<?php echo $code ?>三码遗漏(次数变化表)'
                        },
                        tooltip: {},
                        xAxis: {
                            name: '期号',
                            nameLocation: 'start',
                            nameGap: 25,
                            type: 'category',
                            boundaryGap: false,
                            splitNumber: 5,
                            data: <?php echo json_encode($chart['lenged']) ?>,
                        },
                        yAxis: {
                            type: 'value'
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        dataZoom: [{ // 这个dataZoom组件，默认控制x轴。
                            type: 'slider', // 这个 dataZoom 组件是 slider 型 dataZoom 组件
                            startValue: 1000 - 30, // 左边在 10% 的位置。
                            endValue: 1000 // 右边在 60% 的位置。
                        }],
                        series: [{
                            type: 'line',
                            markLine: {
                                symbol: ['none', 'none'],
                                label: {
                                    show: false
                                },
                                data: [{
                                    xAxis: 1
                                }, {
                                    xAxis: 3
                                }, {
                                    xAxis: 5
                                }, {
                                    xAxis: 7
                                }]
                            },
                            data: <?php echo json_encode($chart['data']) ?>,
                            label: {
                                show: true,
                                position: 'top',
                                distance: 3,
                            },
                            lineStyle: {
                                color: "#efa2a2",
                            }
                        }]
                    };

                    // 使用刚指定的配置项和数据显示图表。
                    myChart.setOption(option);
                </script>
            </div>
        </div>
    </nav>
</body>

</html>