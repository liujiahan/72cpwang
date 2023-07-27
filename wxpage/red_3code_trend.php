<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>三码走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
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
            $('#cp_dayid').change(function() {
                var limit_num = $("#limit_num").val();
                if (!limit_num) {
                    limit_num = 30;
                }
                window.location.href = "?cp_dayid=" + $(this).val() + "&limit_num=" + limit_num;
            })
        })
    </script>
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
                <div class="form-group">
                    <label for="exampleInputEmail2">回头望</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="lookdata()">
                        <option value="">--请选择--</option>
                        <?php foreach ($cp_dayids as $tmpid) { ?>
                            <option value="<?php echo $tmpid ?>" <?php echo isset($cp_dayid) && $cp_dayid == $tmpid ? 'selected' : '' ?>><?php echo $tmpid ?>期</option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            <div class="bs-example" data-example-id="contextual-table">
                <div class="clearfix"></div>
                <div class="center-block" id="chart1" style="height: 300px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>
            </div>
        </div>
    </nav>
</body>

<?php

$coolHot = array();
$coolHot['date']     = array();
$coolHot['missnum']  = array();
$coolHot['freq'] = array();
$coolHot['win'] = array();
$coolHot['all_win'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;

if ((!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit) {
    $limit_num = $no_admin_limit;
}

if (isset($cp_dayid) && !empty($cp_dayid)) {
    $dosql->Execute("SELECT * FROM `#@__caipiao_3code_chart` WHERE cp_dayid<=$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit_num");
} else {
    $dosql->Execute("SELECT * FROM `#@__caipiao_3code_chart` ORDER BY cp_dayid DESC LIMIT $limit_num");
}
$next_xdata = 0;
$max_missnum = 0;
while ($row = $dosql->GetArray()) {
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4) . '000');
    if ($xdate == 1) {
        $xdate = substr($row['cp_dayid'], 0, 4);
    }
    if ($next_xdata == 0) {
        $next_xdata = $xdate + 1;
    }
    if ($row['missnum'] > $max_missnum) $max_missnum = $row['missnum'];
    $coolHot['date'][]     = $xdate;
    $coolHot['missnum'][]  = $row['missnum'];
    $coolHot['freq'][] = $row['freq'];
    $coolHot['win'][] = $row['win'];
    $coolHot['all_win'][] = $row['all_win'];
}
$yu = $max_missnum % 10;
$bu = $yu > 0 ? 10 - $yu : 0;
$max_missnum += $bu;
$level = ceil($max_missnum / 5);

$coolHot['date']     = array_reverse($coolHot['date']);
$coolHot['missnum']  = array_reverse($coolHot['missnum']);
$coolHot['freq'] = array_reverse($coolHot['freq']);
$coolHot['win'] = array_reverse($coolHot['win']);
$coolHot['all_win'] = array_reverse($coolHot['all_win']);

?>

</html>
<script type="text/javascript" src="./js/echarts.js"></script>
<!-- <script type="text/javascript" src="./js/echarts.min.js"></script> -->
<!-- <script type="text/javascript" src="./js/echarts.simple.min.js"></script> -->
<!-- <script type="text/javascript" src="js/chart/radar.js"></script> -->
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
            var title = '三码趋势图';
            var xData = <?php echo json_encode($coolHot['date']) ?>;
            var missnum = <?php echo json_encode($coolHot['missnum']) ?>;
            var freq = <?php echo json_encode($coolHot['freq']) ?>;
            var win = <?php echo json_encode($coolHot['win']) ?>;
            var all_win = <?php echo json_encode($coolHot['all_win']) ?>;

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
                    data: ['遗漏期数', '该遗漏组数', '命中', '总命中'],
                    selected: {
                        '该遗漏组数': false,
                        '命中': false,
                        '总命中': false,
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
                    min: 0,
                    max: <?php echo $max_missnum ?>,
                    name: 'y',
                    splitNumber: <?php echo $level ?>
                },
                series: [{
                    name: '遗漏期数',
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
                    data: missnum
                }, {
                    name: '该遗漏组数',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            color: '#E89963',
                            lineStyle: {
                                color: '#E89963'
                            }
                        }
                    },
                    data: freq
                }, {
                    name: '命中',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            color: '#3DAAFE',
                            lineStyle: {
                                color: '#3DAAFE'
                            }
                        }
                    },
                    data: win
                }, {
                    name: '总命中',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            color: '#3DAAFE',
                            lineStyle: {
                                color: '#3DAAFE'
                            }
                        }
                    },
                    data: all_win
                }]
            };
            myChart.setOption(option);
        }
    );
</script>