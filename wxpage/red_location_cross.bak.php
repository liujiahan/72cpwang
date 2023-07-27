<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$redRoad3 = array();
for ($i=1; $i <=33 ; $i++) { 
  $i < 10 && $i = '0' . $i;
  $yushu = $i % 3;
  if(!isset($redRoad3[$yushu])){
    $redRoad3[$yushu] = array();
  }
  $redRoad3[$yushu][] = $i;
}
ksort($redRoad3);

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>区位交叉选号法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">

    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'red_location_cross'},
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
              <option value="">--请选择--</option>
              <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
                <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
              <?php } ?>
          </select>
          </div>
        </form>      
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">大小四区间</a>
        <?php } ?>

        <div class="clearfix"></div>
        <div class="center-block" id="chart" style="height: 400px;"></div>
        <div class="bs-example" data-example-id="contextual-table">
        <table class="table">
            <thead>
                <tr>
                    <th width="33%">除3余1</th>
                    <th width="33%">除3余2</th>
                    <th width="33%">除3余0</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <?php  
                      foreach ($redRoad3[1] as $tmp_red) {
                        echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                      }
                    ?>
                    </td>
                    <td>
                    <?php 
                      foreach ($redRoad3[2] as $tmp_red) {
                        echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                      }
                    ?>
                    </td>
                    <td>
                    <?php 
                      foreach ($redRoad3[0] as $tmp_red) {
                        echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                      }
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
        <div class="center-block" id="chart2" style="height: 300px;"></div>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

//大小四区间
$redLC = array();
$redLC['date']  = array();
$redLC['one']   = array();
$redLC['two']   = array();
$redLC['three'] = array();
$redLC['four']  = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_location_cross` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $redLC['date'][] = $xdate;
    
    $redLC['one'][]   = $row['location_one'];
    $redLC['two'][]   = $row['location_two'];
    $redLC['three'][] = $row['location_three'];
    $redLC['four'][]  = $row['location_four'];
}

$redLC['date']  = array_reverse($redLC['date']);
$redLC['one']   = array_reverse($redLC['one']);
$redLC['two']   = array_reverse($redLC['two']);
$redLC['three'] = array_reverse($redLC['three']);
$redLC['four']  = array_reverse($redLC['four']);

$red_road = array();
$red_road['date']  = array();
$red_road['road3'] = array();

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
}

$red_road['date']         = array_reverse($red_road['date']);
foreach ($red_road['road3'] as $yushu => &$yushuArr) {
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
            var title    = '红球大小四区间出球走势图';
            var title2   = '红球除3余2、1、0路出球走势';

            var xData  = <?php echo json_encode($redLC['date']) ?>;
            var y1Data = <?php echo json_encode($redLC['one']) ?>;
            var y2Data = <?php echo json_encode($redLC['two']) ?>;
            var y3Data = <?php echo json_encode($redLC['three']) ?>;
            var y4Data = <?php echo json_encode($redLC['four']) ?>;


            var r3y1Data = <?php echo json_encode($red_road['road3'][0]) ?>;
            var r3y2Data = <?php echo json_encode($red_road['road3'][1]) ?>;
            var r3y3Data = <?php echo json_encode($red_road['road3'][2]) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['大小区间1-8', '大小区间9-16', '大小区间17-24', '大小区间25-33']
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
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [{
                      name: '大小区间1-8',
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
                      data: y1Data
                  },{
                      name: '大小区间9-16',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'purple',
                          lineStyle:{
                              color:'purple'  
                          } 
                        }
                      },
                      data: y2Data
                  },{
                      name: '大小区间17-24',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'green',
                          lineStyle:{
                              color:'green'  
                          } 
                        }
                      },
                      data: y3Data
                  },{
                      name: '大小区间25-33',
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
                      data: y4Data
                  }]
            };

              var option2 = {
                title: {
                    text: title2,
                    left: 'center'
                },
                legend: {
                    left: 'left',
                    data: ['1路', '2路', '0路']
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
                        name: '1路',
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
                        data: r3y2Data
                    },{
                        name: '2路',
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
                        data: r3y3Data
                    },{
                        name: '0路',
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
                        data: r3y1Data
                    }]
            };
            myChart.setOption(option);
            myChart2.setOption(option2);
        }
    );
    </script>