<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/../wxpage/core/suanfa.func.php';
require_once dirname(__FILE__) . '/../wxpage/core/ssq.config.php';

include_once 'RnPdo.php';

$db_config = array(
    'db_type' => "mysql", //数据库链接类型
    'db_host' => "localhost", //数据库地址
    'db_name' => "win500w_db", //数据库名
    'db_user' => "root", //用户名
    'db_pass' => "lsls0828", //密码
    'prefix' => "win_" //默认表前缀
);
//实例化
$db = new Mdb($db_config);

$limit_num = isset($limit_num) ? $limit_num : 30;
$data = $db->name('daletou')->order('lottery_no desc')->limit($limit_num)->select();

$bData = array();
$bData['date']     = array();
$bData['blue1']  = array();
$bData['blue2'] = array();
$bData['blue_avg'] = array();
$bData['bluediff'] = array();
$bData['bluediff5'] = array();
foreach ($data as $row) {
    $xdate = $row['lottery_no'] - intval(substr($row['lottery_no'], 0, 4) . '000');
    if ($xdate == 1) {
        $xdate = substr($row['lottery_no'], 0, 4);
    }
    $row5 = $db->name('daletou')->where('lottery_no <= ' . $row['lottery_no'])->order('lottery_no desc')->limit(5)->select();
    $blue_diff = 0;
    foreach ($row5 as $v) {
        $blue_diff += $v['another_blueball'] - $v['blueball'];
    }
    $blue_diff /= 5;
    $bData['date'][]     = $xdate;
    $bData['blue1'][]  = $row['blueball'];
    $bData['blue2'][] = $row['another_blueball'];
    $bData['blue_avg'][] = ($row['blueball'] + $row['another_blueball']) / 2;
    $bData['bluediff'][] = $row['another_blueball'] - $row['blueball'];
    $bData['bluediff5'][] = $blue_diff;
}

$bData['date']     = array_reverse($bData['date']);
$bData['blue1']  = array_reverse($bData['blue1']);
$bData['blue2'] = array_reverse($bData['blue2']);
$bData['blue_avg'] = array_reverse($bData['blue_avg']);
$bData['bluediff'] = array_reverse($bData['bluediff']);
$bData['bluediff5'] = array_reverse($bData['bluediff5']);
// print_r($bData);

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>大乐透蓝号</title>
    <link rel="stylesheet" type="text/css" href="../wxpage/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="../wxpage/bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="../wxpage/bootstrap/js/bootstrap.min.js"></script>
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
    </script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('../wxpage/navbar.php')
            ?>
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
            </form>
            <div class="clearfix"></div>
            <div class="center-block" id="chart" style="height: 360px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>
        </div>
    </nav>
</body>

</html>
<script type="text/javascript" src="../wxpage/js/echarts.js"></script>
<!-- <script type="text/javascript" src="./js/echarts.min.js"></script> -->
<!-- <script type="text/javascript" src="./js/echarts.simple.min.js"></script> -->
<!-- <script type="text/javascript" src="js/chart/radar.js"></script> -->
<script type="text/javascript">
    require.config({
        paths: {
            echarts: '../wxpage/js'
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
            var title = '大乐透蓝号趋势';
            var xData = <?php echo json_encode($bData['date']) ?>;

            var yb1 = <?php echo json_encode($bData['blue1']) ?>;
            var yb2 = <?php echo json_encode($bData['blue2']) ?>;
            var yb3 = <?php echo json_encode($bData['blue_avg']) ?>;
            var ybdiff = <?php echo json_encode($bData['bluediff']) ?>;
            var ybdiff5 = <?php echo json_encode($bData['bluediff5']) ?>;

            var myChart = ec.init(document.getElementById('chart'));

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
                    data: ['蓝1', '蓝2', '蓝均值', '蓝间距', '蓝间距5期均线'],
                    selected: {
                        '蓝2': false,
                        '蓝间距': false,
                        '蓝均值': false,
                        '蓝间距5期均线': false,
                    }
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
                    max: 12,
                    name: 'y',
                    splitNumber: 11
                },
                series: [{
                    name: '蓝1',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            // color: 'red',
                            // lineStyle: {
                            //     color: '#f36'
                            // }
                        }
                    },
                    data: yb1
                }, {
                    name: '蓝2',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            // color: 'blue',
                            // lineStyle: {
                            //     color: 'blue'
                            // }
                        }
                    },
                    data: yb2
                }, {
                    name: '蓝均值',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            // color: 'blue',
                            // lineStyle: {
                            //     color: 'blue'
                            // }
                        }
                    },
                    data: yb3
                }, {
                    name: '蓝间距',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            // color: 'green',
                            // lineStyle: {
                            //     color: 'green'
                            // }
                        }
                    },
                    data: ybdiff
                }, {
                    name: '蓝间距5期均线',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            // color: 'green',
                            // lineStyle: {
                            //     color: 'green'
                            // }
                        }
                    },
                    data: ybdiff5
                }]
            };
            myChart.setOption(option);
        }
    );
</script>