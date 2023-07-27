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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>500万走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function godayurl() {
            window.location.href = "?act=1&limit_num=" + $("#limit_num").val();
        }

        function gourl() {
            window.location.href = "?act=2&limit_num=30&sid=" + $("#sid").val() + "&eid=" + $("#eid").val() + "&p1s=" + $("#p1s").val() + "&p1b=" + $("#p1b").val();
        }
        $(document).ready(function() {
            $("#duiJiangBtn").click(function() {
                $(this).html('计算中......');
                $.ajax({
                    url: 'ajax/ssq_do.php',
                    dataType: 'html',
                    type: 'post',
                    data: {
                        action: 'pull_ssqprize'
                    },
                    success: function(data) {
                        // console.log(data)
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
                    <label for="exampleInputEmail2">一等奖数量</label>
                    <select class="form-control" name="p1s" id="p1s">
                        <option value="">--请选择--</option>
                        <option value="1">1</option>
                        <?php for ($i = 5; $i < 121; $i = $i + 5) { ?>
                            <option value="<?php echo $i ?>" <?php echo isset($p1s) && $p1s == $i ? 'selected' : '' ?>><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">—</label>
                    <select class="form-control" name="p1b" id="p1b">
                        <option value="">--请选择--</option>
                        <option value="1">1</option>
                        <?php for ($ii = 5; $ii < 121; $ii = $ii + 5) { ?>
                            <option value="<?php echo $ii ?>" <?php echo isset($p1b) && $p1b == $ii ? 'selected' : '' ?>><?php echo $ii ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">开始期数</label>
                    <input type="text" class="form-control" name="sid" id="sid" style="width: 100px;" value="<?php echo isset($sid) ? $sid : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">结束期数</label>
                    <input type="text" class="form-control" name="eid" id="eid" style="width: 100px;" value="<?php echo isset($eid) ? $eid : ''; ?>">
                </div>
                <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
            </form>

            <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">拉取500万信息</a>
            <?php } ?>

            <div class="clearfix"></div>
            <div class="center-block" id="chart" style="height: 320px;<?php echo isset($width) ? "width:" . $width . "px" : ""; ?>"></div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>期数</th>
                            <th>开奖号</th>
                            <th>一等奖</th>
                            <th>二等奖</th>
                            <th>六等奖</th>
                            <th>尾个数</th>
                            <th>大小</th>
                            <th>奇偶</th>
                            <th>区间</th>
                            <th>质合</th>
                            <th>热温冷</th>
                            <th>遗漏和</th>
                            <th>和值</th>
                            <th>AC</th>
                            <th>重号</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $score = array();
                        $prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);

                        $tail4 = array(1, 2, 3);
                        $tail3 = array(4, 5, 6, 7, 8, 9, 0);

                        $act = isset($act) ? $act : 1;
                        if (isset($act) && $act == 1) {

                            $limit_tnum = isset($limit_num) && !empty($limit_num) ? $limit_num : 10;
                            $dosql->Execute("SELECT cp_dayid FROM `lz_caipiao_red_tail` ORDER BY cp_dayid DESC LIMIT $limit_tnum");
                            $cp_dayid = 999999999;
                            while ($t = $dosql->GetArray()) {
                                if ($t['cp_dayid'] < $cp_dayid) $cp_dayid = $t['cp_dayid'];
                            }

                            $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid >= $cp_dayid ORDER BY cp_dayid ASC";
                            $dopage->GetPage($sql, 10);
                        } else if (isset($act) && $act == 2) {
                            if (empty($sid) && !empty($eid)) {
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid <= $eid ORDER BY cp_dayid ASC";
                            } else if (!empty($sid) && empty($eid)) {
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid >= $sid ORDER BY cp_dayid ASC";
                            } else if (!empty($sid) && !empty($eid)) {
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid >= $sid AND cp_dayid <= $eid ORDER BY cp_dayid ASC";
                            } else if (!empty($p1s) && !empty($p1b)) {
                                $sql = "SELECT a.* FROM `#@__caipiao_history` a INNER JOIN `#@__caipiao_history_prize` b ON a.cp_dayid=b.cp_dayid WHERE b.p1 >= $p1s AND b.p1 <= $p1b ORDER BY a.cp_dayid ASC";
                            } else {
                                $dosql->Execute("SELECT cp_dayid FROM `lz_caipiao_red_tail` ORDER BY cp_dayid DESC LIMIT 10");
                                $cp_dayid = 999999999;
                                while ($t = $dosql->GetArray()) {
                                    if ($t['cp_dayid'] < $cp_dayid) $cp_dayid = $t['cp_dayid'];
                                }

                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid >= $cp_dayid ORDER BY cp_dayid ASC";
                            }
                            $dopage->GetPage($sql, 10);
                        }

                        $i = 0;
                        while ($row = $dosql->GetArray()) {
                            $i++;
                            $tail = $dosql->GetOne("SELECT * FROM `#@__caipiao_red_tail` WHERE cp_dayid='{$row['cp_dayid']}'");
                            $red_tail_detail = unserialize($tail['red_tail_detail']);
                            $row['tail_num'] = $red_tail_detail['tail_num'];

                            $prizer = $dosql->GetOne("SELECT * FROM `#@__caipiao_history_prize` WHERE cp_dayid='{$row['cp_dayid']}'");
                            if (!isset($prizer['id'])) continue;
                            $row['p1'] = $prizer['p1'];
                            $row['p1_bonus'] = round($prizer['p1_bonus'] / 10000) . '万';
                            $row['p2'] = $prizer['p2'];
                            $row['p2_bonus'] = round($prizer['p2_bonus'] / 10000) . '万';
                            $row['p6'] = $prizer['p6'];

                            $score[$row['cp_dayid']] = array();
                            $red_num = explode(',', $row['red_num']);
                            $bigsmall = array(0 => 0, 1 => 0);
                            $oddeven = array(0 => 0, 1 => 0);
                            $redarea = array(0 => 0, 1 => 0, 2 => 0);
                            $primenum = array(0 => 0, 1 => 0);

                            $before_id = $row['cp_dayid'] - 1;
                            $one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']}");
                            $before_reds = explode(',', $one['red_num']);
                            $repeat_num = 0;

                            $weisum = 0;
                            $tailgroup = array();
                            $tail4_3 = array(0 => 0, 1 => 0);
                            foreach ($red_num as $red) {
                                $red > 16 && $bigsmall[0]++;
                                $red < 17 && $bigsmall[1]++;

                                $red % 2 == 1 && $oddeven[0]++;
                                $red % 2 == 0 && $oddeven[1]++;

                                $red < 12 && $redarea[0]++;
                                $red > 11 && $red < 23 && $redarea[1]++;
                                $red > 22 && $redarea[2]++;

                                in_array($red, $prime) && $primenum[0]++;
                                !in_array($red, $prime) && $red != 1 && $primenum[1]++;

                                $tail = $red % 10;
                                if (in_array($tail, $tail4)) {
                                    $tail4_3[0]++;
                                } else {
                                    $tail4_3[1]++;
                                }
                                in_array($red, $before_reds) && $repeat_num++;


                                $weisum += $tail;
                                $tailgroup[] = $tail;
                            }
                            $tailgroup = array_unique($tailgroup);

                            $sum = array_sum($red_num);
                            $acnum = getAC($red_num);

                            $curmiss = getCurMiss($row['cp_dayid']);

                            //蓝球
                            $row['blue_num'] < 10 && $row['blue_num']              = '0' . $row['blue_num'];
                            $itemsData['blue'][$row['cp_dayid']][$row['blue_num']] = $row['blue_num'];
                            $itemsData['ac'][$row['cp_dayid']][$acnum]             = $acnum;
                            $itemsData['repeat'][$row['cp_dayid']][$repeat_num]    = $repeat_num;
                            $itemsData['bigsmall'][$row['cp_dayid']][implode(":", $bigsmall)] = implode(":", $bigsmall);
                            $itemsData['oddeven'][$row['cp_dayid']][implode(":", $oddeven)]   = implode(":", $oddeven);
                            $itemsData['primenum'][$row['cp_dayid']][implode(":", $primenum)] = implode(":", $primenum);
                            $itemsData['redarea'][$row['cp_dayid']][implode("", $redarea)]    = implode("", $redarea);
                            $itemsData['tail4_3'][$row['cp_dayid']][implode(":", $tail4_3)]   = implode(":", $tail4_3);
                            $itemsData['tailgroup'][$row['cp_dayid']][count($tailgroup)]      = count($tailgroup);

                            $indexScore = indexScore();

                            $score[$row['cp_dayid']][] = $bigsmall_score = $indexScore['bigsmall'][implode(":", $bigsmall)];
                            $score[$row['cp_dayid']][] = $oddeven_score = $indexScore['oddeven'][implode(":", $oddeven)];
                            $score[$row['cp_dayid']][] = $redarea_score = $indexScore['redarea'][implode(":", $redarea)];
                            $score[$row['cp_dayid']][] = $cool_hot_score = $indexScore['cool_hot'][implode(":", $curmiss['cool_hot'])];
                            $score[$row['cp_dayid']][] = $primenum_score = $indexScore['primenum'][implode(":", $primenum)];
                            $score[$row['cp_dayid']][] = $tail4_3_score = $indexScore['tail4_3'][implode(":", $tail4_3)];

                            $sumlist = itemsTable('sum');
                            foreach ($sumlist as $key => $sumstr) {
                                $sumarr = explode('-', $sumstr);
                                if ($sum >= $sumarr[0] && $sum <= $sumarr[1]) {
                                    $itemsData['sum'][$row['cp_dayid']][$sumstr] = $sum;
                                    break;
                                }
                            }

                            $taillist = itemsTable('tailnum');
                            foreach ($taillist as $key => $tailstr) {
                                $tailarr = explode('-', $tailstr);
                                if ($weisum >= $tailarr[0] && $weisum <= $tailarr[1]) {
                                    $itemsData['tailnum'][$row['cp_dayid']][$tailstr] = $weisum;
                                    break;
                                }
                            }

                            $misslist = itemsTable('misssum');
                            foreach ($misslist as $key => $missstr) {
                                $missarr = explode('-', $missstr);
                                if ($curmiss['miss_sum'] >= $missarr[0] && $curmiss['miss_sum'] <= $missarr[1]) {
                                    $itemsData['misssum'][$row['cp_dayid']][$missstr] = $curmiss['miss_sum'];
                                    break;
                                }
                            }
                            $itemsData['hotcool'][$row['cp_dayid']][implode(":", $curmiss['cool_hot'])] = implode(":", $curmiss['cool_hot']);
                        ?>
                            <tr>
                                <td><a target="_blank" href="prize_list.php?act=2&limit_num=30&sid=<?php echo $row['cp_dayid'] ?>&eid=<?php echo $row['cp_dayid'] + 5 ?>"><?php echo $row['cp_dayid'] ?></a></td>
                                <td><?php echo $row['opencode'] ?></td>
                                <td><?php echo $row['p1'] . "（" . $row['p1_bonus'] . "）" ?></td>
                                <td><?php echo $row['p2'] . "（" . $row['p2_bonus'] . "）" ?></td>
                                <td><?php echo $row['p6'] ?></td>
                                <td><?php echo $row['tail_num'] ?></td>
                                <td><?php echo implode(":", $bigsmall) ?></td>
                                <td><?php echo implode(":", $oddeven) ?></td>
                                <td><?php echo implode(":", $redarea) ?></td>
                                <td><?php echo implode(":", $primenum) ?></td>
                                <td><?php echo implode(":", $curmiss['cool_hot']) ?></td>

                                <td><?php echo $curmiss['miss_sum'] ?></td>
                                <td><?php echo $sum ?></td>
                                <td><?php echo $acnum ?></td>
                                <td><?php echo $repeat_num ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php echo $dopage->GetList(); ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
<?php

$prize = array();
$prize['p1'] = array();
$prize['pmax'] = 0;

$limit_num = isset($limit_num) ? $limit_num : 30;
if ((!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit) {
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_history_prize` ORDER BY cp_dayid DESC LIMIT $limit_num");
while ($row = $dosql->GetArray()) {
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4) . '000');
    if ($xdate == 1) {
        $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $prize['date'][] = $xdate;
    $prize['p1'][] = $row['p1'];
    if ($prize['pmax'] < $row['p1']) $prize['pmax'] = $row['p1'];
}

if ($prize['pmax'] % 6) {
    $y = $prize['pmax'] % 6;
    $prize['pmax'] += 6 - $y;
}

$prize['date'] = array_reverse($prize['date']);
$prize['p1'] = array_reverse($prize['p1']);

?>
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
            var myChart = ec.init(document.getElementById('chart'));
            var title = '五百万井喷走势图';

            var xData = <?php echo json_encode($prize['date']) ?>;
            var y1Data = <?php echo json_encode($prize['p1']) ?>;

            var option = {
                title: {
                    text: title,
                    left: 'center'
                },
                legend: {
                    left: 'left',
                    data: ['一等奖数量']
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
                    type: 'log',
                    min: 0,
                    max: <?php echo $prize['pmax'] ?>,
                    splitNumber: 6,
                    name: 'y'
                },
                series: [{
                    name: '一等奖数量',
                    type: 'line',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            },
                            color: 'red',
                            lineStyle: {
                                color: 'red'
                            }
                        }
                    },
                    data: y1Data
                }]
            };
            myChart.setOption(option);
        }
    );
</script>