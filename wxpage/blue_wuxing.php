<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
require_once dirname(__FILE__) . '/core/choosered.func.php';
require_once dirname(__FILE__) . '/core/wuxing.func.php';

$nums = isset($limit_num) ? $limit_num : 300;
$wuxingtj = wuxingtj($nums);

$data = blueWuXing(8);

LoginCheck();

$allBlue = array();
for ($i = 1; $i < 17; $i++) {
    // $i<10 && $i = '0' . $i;
    $allBlue[] = $i;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>蓝球五行关系走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function godayurl() {
            var limit_num = $("#limit_num").val();
            if (limit_num == 300) {
                window.location.href = "?limit_num=" + limit_num + "&width=10000";
            } else if (limit_num == 500) {
                window.location.href = "?limit_num=" + limit_num + "&width=16500";
            } else {
                window.location.href = "?limit_num=" + limit_num;
            }
        }

        $(document).ready(function() {
            // $('#limit_num').change(function(){
            //     window.location.href = "?limit_num="+$("#limit_num").val();
            // })
            $("#dataCalcBtn").click(function() {
                $(this).html('计算中...');
                $.ajax({
                    url: 'ajax/blue_suanfa_do.php',
                    dataType: 'html',
                    type: 'post',
                    data: {
                        action: 'blue_wuxing'
                    },
                    success: function(data) {
                        // alert("成功计算"+data+"期");
                        window.location.reload();
                    }
                })
            })
        })
    </script>
    <style type="text/css">
        .red_ball {
            width: 25px;
            height: 25px;
            line-height: 25px;
        }

        .blue_ball {
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">选择期数</label>
                    <select class="form-control" name="limit_num" id="limit_num" onchange="godayurl()">
                        <option value="">--请选择--</option>
                        <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
                            <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
                        <?php } ?>
                    </select>
                </div>
                <!-- <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a> -->
            </form>
            <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">蓝球五行关系</a>
            <?php } ?>

            <?php
            $preData = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
            $pre_red = explode(',', $preData['red_num']);
            $pre_tail = array();
            foreach ($pre_red as $red) {
                $pre_tail[] = $red % 10;
            }
            $pre_tail = array_unique($pre_tail);
            sort($pre_tail);
            ?>

            <div class="clearfix"></div>


            <div class="bs-example" data-example-id="contextual-table">
                <?php

                $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");

                $blueWuXing = blueWuXing($row['blue_num']);

                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>下期期数</th>
                            <th>上期蓝球</th>
                            <th>相生蓝球【y=1】</th>
                            <th>被生蓝球【y=2】</th>
                            <th>相克蓝球【y=3】</th>
                            <th>被克蓝球【y=4】</th>
                            <th>相同蓝球【y=5】</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo nextCpDayId($row['cp_dayid']) ?></td>
                            <td><?php echo $row['blue_num'] . '【' . DigitFiveElements($row['blue_num'] % 10) . '】' ?></td>
                            <td>
                                <?php
                                echo implode(" ", $blueWuXing['living']);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo implode(" ", $blueWuXing['be_living']);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo implode(" ", $blueWuXing['restrain']);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo implode(" ", $blueWuXing['be_restrain']);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo implode(" ", $blueWuXing['same']);
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="center-block" id="chart1" style="height: 360px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>
                <?php foreach ($wuxingtj as $index => $vals) { ?>
                    <p class="lead"><?php echo $vals ?></p>
                <?php } ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

<?php

$suanfa = array();
$suanfa['date'] = array();
$suanfa['wuxing'] = array();

$limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_blue_wuxing` ORDER BY cp_dayid DESC LIMIT $limit_num");
while ($row = $dosql->GetArray()) {
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4) . '000');
    if ($xdate == 1) {
        $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa['date'][] = $xdate;
    $suanfa['wuxing'][] = $row['wuxing_key'];
}

$suanfa['date']   = array_reverse($suanfa['date']);
$suanfa['wuxing'] = array_reverse($suanfa['wuxing']);

?>

</html>
<script type="text/javascript" src="./js/echarts.js"></script>

<script type="text/javascript">
    require.config({
        paths: {
            echarts: './js'
        }
    });
    require(
        [
            'echarts',
            'echarts/chart/line', // 按需加载所需图表，如需动态类型切换功能，别忘了同时加载相应图表
            'echarts/chart/radar', // 按需加载所需图表，如需动态类型切换功能，别忘了同时加载相应图表
            'echarts/chart/bar'
        ],
        function(ec) {
            var title = '蓝球五行关系走势图';
            var xData = <?php echo json_encode($suanfa['date']) ?>;

            var yData = <?php echo json_encode($suanfa['wuxing']) ?>;
            var myChart = ec.init(document.getElementById('chart1'));

            var option = {
                title: {
                    text: title,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c}'
                },
                legend: {
                    left: 'left',
                    data: [title]
                },
                xAxis: {
                    type: 'category',
                    name: 'x',
                    splitLine: {
                        show: false
                    },
                    data: xData
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    show: false,
                    containLabel: true
                },
                yAxis: {
                    type: 'value',
                    min: 1,
                    max: 5,
                    name: 'y',
                    splitNumber: 4
                },
                series: [{
                    name: title,
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            color: 'red',
                            lineStyle: {
                                color: '#f36'
                            }
                        }
                    },
                    data: yData
                }]
            };
            myChart.setOption(option);
        }
    );
</script>