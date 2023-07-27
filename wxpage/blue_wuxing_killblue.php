<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';

LoginCheck();

$maxid = maxDayid();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>五行杀蓝算法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val()+"&tailnum="+$(this).val();
    }

    $(document).ready(function() {
        $('#limit_num').change(function(){
            window.location.href = "?limit_num="+$("#limit_num").val();
        })
        $("#dataCalcBtn").click(function() {
            $(this).html('计算中...');
            $.ajax({
                url: 'ajax/blue_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'wuhang_kill'
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
        .red_ball{
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
        .blue_ball{
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
        
        <div class="clearfix"></div>
        <p class="lead"></p>
        <div class="bs-example" data-example-id="contextual-table">
            <?php

                $whkill = blueWuXingKill();

            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>上期期数</th>
                        <th>上期开奖</th>
                        <th>杀N行</th>
                        <th>选N行蓝号</th>
                        <th>杀N行蓝号</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $maxid ?></td>
                        <td><?php echo implode(".", $whkill['reds']) ?></td>
                        <td>
                        <?php 
                            echo $whkill['kill_whnum'];
                        ?>
                        </td>
                        <td><?php echo !empty($whkill['wins_blue']) ? implode(".", $whkill['wins_blue']) : '无'; ?></td>
                        <td><?php echo implode(".", $whkill['kill_blue']) ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="clearfix"></div>
            <div class="center-block" id="chart4" style="height: 200px;"></div>
            <div class="center-block" id="chart1" style="height: 200px;"></div>
            <div class="center-block" id="chart2" style="height: 200px;"></div>
            <div class="center-block" id="chart3" style="height: 200px;"></div>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

<?php

$suanfa = array();
$suanfa[1]['date'] = array();
$suanfa[1]['kill_win'] = array();

$suanfa[2]['date'] = array();
$suanfa[2]['kill_win'] = array();

$suanfa[3]['date'] = array();
$suanfa[3]['kill_win'] = array();

$suanfa[4]['date'] = array();
$suanfa[4]['kill_win'] = array();

$limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_blue_whkill` WHERE kill_whnum=1 ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa[1]['date'][] = $xdate;
    $suanfa[1]['kill_win'][] = $row['kill_win'];
}

$suanfa[1]['date']   = array_reverse($suanfa[1]['date']);
$suanfa[1]['kill_win'] = array_reverse($suanfa[1]['kill_win']);

$dosql->Execute("SELECT * FROM `#@__caipiao_blue_whkill` WHERE kill_whnum=2 ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa[2]['date'][] = $xdate;
    $suanfa[2]['kill_win'][] = $row['kill_win'];
}

$suanfa[2]['date']   = array_reverse($suanfa[2]['date']);
$suanfa[2]['kill_win'] = array_reverse($suanfa[2]['kill_win']);

$dosql->Execute("SELECT * FROM `#@__caipiao_blue_whkill` WHERE kill_whnum=3 ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa[3]['date'][] = $xdate;
    $suanfa[3]['kill_win'][] = $row['kill_win'];
}

$suanfa[3]['date']   = array_reverse($suanfa[3]['date']);
$suanfa[3]['kill_win'] = array_reverse($suanfa[3]['kill_win']);

$dosql->Execute("SELECT * FROM `#@__caipiao_blue_whkill` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa[4]['date'][] = $xdate;
    $suanfa[4]['kill_win'][] = $row['kill_win'];
}

$suanfa[4]['date']   = array_reverse($suanfa[4]['date']);
$suanfa[4]['kill_win'] = array_reverse($suanfa[4]['kill_win']);

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
        var title = '五行杀蓝杀1行命中走势图';
        var xData = <?php echo json_encode($suanfa[1]['date']) ?>;

        var yData = <?php echo json_encode($suanfa[1]['kill_win']) ?>;
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
        myChart.setOption(option);

        var title2 = '五行杀蓝杀2行命中走势图';
        var x2Data = <?php echo json_encode($suanfa[2]['date']) ?>;

        var y2Data = <?php echo json_encode($suanfa[2]['kill_win']) ?>;
        var myChart2 = ec.init(document.getElementById('chart2'));
        
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
                data: [title2]
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
                data: x2Data
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
                    name: title2,
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
                    data: y2Data
                }
            ]
        };
        myChart2.setOption(option2);

        var title3 = '五行杀蓝杀3行命中走势图';
        var x3Data = <?php echo json_encode($suanfa[3]['date']) ?>;

        var y3Data = <?php echo json_encode($suanfa[3]['kill_win']) ?>;
        var myChart3 = ec.init(document.getElementById('chart3'));
        
        var option3 = {
            title: {
                text: title3,
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: [title3]
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
                data: x3Data
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
                    name: title3,
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
                    data: y3Data
                }
            ]
        };
        myChart3.setOption(option3);

        var title4 = '五行杀蓝杀命中走势图';
        var x4Data = <?php echo json_encode($suanfa[4]['date']) ?>;

        var y4Data = <?php echo json_encode($suanfa[4]['kill_win']) ?>;
        var myChart4 = ec.init(document.getElementById('chart4'));
        
        var option4 = {
            title: {
                text: title4,
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: [title4]
            },
            xAxis: {
                type: 'category',
                name: 'x',
                splitLine: {show: false},
                data: x4Data
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
                    name: title4,
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
                    data: y4Data
                }
            ]
        };
        myChart4.setOption(option4);
    }
);
</script>