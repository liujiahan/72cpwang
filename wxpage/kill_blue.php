<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$allRed = array();
for ($i=1; $i < 34; $i++) { 
    $i<10 && $i = '0' . $i;
    $allRed[] = $i;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>蓝球八杀法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        var limit_num = $("#limit_num").val();

        if(limit_num == 300){
            window.location.href = "?limit_num="+limit_num+"&width=10000";
        }else if(limit_num == 500){
            window.location.href = "?limit_num="+limit_num+"&width=16500";
        }else{
            window.location.href = "?limit_num="+limit_num;
        }
    }

    $(document).ready(function() {
        $('#kill_x_blue').change(function(){
            window.location.href = "?kill_x_blue="+$(this).val();
        })
        $("#dataCalcBtn").click(function() {
            $(this).html('计算中...');
            $.ajax({
                url: 'ajax/blue_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'kill_blue'
                },
                success: function(data) {
                    // alert("成功计算"+data+"期");
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
            <label for="cp_dayid">篮球八杀法</label>
            <select class="form-control" name="kill_x_blue" id="kill_x_blue">
                <option value="">--请选择--</option>
                <?php foreach (array(1, 2, 3, 4, 5, 6, 7, 8) as $methodid) { ?>
                <option value="<?php echo $methodid ?>" <?php echo isset($kill_x_blue) && $kill_x_blue == $methodid ? 'selected' : ''; ?>>杀法<?php echo $methodid; ?></option>
                <?php } ?>
            </select>
          </div>
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
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">蓝球八杀法计算</a>
        <?php } ?> 
        
        <div class="bs-example" data-example-id="contextual-table">
            <?php
                $allBlue = array();
                for ($i=1; $i <= 16; $i++) { 
                    // $i<10 && $i = '0' . $i;
                    $allBlue[] = $i;
                }

                $next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 1");
                $blue1 = $next1['blue_num'];
                $red_1_6 = explode(',', $next1['red_num']);
                $red_1_6 = array_sum($red_1_6);

                $cp_dayid = $next1['cp_dayid'];
                $next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
                $blue2 = $next2['blue_num'];

                $cp_dayid = $next2['cp_dayid'];
                $next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
                $blue3 = $next3['blue_num'];

                $cp_dayid = $next3['cp_dayid'];
                $next4 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 1");
                $blue4 = $next4['blue_num'];

                $kill_list = array();
                $kill_list[] = $kill_1_blue = killBlue1($blue1, $blue2);
                $kill_list[] = $kill_2_blue = killBlue2($blue1);
                $kill_list[] = $kill_3_blue = killBlue3($blue1, $blue2, $blue3);
                $kill_list[] = $kill_4_blue = killBlue4($blue1, $blue2, $blue3, $blue4);
                $kill_list[] = $kill_5_blue = killBlue5($blue1, $red_1_6);
                $kill_list[] = $kill_6_blue = killBlue6($blue1);
                $kill_list[] = $kill_7_blue = killBlue7($blue1);
                $kill_list[] = $kill_8_blue = killBlue8($blue1);
                $kill_list = array_unique($kill_list);
                sort($kill_list);

            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th width="4%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="6%">蓝球</th>
                        <th width="5%" title="A+B绝杀法">①<?php echo isset($kill_blue_per[1]) ? '/'.$kill_blue_per[1] : ''; ?></th>
                        <th width="5%" title="A+16绝杀法">②<?php echo isset($kill_blue_per[2]) ? '/'.$kill_blue_per[2] : ''; ?></th>
                        <th width="5%" title="A+B+C绝杀法">③<?php echo isset($kill_blue_per[3]) ? '/'.$kill_blue_per[3] : ''; ?></th>
                        <th width="5%" title="A+B+C+D绝杀法">④<?php echo isset($kill_blue_per[4]) ? '/'.$kill_blue_per[4] : ''; ?></th>
                        <th width="5%" title="红球+篮球绝杀法">⑤<?php echo isset($kill_blue_per[5]) ? '/'.$kill_blue_per[5] : ''; ?></th>
                        <th width="5%" title="S+G绝杀法">⑥<?php echo isset($kill_blue_per[6]) ? '/'.$kill_blue_per[6] : ''; ?></th>
                        <th width="5%" title="S-G绝杀法">⑦<?php echo isset($kill_blue_per[7]) ? '/'.$kill_blue_per[7] : ''; ?></th>
                        <th width="5%" title="S*G绝杀法">⑧<?php echo isset($kill_blue_per[8]) ? '/'.$kill_blue_per[8] : ''; ?></th>
                        <th width="20%" title="S*G绝杀法">杀蓝</th>
                        <th width="20%" title="S*G绝杀法">余蓝</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo nextCpDayId($next1['cp_dayid']) ?></td>
                        <td><?php //echo $row['opencode'] ?></td>
                        <td><?php //echo $row['blue_num'] ?></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_1_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_2_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_3_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_4_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_5_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_6_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_7_blue ?></button></td>
                        <td><button class="btn btn-primary" type="button"><?php echo $kill_8_blue ?></button></td>
                        <td>
                        <?php 
                            foreach ($kill_list as $tmp_blue) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</span>
                                </button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            // $kill_list = explode(',', $row['kill_list']);
                            $diffBlue = array_diff($allBlue, $kill_list);
                            foreach ($diffBlue as $tmp_blue) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</span>
                                </button>';
                            }
                        ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>
            <div class="center-block" id="chart4" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <div class="center-block" id="chart1" style="height: 250px;"></div>
            <div class="center-block" id="chart2" style="height: 250px;"></div>
            <div class="center-block" id="chart3" style="height: 250px;"></div>
            <?php

                $kill_blue_count = array();
                $dosql->Execute("SELECT * FROM `#@__caipiao_kill_blue` WHERE cp_dayid>=2017001");
                $total = 0;
                while($row = $dosql->GetArray()){
                    for ($i=1; $i <=8 ; $i++) { 
                        if(!isset($kill_blue_count[$i])){
                            $kill_blue_count[$i] = 0;
                        }
                        if($row['kill_'.$i.'_win'] == 1){
                            $kill_blue_count[$i]++;
                        }
                    }
                    $total++;
                }

                $kill_blue_per = array();
                for ($i=1; $i <=8 ; $i++) {
                    if(isset($kill_blue_count[$i])){
                        $kill_blue_per[$i] = round($kill_blue_count[$i] / $total, 1) * 100 . '%';
                    }
                }

            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th width="4%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="6%">蓝球</th>
                        <th width="5%" title="A+B绝杀法">①<?php echo isset($kill_blue_per[1]) ? '/'.$kill_blue_per[1] : ''; ?></th>
                        <th width="5%" title="A+16绝杀法">②<?php echo isset($kill_blue_per[2]) ? '/'.$kill_blue_per[2] : ''; ?></th>
                        <th width="5%" title="A+B+C绝杀法">③<?php echo isset($kill_blue_per[3]) ? '/'.$kill_blue_per[3] : ''; ?></th>
                        <th width="5%" title="A+B+C+D绝杀法">④<?php echo isset($kill_blue_per[4]) ? '/'.$kill_blue_per[4] : ''; ?></th>
                        <th width="5%" title="红球+篮球绝杀法">⑤<?php echo isset($kill_blue_per[5]) ? '/'.$kill_blue_per[5] : ''; ?></th>
                        <th width="5%" title="S+G绝杀法">⑥<?php echo isset($kill_blue_per[6]) ? '/'.$kill_blue_per[6] : ''; ?></th>
                        <th width="5%" title="S-G绝杀法">⑦<?php echo isset($kill_blue_per[7]) ? '/'.$kill_blue_per[7] : ''; ?></th>
                        <th width="5%" title="S*G绝杀法">⑧<?php echo isset($kill_blue_per[8]) ? '/'.$kill_blue_per[8] : ''; ?></th>
                        <th width="20%" title="S*G绝杀法">杀蓝</th>
                        <th width="20%" title="S*G绝杀法">余蓝</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $allBlue = array();
                        for ($i=1; $i < 16; $i++) { 
                            // $i<10 && $i = '0' . $i;
                            $allBlue[] = $i;
                        }
                        $sql = "SELECT * FROM `#@__caipiao_kill_blue` ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }
                        
                        $dopage->GetPage($sql);
                        while($row = $dosql->GetArray()){
                    ?>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['blue_num'] ?></td>
                        <td><button class="btn btn-<?php echo $row['kill_1_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_1_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_2_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_2_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_3_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_3_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_4_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_4_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_5_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_5_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_6_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_6_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_7_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_7_blue'] ?></button></td>
                        <td><button class="btn btn-<?php echo $row['kill_8_blue'] == $row['blue_num'] ? 'danger' : 'primary' ?>" type="button"><?php echo $row['kill_8_blue'] ?></button></td>
                        <td>
                        <?php 
                            $kill_list = explode(',', $row['kill_list']);
                            foreach ($kill_list as $tmp_blue) {
                                if($tmp_blue == $row['blue_num']){ 
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_blue . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</span>
                                    </button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $kill_list = explode(',', $row['kill_list']);
                            $diffBlue = array_diff($allBlue, $kill_list);
                            foreach ($diffBlue as $tmp_blue) {
                                if($tmp_blue == $row['blue_num']){ 
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_blue . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</span>
                                    </button>';
                                }
                            }
                        ?>
                        </td>
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

<?php

$suanfa = array();
$suanfa['date']    = array();
$suanfa['kill_ok'] = array();

$kill_x_blue = isset($kill_x_blue) ? $kill_x_blue : 1;

$field = 'kill_'.$kill_x_blue.'_win';

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_kill_blue` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $suanfa['date'][] = $xdate;
    $suanfa['kill_ok'][] = $row[$field] == 2 ? $row[$field] - 2 : $row[$field];
}

$suanfa['date']    = array_reverse($suanfa['date']);
$suanfa['kill_ok'] = array_reverse($suanfa['kill_ok']);

$kill_blue = array();
$kill_blue['date']     = array();
$kill_blue['kill_8ok'] = array();
$kill_blue['kill_ok']  = array();

$dosql->Execute("SELECT * FROM `#@__caipiao_kill_blue` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $kill_blue['date'][] = $xdate;
    
    $kill_8ok = -1;
    for ($i=1; $i <=8 ; $i++) { 
        $field = 'kill_'.$i.'_win';
        if(!isset($kill_blue['kill_ok'][$i])){
            $kill_blue['kill_ok'][$i] = array();
        }
        $kill_blue['kill_ok'][$i][] = $row[$field] == 2 ? $row[$field] - 2 : $row[$field];

        if($row[$field] == 2){
            $kill_8ok = 0;
        }
    }
    $kill_blue['kill_8ok'][] = $kill_8ok == -1 ? 1 : 0;
}

$kill_blue['date']     = array_reverse($kill_blue['date']);
$kill_blue['kill_8ok'] = array_reverse($kill_blue['kill_8ok']);
foreach ($kill_blue['kill_ok'] as $i => &$kill) {
    $kill = array_reverse($kill);
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
        var title = '杀法<?php echo $kill_x_blue ?>杀对杀错走势图';
        var xData = <?php echo json_encode($suanfa['date']) ?>;

        var yData = <?php echo json_encode($suanfa['kill_ok']) ?>;
        var myChart = ec.init(document.getElementById('chart1'));

        
        var xData    = <?php echo json_encode($kill_blue['date']) ?>;
        var y1Data   = <?php echo json_encode($kill_blue['kill_ok'][1]) ?>;
        var y2Data   = <?php echo json_encode($kill_blue['kill_ok'][8]) ?>;
        var y3Data   = <?php echo json_encode($kill_blue['kill_ok'][3]) ?>;
        var y4Data   = <?php echo json_encode($kill_blue['kill_ok'][4]) ?>;
        var y5Data   = <?php echo json_encode($kill_blue['kill_ok'][5]) ?>;
        var y6Data   = <?php echo json_encode($kill_blue['kill_ok'][6]) ?>;
        var y7Data   = <?php echo json_encode($kill_blue['kill_ok'][7]) ?>;
        var y8Data   = <?php echo json_encode($kill_blue['kill_ok'][8]) ?>;
        var yk8Data  = <?php echo json_encode($kill_blue['kill_8ok']) ?>;
        var myChart2 = ec.init(document.getElementById('chart2'));
        var myChart3 = ec.init(document.getElementById('chart3'));
        var myChart4 = ec.init(document.getElementById('chart4'));
        
        var option = {
            title: {
                text: '杀法<?php echo $kill_x_blue ?>杀中走势图',
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
                splitLine: {show: false},
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
                max: 1,
                name: 'y',
                splitNumber: 1
            },
            series: [
                {
                    name: title,
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'red',
                        lineStyle:{
                            color:'#f36'  
                        } 
                      }
                    },
                    data: yData
                }
            ]
        };
        var option4 = {
            title: {
                text: '八杀法杀对杀错走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['8杀法杀中走势图[1杀对]']
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
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
                max: 1,
                name: 'y',
                splitNumber: 1
            },
            series: [
                {
                    name: '8杀法杀中走势图[1杀对]',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'red',
                        lineStyle:{
                            color:'#f36'  
                        } 
                      }
                    },
                    data: yk8Data
                }
            ]
        };
        var option2 = {
            title: {
                text: '蓝球8杀法之奇数杀法杀中走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['1杀', '3杀', '5杀', '7杀']
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
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
                max: 1,
                name: 'y',
                splitNumber: 1
            },
            series: [
                {
                    name: '1杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'red',
                        lineStyle:{
                            color:'#f36'  
                        } 
                      }
                    },
                    data: y1Data
                },{
                    name: '3杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#b36',
                        lineStyle:{
                            color:'#b36'  
                        } 
                      }
                    },
                    data: y3Data
                },{
                    name: '5杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#336',
                        lineStyle:{
                            color:'#336'  
                        } 
                      }
                    },
                    data: y5Data
                },{
                    name: '7杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#a3e',
                        lineStyle:{
                            color:'#a3e'  
                        } 
                      }
                    },
                    data: y7Data
                }
            ]
        };
        var option3 = {
            title: {
                text: '蓝球8杀法之偶数杀法杀中走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['2杀', '4杀', '6杀', '8杀']
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
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
                max: 1,
                name: 'y',
                splitNumber: 1
            },
            series: [
                {
                    name: '2杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#a0b',
                        lineStyle:{
                            color:'#a0b'  
                        } 
                      }
                    },
                    data: y2Data
                },{
                    name: '4杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#e0c',
                        lineStyle:{
                            color:'#e0c'  
                        } 
                      }
                    },
                    data: y4Data
                },{
                    name: '6杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'red',
                        lineStyle:{
                            color:'#f36'  
                        } 
                      }
                    },
                    data: y6Data
                },{
                    name: '8杀',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'blue',
                        lineStyle:{
                            color:'blue'  
                        } 
                      }
                    },
                    data: y8Data
                }
            ]
        };
        myChart.setOption(option);
        myChart2.setOption(option2);
        myChart3.setOption(option3);
        myChart4.setOption(option4);
    }
);
</script>
