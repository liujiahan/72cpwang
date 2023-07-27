<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();


?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>频率趋势选号法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
    	var cur_red = '';
    	if($('#cur_red').val() != ''){
    		cur_red = $('#cur_red').val();
    	}
    	if($('#cur_red2').val() != ''){
    		cur_red = $('#cur_red2').val();
    	}
    	if($('#cur_red3').val() != ''){
    		cur_red = $('#cur_red3').val();
    	}
        window.location.href = "?limit_num="+$("#limit_num").val()+"&cur_red="+cur_red;
    }
    function gourl(){
        window.location.href = "?cur_red="+$("#cur_red").val()+"&limit_num="+$("#limit_num").val();
    }
    function gourl2(){
        window.location.href = "?cur_red="+$("#cur_red2").val()+"&limit_num="+$("#limit_num").val();
    }
    function gourl3(){
        window.location.href = "?cur_red="+$("#cur_red3").val()+"&limit_num="+$("#limit_num").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'red_pinlv_trend'},
                success: function(data){
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
            <!-- <option value="">--请选择--</option> -->
            <?php 
            $limit_num = isset($limit_num) ? $limit_num : 50;
            foreach (getSelArr() as $daynum => $daytxt) { ?>
              <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
            <?php } ?>
        </select>
        </div>
        <div class="form-group">
        <label for="exampleInputEmail2">33红球</label>
        <select class="form-control" name="cur_red" id="cur_red" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 1){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>
        <select class="form-control" name="cur_red2" id="cur_red2" onchange="gourl2()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 2){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>
        <select class="form-control" name="cur_red3" id="cur_red3" onchange="gourl3()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 0){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>
        </div>
        <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form>        
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">频率趋势选号法</a>
        <?php } ?>

        <div class="clearfix"></div>
        <div class="center-block" id="chart" style="height: 450px;"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

$redPLTrend = array();
$redPLTrend['before_5']  = array();
$redPLTrend['before_10'] = array();
$redPLTrend['before_25'] = array();
$redPLTrend['before_50'] = array();

// $cur_red = '01';
$cur_red = isset($cur_red) ? $cur_red : '01';

$redPLTrend[$cur_red] = 0;

$limit_num = isset($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_pinlv_trend` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $redPLTrend['date'][] = $xdate;

    $before5_pinlv  = unserialize($row['before5_pinlv']);
    $before10_pinlv = unserialize($row['before10_pinlv']);
    $before25_pinlv = unserialize($row['before25_pinlv']);
    $before50_pinlv = unserialize($row['before50_pinlv']);

    $redPLTrend['before_5'][]  = $before5_pinlv[$cur_red];
    $redPLTrend['before_10'][] = $before10_pinlv[$cur_red];
    $redPLTrend['before_25'][] = $before25_pinlv[$cur_red];
    $redPLTrend['before_50'][] = $before50_pinlv[$cur_red];
    if($before5_pinlv[$cur_red] > $redPLTrend[$cur_red]){
        $redPLTrend[$cur_red] = $before5_pinlv[$cur_red];
    }
    if($before10_pinlv[$cur_red] > $redPLTrend[$cur_red]){
        $redPLTrend[$cur_red] = $before10_pinlv[$cur_red];
    }
    if($before25_pinlv[$cur_red] > $redPLTrend[$cur_red]){
        $redPLTrend[$cur_red] = $before25_pinlv[$cur_red];
    }
    if($before50_pinlv[$cur_red] > $redPLTrend[$cur_red]){
        $redPLTrend[$cur_red] = $before50_pinlv[$cur_red];
    }
}

$redPLTrend['date']      = array_reverse($redPLTrend['date']);
$redPLTrend['before_5']  = array_reverse($redPLTrend['before_5']);
$redPLTrend['before_10'] = array_reverse($redPLTrend['before_10']);
$redPLTrend['before_25'] = array_reverse($redPLTrend['before_25']);
$redPLTrend['before_50'] = array_reverse($redPLTrend['before_50']);

?>
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
            var myChart  = ec.init(document.getElementById('chart'));
            var title    = '红球<?php echo $cur_red ?>前5期，10期，25期，50期出球频率';

            var xData  = <?php echo json_encode($redPLTrend['date']) ?>;
            var y1Data = <?php echo json_encode($redPLTrend['before_5']) ?>;
            var y2Data = <?php echo json_encode($redPLTrend['before_10']) ?>;
            var y3Data = <?php echo json_encode($redPLTrend['before_25']) ?>;
            var y4Data = <?php echo json_encode($redPLTrend['before_50']) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['前5期', '前10期', '前25期', '前50期'],
                  selected: {  
                      '前50期': false,
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
                  type: 'log',
                  min: 0,
                  max: <?php echo $redPLTrend[$cur_red] ?>,
                  splitNumber:5,
                  name: 'y'
              },
              series: [{
                      name: '前5期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          // color:'red',
                          // lineStyle:{
                          //     color:'yellow'  
                          // } 
                        }
                      },
                      data: y1Data
                  },{
                      name: '前10期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          // color:'purple',
                          // lineStyle:{
                          //     color:'purple'  
                          // } 
                        }
                      },
                      data: y2Data
                  },{
                      name: '前25期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          // color:'blue',
                          // lineStyle:{
                          //     color:'green'  
                          // } 
                        }
                      },
                      data: y3Data
                  },{
                      name: '前50期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          // color:'orange',
                          // lineStyle:{
                          //     color:'#f36'  
                          // } 
                        }
                      },
                      data: y4Data
                  }]
            };
            myChart.setOption(option);
        }
    );
    </script>