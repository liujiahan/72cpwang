<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

$redRoad3 = array();
$redRoad6 = array();
for ($i=1; $i <=33 ; $i++) { 
  $yushu = $i % 3;
  if(!isset($redRoad3[$yushu])){
    $redRoad3[$yushu] = array();
  }
  $redRoad3[$yushu][] = $i;
  $yushu = $i % 6;
  if(!isset($redRoad6[$yushu])){
    $redRoad6[$yushu] = array();
  }
  $redRoad6[$yushu][] = $i;
}
ksort($redRoad3);
ksort($redRoad6);

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球除3除6路走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        // window.location.href = "?cp_dayid="+$("#cp_dayid").val()+"&year_num="+$("#year_num").val();
        window.location.href = "?limit_num="+$("#limit_num").val();
    }

    $(document).ready(function() {
        $("#doData").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'red_road'
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

        <h1>红球3路数字<small></small></h1>
        <blockquote>
            <?php foreach ($redRoad3 as $yushu => $yushuArr) {?>
            <p>除3余<?php echo $yushu ?>号：<?php echo implode(' ', $yushuArr) ?></p>
            <?php } ?>
        </blockquote>
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
        <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form> 
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">红球除3除6</a>
        <?php } ?>
        <!-- <div class="clearfix"></div> -->
        <div class="center-block" id="chart" style="height: 400px;"></div>
        
        <h1>红球6路数字<small></small></h1>
        <blockquote>
            <?php foreach ($redRoad6 as $yushu => $yushuArr) {?>
            <p>除6余<?php echo $yushu ?>号：<?php echo implode(' ', $yushuArr) ?></p>
            <?php } ?>
        </blockquote>

        
        <div class="center-block" id="chart2" style="height: 400px;"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

$red_road = array();
$red_road['date']  = array();
$red_road['road3'] = array();
$red_road['road6'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
// $dosql->Execute("SELECT * FROM `#@__caipiao_red_road` WHERE cp_dayid>=2017001 ORDER BY cp_dayid DESC LIMIT $limit_num");
$dosql->Execute("SELECT * FROM `#@__caipiao_red_road` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
  $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
  if($xdate == 1){
    $xdate = substr($row['cp_dayid'], 0, 4);
  } 
  $red_road['date'][] = $xdate;
  $row['red_road3'] = unserialize($row['red_road3']);
  foreach ($row['red_road3'] as $yushu => $yushu_num) {
    if(!isset($red_road['road3'][$yushu])){
      $red_road['road3'][$yushu] = array();
    }
    $red_road['road3'][$yushu][] = $yushu_num;
  }
  
  $row['red_road6'] = unserialize($row['red_road6']);
  foreach ($row['red_road6'] as $yushu => $yushu_num) {
    if(!isset($red_road['road6'][$yushu])){
      $red_road['road6'][$yushu] = array();
    }
    $red_road['road6'][$yushu][] = $yushu_num;
  }
}

$red_road['date']         = array_reverse($red_road['date']);
foreach ($red_road['road3'] as $yushu => &$yushuArr) {
  $yushuArr = array_reverse($yushuArr);
}
foreach ($red_road['road6'] as $yushu => &$yushuArr) {
  $yushuArr = array_reverse($yushuArr);
}

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
            var myChart2 = ec.init(document.getElementById('chart2'));
            var title    = '红球除3路走势图';
            var title2   = '红球除6路走势图';

            var xData  = <?php echo json_encode($red_road['date']) ?>;
            var y1Data = <?php echo json_encode($red_road['road3'][0]) ?>;
            var y2Data = <?php echo json_encode($red_road['road3'][1]) ?>;
            var y3Data = <?php echo json_encode($red_road['road3'][2]) ?>;

            var y6_1Data = <?php echo json_encode($red_road['road6'][0]) ?>;
            var y6_2Data = <?php echo json_encode($red_road['road6'][1]) ?>;
            var y6_3Data = <?php echo json_encode($red_road['road6'][2]) ?>;
            var y6_4Data = <?php echo json_encode($red_road['road6'][3]) ?>;
            var y6_5Data = <?php echo json_encode($red_road['road6'][4]) ?>;
            var y6_6Data = <?php echo json_encode($red_road['road6'][5]) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['0路', '1路', '2路']
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
                  max: 5,
                  splitNumber:5,
                  name: 'y'
              },
              series: [{
                      name: '0路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'red',
                          lineStyle:{
                              color:'red'  
                          } 
                        }
                      },
                      data: y1Data
                  },{
                      name: '1路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'orange',
                          lineStyle:{
                              color:'orange'  
                          } 
                        }
                      },
                      data: y2Data
                  },{
                      name: '2路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'violet',
                          lineStyle:{
                              color:'violet'  
                          } 
                        }
                      },
                      data: y3Data
                  }]
          };

           var option2 = {
              title: {
                  text: title2,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['0路', '1路', '2路', '3路', '4路', '5路']
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
                  max: 4,
                  splitNumber:4,
                  name: 'y'
              },
              series: [{
                      name: '0路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'#c00',
                          lineStyle:{
                              color:'#c00'  
                          } 
                        }
                      },
                      data: y6_1Data
                  },{
                      name: '1路',
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
                      data: y6_2Data
                  },{
                      name: '2路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'#f09',
                          lineStyle:{
                              color:'#f09'  
                          } 
                        }
                      },
                      data: y6_3Data
                  },{
                      name: '3路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'indigo',
                          lineStyle:{
                              color:'indigo'  
                          } 
                        }
                      },
                      data: y6_4Data
                  },{
                      name: '4路',
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
                      data: y6_5Data
                  },{
                      name: '5路',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'orange',
                          lineStyle:{
                              color:'orange'  
                          } 
                        }
                      },
                      data: y6_6Data
                  }]
          };
            myChart.setOption(option);
            myChart2.setOption(option2);
        }
    );
    </script>