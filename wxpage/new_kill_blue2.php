<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';


LoginCheck();

$allBlue = array();
for ($i=1; $i < 17; $i++) { 
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
    <title>蓝球八KF - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
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
                    action: 'new_kill_blue2'
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
            <label for="cp_dayid">篮球八KF</label>
            <select class="form-control" name="kill_x_blue" id="kill_x_blue">
                <option value="">--请选择--</option>
                <?php foreach (array(1, 2, 3, 4, 5, 6, 7, 8) as $methodid) { ?>
                <option value="<?php echo $methodid ?>" <?php echo isset($kill_x_blue) && $kill_x_blue == $methodid ? 'selected' : ''; ?>>KF<?php echo $methodid; ?></option>
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
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">蓝球八KF计算</a>
        <?php } ?>
        
        <div class="bs-example" data-example-id="contextual-table">
            <?php

                $next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 1");
                $sqblue1 = isset($next1['blue_num']) ? $next1['blue_num'] : 0;
                $cp_dayid2 = isset($next1['cp_dayid']) ? $next1['cp_dayid'] : 0;

                $next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid2 ORDER BY cp_dayid DESC LIMIT 1");
                $sqblue2 = isset($next2['blue_num']) ? $next2['blue_num'] : 0;
                $cp_dayid3 = isset($next2['cp_dayid']) ? $next2['cp_dayid'] : 0;

                $next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid3 ORDER BY cp_dayid DESC LIMIT 1");
                $sqblue3 = isset($next3['blue_num']) ? $next3['blue_num'] : 0;

                $kill_1_blue = NewkillBlue1($sqblue1, 15);
                $kill_2_blue = NewkillBlue1($sqblue1, 19);
                $kill_3_blue = NewkillBlue1($sqblue1, 21);
                $kill_4_blue = NewkillBlue2($sqblue1, $sqblue2);
                $kill_5_blue = NewkillBlue3($sqblue1, $sqblue2);
                $kill_6_blue = NewkillBlue4($sqblue1, $sqblue2);
                $kill_7_blue = NewkillBlue44($sqblue1, $sqblue3);
                $kill_8_blue = NewkillBlue5($sqblue1, 2);
                $kill_9_blue = NewkillBlue5($sqblue1, 4);
                $kill_10_blue = NewkillBlue6($sqblue1, 7);
                $kill_11_blue = NewkillBlue7($sqblue1, 6);

                $kill_list = array();

                $kill_json = array();
                for ($i=1; $i <=11 ; $i++) { 
                    $arr = 'kill_'.$i.'_blue';
                    $kill_json['kb'.$i] = array();
                    $kill_json['kb'.$i]['list'] = $$arr;
                    // $kill_json['kb'.$i]['kill'] = in_array($row['blue_num'], $$arr) ? 0 : 1;
                    foreach ($$arr as $kblue) {
                        $kill_list[] = $kblue;
                    }
                }
                $kill_list = array_unique($kill_list);
                sort($kill_list);

                $diff = array_diff($allBlue, $kill_list);

                $kill_list = implode(',', $kill_list);

            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>期数</th>
                        <th>蓝</th>
                        <th>杀蓝</th>
                        <th>余蓝</th>
                        <th>KF1</th>
                        <th>KF2</th>
                        <th>KF3</th>
                        <th>KF4</th>
                        <th>KF5</th>
                        <th>KF6</th>
                        <th>KF7</th>
                        <th>KF8</th>
                        <th>KF9</th>
                        <th>KF10</th>
                        <th>KF11</th>
                        <!-- <th>KF12</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo nextCpDayId($cp_dayid2) ?></td>
                        <td></td>
                        <td><?php echo $kill_list ?></td>
                        <td><?php echo implode(",", $diff) ?></td>
                        <?php for ($i=1; $i < 12; $i++) { 
                            $kb = $kill_json['kb'.$i];
                        ?>
                        <td>
                            <?php foreach ($kb['list'] as $blue) { ?>
                            <span class="red_ball"><?php echo $blue ?></span>
                            <?php } ?>
                        </td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>
            <div class="center-block" id="chart1" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <!-- <div class="center-block" id="chart2" style="height: 250px;"></div>
            <div class="center-block" id="chart3" style="height: 250px;"></div>
            <div class="center-block" id="chart4" style="height: 250px;"></div> -->
            <table class="table">
                <thead>
                    <tr>
                        <th>期数</th>
                        <th>蓝</th>
                        <th>杀蓝</th>
                        <th>余蓝</th>
                        <th>KF1</th>
                        <th>KF2</th>
                        <th>KF3</th>
                        <th>KF4</th>
                        <th>KF5</th>
                        <th>KF6</th>
                        <th>KF7</th>
                        <th>KF8</th>
                        <th>KF9</th>
                        <th>KF10</th>
                        <th>KF11</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        
                        $sql = "SELECT * FROM `#@__caipiao_newkill_blue2` ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }
                        $dopage->GetPage($sql, 10);
                        while($row = $dosql->GetArray()){
                            $kill_json = json_decode($row['kill_json'], true);
                            // print_r($kill_json);die;
                    ?>
                    <tr>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><span class="blue_ball active"><?php echo $row['blue_num'] ?></span></td>
                        <td><?php echo $row['kill_list'] ?></td>
                        <td><?php echo implode(",", array_diff($allBlue, explode(',', $row['kill_list']))) ?></td>
                        <?php for ($i=1; $i < 12; $i++) { 
                            $kb = $kill_json['kb'.$i];
                        ?>
                        <td>
                            <?php foreach ($kb['list'] as $blue) { ?>
                            <span class="red_ball <?php echo $row['kill_win'] == 0 && $blue == $row['blue_num'] ? 'active' : ''; ?>"><?php echo $blue ?></span>
                            <!-- <button class="btn btn-primary" type="button"><?php echo $blue ?></button> -->
                            <?php } ?>
                        </td>
                        <?php } ?>
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

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_newkill_blue2` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $suanfa['date'][] = $xdate;
    $suanfa['kill_ok'][] = $row['kill_win'];
}

$suanfa['date']    = array_reverse($suanfa['date']);
$suanfa['kill_ok'] = array_reverse($suanfa['kill_ok']);

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
        var title = '11招杀蓝杀中走势图[1余蓝命中]';
        var xData = <?php echo json_encode($suanfa['date']) ?>;

        var yData = <?php echo json_encode($suanfa['kill_ok']) ?>;
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
    }
);
</script>