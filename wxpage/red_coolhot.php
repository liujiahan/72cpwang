<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
LoginCheck();

$allRed = array();
for ($i = 1; $i < 34; $i++) {
    $i < 10 && $i = '0' . $i;
    $allRed[] = $i;
}
$allBlue = array();
for ($i = 1; $i < 17; $i++) {
    $i < 10 && $i = '0' . $i;
    $allBlue[] = $i;
}

$dosql->Execute("SELECT cp_dayid FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 50");
$cp_dayids = array();
while ($row = $dosql->GetArray()) {
    $cp_dayids[] = $row['cp_dayid'];
}
// $cp_dayids = array_reverse($cp_dayids);


?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球遗漏冷热走势 - <?php echo $cfg_seotitle; ?></title>
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
            $("#doData").click(function() {
                $(this).html('同步中......');
                $.ajax({
                    url: 'ajax/red_suanfa_do.php',
                    dataType: 'html',
                    type: 'post',
                    data: {
                        action: 'red_coolhot'
                    },
                    success: function(data) {
                        window.location.reload();
                    }
                })
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
            <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">红球遗漏冷热出球</a>
            <?php } ?>
            <!-- <div class="clearfix"></div> -->
            <!-- <div class="center-block" id="chart2" style="height: 250px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div> -->
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="20%">下期</th>
                            <th width="45%">热号及遗漏期数</th>
                            <th width="20%">温号及遗漏期数</th>
                            <th width="15%">冷号及遗漏期数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if (!empty($cp_dayid)) {
                            $new_cp_dayid = $cp_dayid;
                        } else {
                            $new = $dosql->GetOne("SELECT MAX(cp_dayid) as max FROM `#@__caipiao_history`");
                            $lastyear = substr($new['max'], 0, 4);
                            if (date("Y") != $lastyear) {
                                $new_cp_dayid = date("Y") . '001';
                            } else {
                                $new_cp_dayid = $new['max'] + 1;
                            }
                        }

                        $missArr = array('hot' => array(), 'warm' => array(), 'cool' => array());
                        for ($i = 0; $i < 5; $i++) {
                            $missArr['hotgroup'][$i] = array();
                        }
                        for ($i = 5; $i < 10; $i++) {
                            $missArr['warmgroup'][$i] = array();
                        }
                        $winreds = array();
                        if (isset($cp_dayid)) {
                            $wins = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                            if (!empty($wins['red_num'])) {
                                $winreds = explode(',', $wins['red_num']);
                            }
                            $all_red_miss = redMissing($cp_dayid);
                        } else {
                            $all_red_miss = redMissing();
                        }
                        $miss_num_total = array_sum($all_red_miss);
                        foreach ($all_red_miss as $tmp_red => $tmp_miss) {
                            if ($tmp_miss >= 0 && $tmp_miss <= 4) {
                                $missArr['hotgroup'][$tmp_miss][$tmp_red] = $tmp_miss;
                                $missArr['hot'][$tmp_red] = $tmp_miss;
                            } else if ($tmp_miss >= 5 && $tmp_miss <= 9) {
                                $missArr['warm'][$tmp_red] = $tmp_miss;
                                $missArr['warmgroup'][$tmp_miss][$tmp_red] = $tmp_miss;
                            } else if ($tmp_miss > 9) {
                                $missArr['cool'][$tmp_red] = $tmp_miss;
                            }
                        }
                        ?>

                        <tr class="info">
                            <?php if (!empty($cp_dayid)) { ?>
                                <td><?php echo $cp_dayid ?></td>
                            <?php } else { ?>
                                <td><?php echo $new_cp_dayid; ?></td>
                            <?php } ?>
                            <td>
                                <?php
                                foreach ($missArr['hotgroup'] as $tmp_miss2 => $tmp_reds) {
                                    foreach ($tmp_reds as $tmp_red => $tmp_miss) {
                                        $class = $tmp_miss == 4 ? 'btn-danger' : 'btn-primary';
                                        if (in_array($tmp_red, $winreds)) {
                                            $class = 'btn-warning';
                                        }
                                        echo '<a href="red_kchart.php?cur_red=' . $tmp_red . '" target="_blank"><button class="btn ' . $class . '" type="button">' . $tmp_red . ' <span class="badge">' . $tmp_miss . '</button></a>';
                                    }
                                    echo '<br />';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                foreach ($missArr['warmgroup'] as $tmp_miss2 => $tmp_reds) {
                                    if (empty($tmp_reds)) continue;
                                    foreach ($tmp_reds as $tmp_red => $tmp_miss) {
                                        $class = $tmp_miss == 9 ? 'btn-danger' : 'btn-primary';
                                        if (in_array($tmp_red, $winreds)) {
                                            $class = 'btn-warning';
                                        }
                                        echo '<a href="red_kchart.php?cur_red=' . $tmp_red . '" target="_blank"><button class="btn ' . $class . '" type="button">' . $tmp_red . ' <span class="badge">' . $tmp_miss . '</button></a>';
                                    }
                                    echo '<br />';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                foreach ($missArr['cool'] as $tmp_red => $tmp_miss) {
                                    $class = 'btn-primary';
                                    if (in_array($tmp_red, $winreds)) {
                                        $class = 'btn-warning';
                                    }
                                    echo '<a href="red_kchart.php?cur_red=' . $tmp_red . '" target="_blank"><button class="btn ' . $class . '"  type="button">' . $tmp_red . ' <span class="badge">' . $tmp_miss . '</button></a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="info">
                            <td><?php echo $new_cp_dayid ?>【<?php echo $miss_num_total ?>】</td>
                            <td>
                                <?php
                                echo "热号数" . count($missArr['hot']) . "个：" . implode(".", array_keys($missArr['hot']));
                                ?>
                            </td>
                            <td>
                                <?php
                                echo "温号数" . count($missArr['warm']) . "个：" . implode(".", array_keys($missArr['warm']));
                                ?>
                            </td>
                            <td>
                                <?php
                                echo "冷号数" . count($missArr['cool']) . "个：" . implode(".", array_keys($missArr['cool']));
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="center-block" id="chart2" style="height: 250px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>

                <div class="center-block" id="chart1" style="height: 300px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>
                <div class="center-block" id="chart11" style="height: 300px;<?php echo isset($width) ? ' width: ' . $width . 'px;' : ''; ?>"></div>

                <?php /*
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="7%">热-温-冷</th>
                        <th width="8%">遗漏和值</th>
                        <th width="40%">出球和遗漏</th>
                        <th width="30%">余球和遗漏</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sql = "SELECT * FROM `#@__caipiao_cool_hot` ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }

                        $dopage->GetPage($sql, 5);
                        $i = 0;
                        while($row = $dosql->GetArray()){
                            $i++;
                    ?>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['hot_num'] . ' - ' . $row['warm_num'] . ' - ' . $row['cool_num'] ?></td>
                        <td><?php echo $row['miss_sum'] ?></td>
                        <td>
                        <?php 
                        	$win_miss = unserialize($row['win_miss']);
                            foreach ($win_miss as $tmp_red => $tmp_miss) {
                                echo '<button class="btn btn-danger" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                            }
                        ?>
                        </td>
                        <td>
                        	<?php 
                        		$win_miss = array_keys($win_miss);
								$miss_content = unserialize($row['miss_content']);
                        	    foreach ($miss_content as $tmp_red => $tmp_miss) {
                        	    	if(in_array($tmp_red, $win_miss)){
                        	    		continue;
                        	    	}
                        	        echo '<button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                        	    }
                        	?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $dopage->GetList(); ?>
            */ ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

<?php

$coolHot = array();
$coolHot['date']     = array();
$coolHot['hot_num']  = array();
$coolHot['warm_num'] = array();
$coolHot['cool_num'] = array();
$coolHot['miss_sum'] = array();
$coolHot['all_miss_sum'] = array();
$coolHot['max_min']  = array('max' => 0, 'min' => 0);
$coolHot['miss_max_min']  = array('max' => 0, 'min' => 0);

$limit_num = isset($limit_num) ? $limit_num : 30;

if ((!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit) {
    $limit_num = $no_admin_limit;
}

if (isset($cp_dayid) && !empty($cp_dayid)) {
    $dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid<=$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit_num");
} else {
    $dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot` ORDER BY cp_dayid DESC LIMIT $limit_num");
}
$next_xdata = 0;
while ($row = $dosql->GetArray()) {
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4) . '000');
    if ($xdate == 1) {
        $xdate = substr($row['cp_dayid'], 0, 4);
    }
    if ($next_xdata == 0) {
        $next_xdata = $xdate + 1;
    }
    $coolHot['date'][]     = $xdate;
    $coolHot['hot_num'][]  = $row['hot_num'];
    $coolHot['warm_num'][] = $row['warm_num'];
    $coolHot['cool_num'][] = $row['cool_num'];
    $coolHot['miss_sum'][] = $row['miss_sum'];
    $miss_num_total = array_sum(unserialize($row['miss_content']));
    $coolHot['all_miss_sum'][] = $miss_num_total;

    if ($coolHot['max_min']['max'] < $row['miss_sum']) {
        $coolHot['max_min']['max'] = $row['miss_sum'];
    }
    if ($coolHot['max_min']['min'] == 0) {
        $coolHot['max_min']['min'] = $row['miss_sum'];
    }
    if ($coolHot['max_min']['min'] > $row['miss_sum']) {
        $coolHot['max_min']['min'] = $row['miss_sum'];
    }

    if ($coolHot['miss_max_min']['max'] < $miss_num_total) {
        $coolHot['miss_max_min']['max'] = $miss_num_total;
    }
    if ($coolHot['miss_max_min']['min'] == 0) {
        $coolHot['miss_max_min']['min'] = $miss_num_total;
    }
    if ($coolHot['miss_max_min']['min'] > $miss_num_total) {
        $coolHot['miss_max_min']['min'] = $miss_num_total;
    }
}

if ($coolHot['max_min']['max'] % 6) {
    $y = $coolHot['max_min']['max'] % 6;
    $coolHot['max_min']['max'] += 6 - $y;
}

if ($coolHot['miss_max_min']['max'] % 5) {
    $y = $coolHot['miss_max_min']['max'] % 5;
    $coolHot['miss_max_min']['max'] += 5 - $y;
}
if ($coolHot['miss_max_min']['min'] % 5) {
    $y = $coolHot['miss_max_min']['min'] % 5;
    $coolHot['miss_max_min']['min'] -= $y;
}

$coolHot['date']     = array_reverse($coolHot['date']);
$coolHot['hot_num']  = array_reverse($coolHot['hot_num']);
$coolHot['warm_num'] = array_reverse($coolHot['warm_num']);
$coolHot['cool_num'] = array_reverse($coolHot['cool_num']);
$coolHot['miss_sum'] = array_reverse($coolHot['miss_sum']);
$coolHot['all_miss_sum'] = array_reverse($coolHot['all_miss_sum']);
$coolHot['date2'] = $coolHot['date'];
if (!isset($cp_dayid)) {
    $coolHot['date2'][] = $next_xdata;
    $coolHot['all_miss_sum'][] = array_sum($all_red_miss);
}

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
            var title = '奖号遗漏和值走势图';
            var title11 = '整体遗漏和值走势图';
            var title2 = '热温冷号命中数走势图';
            var xData = <?php echo json_encode($coolHot['date']) ?>;
            var xData2 = <?php echo json_encode($coolHot['date2']) ?>;

            var yData = <?php echo json_encode($coolHot['miss_sum']) ?>;
            var y_allmissData = <?php echo json_encode($coolHot['all_miss_sum']) ?>;
            var miss_sum_max = <?php echo json_encode($coolHot['max_min']['max']) ?>;
            var miss_sumtotal_max = <?php echo json_encode($coolHot['miss_max_min']['max']) ?>;
            var miss_sumtotal_min = <?php echo json_encode($coolHot['miss_max_min']['min']) ?>;
            var miss_sum_min = <?php echo json_encode($coolHot['max_min']['min']) ?>;

            var y_hotData = <?php echo json_encode($coolHot['hot_num']) ?>;
            var y_warmData = <?php echo json_encode($coolHot['warm_num']) ?>;
            var y_coolData = <?php echo json_encode($coolHot['cool_num']) ?>;

            var myChart = ec.init(document.getElementById('chart1'));
            var myChart11 = ec.init(document.getElementById('chart11'));
            var myChart2 = ec.init(document.getElementById('chart2'));

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
                    data: ['奖号遗漏和值']
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
                    max: miss_sum_max,
                    name: 'y',
                    splitNumber: 6
                },
                series: [{
                    name: '奖号遗漏和值',
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
            var option11 = {
                title: {
                    text: title11,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c}'
                },
                legend: {
                    left: 'left',
                    data: ['整体遗漏和值']
                },
                xAxis: {
                    type: 'category',
                    name: 'x',
                    splitLine: {
                        show: false
                    },
                    data: xData2
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
                    min: miss_sumtotal_min,
                    max: miss_sumtotal_max,
                    name: 'y',
                    splitNumber: 5
                },
                series: [{
                    name: '整体遗漏和值',
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
                    data: y_allmissData
                }]
            };
            var option2 = {
                title: {
                    text: title2,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c}'
                },
                legend: {
                    left: 'left',
                    data: ['热号出球数', '温号出球数', '冷号出球数'],
                    selected: {
                        '冷号出球数': false,
                        '温号出球数': false,
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
                    max: 6,
                    name: 'y',
                    splitNumber: 6
                },
                series: [{
                    name: '热号出球数',
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
                    data: y_hotData
                }, {
                    name: '温号出球数',
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
                    data: y_warmData
                }, {
                    name: '冷号出球数',
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
                    data: y_coolData
                }]
            };
            myChart.setOption(option);
            myChart11.setOption(option11);
            myChart2.setOption(option2);
        }
    );
</script>