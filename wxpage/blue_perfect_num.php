<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';


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
    <title>五期蓝球定蓝与心水集团码 - <?php echo $cfg_seotitle; ?></title>
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
                    action: 'blue_xsh'
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
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">定蓝</a>
        <?php } ?>
        
        <div class="clearfix"></div>
        <div class="center-block" id="chart1" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <?php

            	$redlist5 = array();

                $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 5");
                while($row = $dosql->GetArray()){
                	$redlist5[] = $row;
                }

                $bluexsh = blueXSH($redlist5);

            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>期数</th>
                        <th>区间</th>
                        <th>心水集团号</th>
                        <th>备选1</th>
                        <th>备选2</th>
                        <th>备选3</th>
                        <th>备选4</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo nextCpDayId($bluexsh['cp_dayid']) ?></td>
                        <td><?php echo $bluexsh['blue_range'] ?></td>
                        <td>
                        <?php 
                        	$blue_list = explode(',',$bluexsh['blue_list']);
                        	foreach ($blue_list as $key => &$value) {
                        		if($value<10)
                        			$value = '0'.$value;
                        	}
                        	echo implode(" ", $blue_list);
                        ?>
                        </td>
                        <td>
                            <?php 
                                $beixuan_1 = explode(',',$bluexsh['beixuan_1']);
                                foreach ($beixuan_1 as $key => &$value) {
                                    if($value<10)
                                        $value = '0'.$value;
                                }
                                echo implode(" ", $beixuan_1);
                            ?>
                        </td>
                        <td>
                            <?php 
                                $beixuan_2 = explode(',',$bluexsh['beixuan_2']);
                                foreach ($beixuan_2 as $key => &$value) {
                                    if($value<10)
                                        $value = '0'.$value;
                                }
                                echo implode(" ", $beixuan_2);
                            ?>
                        </td>
                        <td>
                        <?php 
                            $beixuan_3 = explode(',',$bluexsh['beixuan_3']);
                            foreach ($beixuan_3 as $key => &$value) {
                                if($value<10)
                                    $value = '0'.$value;
                            }
                            echo implode(" ", $beixuan_3);
                        ?>
                        </td>
                        <td>
                            <?php 
                                $beixuan_4 = explode(',',$bluexsh['beixuan_4']);
                                foreach ($beixuan_4 as $key => &$value) {
                                    if($value<10)
                                        $value = '0'.$value;
                                }
                                echo implode(" ", $beixuan_4);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>
            <div class="center-block" id="chart2" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <div class="center-block" id="chart3" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <table class="table">
                <thead>
                    <tr>
                        <th>期数</th>
                        <th>蓝球</th>
                        <th>区间</th>
                        <th>心水集团号</th>
                        <th>备选1</th>
                        <th>备选2</th>
                        <th>备选3</th>
                        <th>备选4</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        
                        $sql = "SELECT * FROM `#@__caipiao_blue_xsh` ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }
                        $dopage->GetPage($sql, 10);
                        while($row = $dosql->GetArray()){
                    ?>
                    <tr>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><span class="blue_ball active"><?php echo $row['blue_num'] ?></span></td>
                        <td><?php echo $row['blue_range'] . ($row['blue_range_win'] == 1 ? '(命中)' : ''); ?></td>
                        <td><?php echo $row['blue_list'] . ($row['blue_list_win'] == 1 ? '(命中)' : ''); ?></td>
                        <td><?php echo $row['beixuan_1'] . ($row['beixuan_1_win'] == 1 ? '(命中)' : ''); ?></td>
                        <td><?php echo $row['beixuan_2'] . ($row['beixuan_2_win'] == 1 ? '(命中)' : ''); ?></td>
                        <td><?php echo $row['beixuan_3'] . ($row['beixuan_3_win'] == 1 ? '(命中)' : ''); ?></td>
                        <td><?php echo $row['beixuan_4'] . ($row['beixuan_4_win'] == 1 ? '(命中)' : ''); ?></td>
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
$suanfa['blue_range'] = array();
$suanfa['blue_list'] = array();

$suanfa['beixuan_1'] = array();
$suanfa['beixuan_2'] = array();
$suanfa['beixuan_3'] = array();
$suanfa['beixuan_4'] = array();
$suanfa['beixuan'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
if(isset($cp_dayid)){
    $dosql->Execute("SELECT * FROM `#@__caipiao_blue_xsh` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT $limit_num");
}else{
    $dosql->Execute("SELECT * FROM `#@__caipiao_blue_xsh` ORDER BY cp_dayid DESC LIMIT $limit_num");    
}
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
	$suanfa['date'][]       = $xdate;
	$suanfa['blue_range'][] = $row['blue_range_win'];
    $suanfa['blue_list'][]  = $row['blue_list_win'];

    $suanfa['beixuan_1'][]  = $row['beixuan_1_win'];
    $suanfa['beixuan_2'][]  = $row['beixuan_2_win'];
    $suanfa['beixuan_3'][]  = $row['beixuan_3_win'];
	$suanfa['beixuan_4'][]  = $row['beixuan_4_win'];
	
	$beixuan = 0;
	$row['beixuan_1_win'] == 1 && $beixuan = 1;
	$row['beixuan_2_win'] == 1 && $beixuan = 2;
	$row['beixuan_3_win'] == 1 && $beixuan = 3;
	$row['beixuan_4_win'] == 1 && $beixuan = 4;
	
	$suanfa['beixuan'][]  = $beixuan;
}

$suanfa['date']       = array_reverse($suanfa['date']);
$suanfa['blue_range'] = array_reverse($suanfa['blue_range']);
$suanfa['blue_list']  = array_reverse($suanfa['blue_list']);

$suanfa['beixuan_1']  = array_reverse($suanfa['beixuan_1']);
$suanfa['beixuan_2']  = array_reverse($suanfa['beixuan_2']);
$suanfa['beixuan_3']  = array_reverse($suanfa['beixuan_3']);
$suanfa['beixuan_4']  = array_reverse($suanfa['beixuan_4']);
$suanfa['beixuan']  = array_reverse($suanfa['beixuan']);

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
        var title = 'ddd';
        var xData = <?php echo json_encode($suanfa['date']) ?>;

        var yData = <?php echo json_encode($suanfa['blue_range']) ?>;
        var y2Data = <?php echo json_encode($suanfa['blue_list']) ?>;
        var myChart = ec.init(document.getElementById('chart1'));
        var myChart2 = ec.init(document.getElementById('chart2'));
        var myChart3 = ec.init(document.getElementById('chart3'));

        var yBX1Data = <?php echo json_encode($suanfa['beixuan_1']) ?>;
        var yBX2Data = <?php echo json_encode($suanfa['beixuan_2']) ?>;
        var yBX3Data = <?php echo json_encode($suanfa['beixuan_3']) ?>;
        var yBX4Data = <?php echo json_encode($suanfa['beixuan_4']) ?>;
        var yBXData = <?php echo json_encode($suanfa['beixuan']) ?>;
        
        var option = {
            title: {
                text: '五期蓝球定蓝与心水集团码',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['蓝球范围命中', '心水码命中'],
                selected: {  
                    '心水码命中': false
                }
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
                    name: '蓝球范围命中',
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
                },
                {
                    name: '心水码命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        // color:'red',
                        lineStyle:{
                            // color:'#f36'  
                        } 
                      }
                    },
                    data: y2Data
                }
            ]
        };
        myChart.setOption(option);
        
        var option2 = {
            title: {
                text: '蓝球备选区命中走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['备选1区命中', '备选2区命中', '备选3区命中', '备选4区命中'],
                selected: {  
                    '备选2区命中': false,  
                    '备选3区命中': false,  
                    '备选4区命中': false 
                }
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
                    name: '备选1区命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#FD0C0C',
                        lineStyle:{
                            color:'#FD0C0C'  
                        } 
                      }
                    },
                    data: yBX1Data
                },
                {
                    name: '备选2区命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#2F4554',
                        lineStyle:{
                            color:'#2F4554'  
                        } 
                      }
                    },
                    data: yBX2Data
                },
                {
                    name: '备选3区命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#A624AB',
                        lineStyle:{
                            color:'#A624AB'  
                        } 
                      }
                    },
                    data: yBX3Data
                },
                {
                    name: '备选4区命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#f36',
                        lineStyle:{
                            color:'#f36'  
                        } 
                      }
                    },
                    data: yBX4Data
                }
            ]
        };
        myChart2.setOption(option2);
        
        var option3 = {
            title: {
                text: '蓝球备选区命中走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['备选命中']
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
                min: 1,
                max: 4,
                name: 'y',
                splitNumber: 3
            },
            series: [
                {
                    name: '备选命中',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#FD0C0C',
                        lineStyle:{
                            color:'#FD0C0C'  
                        } 
                      }
                    },
                    data: yBXData
                }
            ]
        };
        myChart3.setOption(option3);
    }
);
</script>