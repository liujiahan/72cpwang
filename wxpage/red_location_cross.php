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
    <title>区间命中选号法 - <?php echo $cfg_seotitle; ?></title>
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
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">统计计算</a>
        <?php } ?>

        <div class="clearfix"></div>
        <div class="center-block" id="chart0" style="height: 300px;"></div>
        <div class="center-block" id="chart2" style="height: 300px;"></div>
        <div class="center-block" id="chart" style="height: 300px;"></div>
        <div class="center-block" id="chart3" style="height: 300px;"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

$arrs = array(
  8 => array(
    '1-8'   => array(1, 8),
    '9-16'  => array(9, 16),
    '17-24' => array(17, 24),
    '25-33' => array(25, 33),
  ),
  5 => array(
    '1-5'   => array(1, 5),
    '6-10'  => array(6, 10),
    '11-15' => array(11, 15),
    '16-20' => array(16, 20),
    '21-25' => array(21, 25),
    '25-30' => array(25, 30),
    '31-33' => array(31, 33),
  ),
  4 => array(
    '1-4'   => array(1, 4),
    '5-8'   => array(5, 8),
    '9-12'  => array(9, 12),
    '13-16' => array(13, 16),
    '17-20' => array(17, 20),
    '21-24' => array(21, 24),
    '25-28' => array(25, 28),
    '29-33' => array(29, 33),
  ),
    3 => array(
      '1-11'   => array(1, 11),
      '12-22'   => array(12, 22),
      '23-33'  => array(23, 33),
    ),
);

$fenqu_cfg_8 = array_keys($arrs[8]);
$fenqu_cfg_5 = array_keys($arrs[5]);
$fenqu_cfg_4 = array_keys($arrs[4]);
$fenqu_cfg_3 = array_keys($arrs[3]);

//大小四区间
$suanfa = array();
$suanfa['date']   = array();
$suanfa['fenqu8'] = array();
$suanfa['fenqu5'] = array();
$suanfa['fenqu4'] = array();
$suanfa['fenqu3'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_location_cross` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $suanfa['date'][] = $xdate;
    
    $fenqu8   = unserialize($row['fenqu8']);
    $fenqu5   = unserialize($row['fenqu5']);
    $fenqu4   = unserialize($row['fenqu4']);
    $fenqu3   = unserialize($row['fenqu3']);

    foreach ($fenqu8 as $key => $value) {
      if(!isset($suanfa['fenqu8'][$key])){
        $suanfa['fenqu8'][$key] = array();
      }
      $suanfa['fenqu8'][$key][] = $value;
    }

    foreach ($fenqu5 as $key => $value) {
      if(!isset($suanfa['fenqu5'][$key])){
        $suanfa['fenqu5'][$key] = array();
      }
      $suanfa['fenqu5'][$key][] = $value;
    }

    foreach ($fenqu4 as $key => $value) {
      if(!isset($suanfa['fenqu4'][$key])){
        $suanfa['fenqu4'][$key] = array();
      }
      $suanfa['fenqu4'][$key][] = $value;
    }

    foreach ($fenqu3 as $key => $value) {
      if(!isset($suanfa['fenqu3'][$key])){
        $suanfa['fenqu3'][$key] = array();
      }
      $suanfa['fenqu3'][$key][] = $value;
    }
}

$suanfa['date']  = array_reverse($suanfa['date']);
foreach ($suanfa as $fenqu => &$fenquV) {
  if($fenqu == 'date')continue;
  foreach ($fenquV as $key => &$value) {
    $value = array_reverse($value);
  }
  $fenquV = array_values($fenquV);
}

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
            var myChart0  = ec.init(document.getElementById('chart0'));
            var myChart  = ec.init(document.getElementById('chart'));
            var myChart2 = ec.init(document.getElementById('chart2'));
            var myChart3 = ec.init(document.getElementById('chart3'));
            var title0    = '3区间命中走势图';
            var title    = '4区间命中走势图';
            var title2   = '6区间命中走势图';
            var title3   = '8区间命中走势图';

            var xData  = <?php echo json_encode($suanfa['date']) ?>;
            var yfq30Data = <?php echo json_encode($suanfa['fenqu3'][0]) ?>;
            var yfq31Data = <?php echo json_encode($suanfa['fenqu3'][1]) ?>;
            var yfq32Data = <?php echo json_encode($suanfa['fenqu3'][2]) ?>;

            var yfq80Data = <?php echo json_encode($suanfa['fenqu8'][0]) ?>;
            var yfq81Data = <?php echo json_encode($suanfa['fenqu8'][1]) ?>;
            var yfq82Data = <?php echo json_encode($suanfa['fenqu8'][2]) ?>;
            var yfq83Data = <?php echo json_encode($suanfa['fenqu8'][3]) ?>;

            var yfq50Data = <?php echo json_encode($suanfa['fenqu5'][0]) ?>;
            var yfq51Data = <?php echo json_encode($suanfa['fenqu5'][1]) ?>;
            var yfq52Data = <?php echo json_encode($suanfa['fenqu5'][2]) ?>;
            var yfq53Data = <?php echo json_encode($suanfa['fenqu5'][3]) ?>;
            var yfq54Data = <?php echo json_encode($suanfa['fenqu5'][4]) ?>;
            var yfq55Data = <?php echo json_encode($suanfa['fenqu5'][5]) ?>;
            var yfq56Data = <?php echo json_encode($suanfa['fenqu5'][6]) ?>;

            var yfq40Data = <?php echo json_encode($suanfa['fenqu4'][0]) ?>;
            var yfq41Data = <?php echo json_encode($suanfa['fenqu4'][1]) ?>;
            var yfq42Data = <?php echo json_encode($suanfa['fenqu4'][2]) ?>;
            var yfq43Data = <?php echo json_encode($suanfa['fenqu4'][3]) ?>;
            var yfq44Data = <?php echo json_encode($suanfa['fenqu4'][4]) ?>;
            var yfq45Data = <?php echo json_encode($suanfa['fenqu4'][5]) ?>;
            var yfq46Data = <?php echo json_encode($suanfa['fenqu4'][6]) ?>;
            var yfq47Data = <?php echo json_encode($suanfa['fenqu4'][7]) ?>;

            var option0 = {
              title: {
                  text: title0,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: <?php echo json_encode($fenqu_cfg_3) ?>,
                  selected: {  
                      '<?php echo $fenqu_cfg_3[1] ?>': false,
                      '<?php echo $fenqu_cfg_3[2] ?>': false,
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
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [{
                      name: '<?php echo $fenqu_cfg_3[0] ?>',
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
                      data: yfq30Data
                  },{
                      name: '<?php echo $fenqu_cfg_3[1] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'#E89963',
                          lineStyle:{
                              color:'#E89963'  
                          } 
                        }
                      },
                      data: yfq31Data
                  },{
                      name: '<?php echo $fenqu_cfg_3[2] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'#3DAAFE',
                          lineStyle:{
                              color:'#3DAAFE'  
                          } 
                        }
                      },
                      data: yfq32Data
                  }]
            };
            myChart0.setOption(option0);

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: <?php echo json_encode($fenqu_cfg_8) ?>,
                  selected: {  
                      '<?php echo $fenqu_cfg_8[1] ?>': false,
                      '<?php echo $fenqu_cfg_8[2] ?>': false,
                      '<?php echo $fenqu_cfg_8[3] ?>': false,
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
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [{
                      name: '<?php echo $fenqu_cfg_8[0] ?>',
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
                      data: yfq80Data
                  },{
                      name: '<?php echo $fenqu_cfg_8[1] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq81Data
                  },{
                      name: '<?php echo $fenqu_cfg_8[2] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq82Data
                  },{
                      name: '<?php echo $fenqu_cfg_8[3] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq83Data
                  }]
            };
            myChart.setOption(option);

            var option2 = {
              title: {
                  text: title2,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: <?php echo json_encode($fenqu_cfg_5) ?>,
                  selected: {  
                      '<?php echo $fenqu_cfg_5[1] ?>': false,
                      '<?php echo $fenqu_cfg_5[2] ?>': false,
                      '<?php echo $fenqu_cfg_5[3] ?>': false,
                      '<?php echo $fenqu_cfg_5[4] ?>': false,
                      '<?php echo $fenqu_cfg_5[5] ?>': false,
                      '<?php echo $fenqu_cfg_5[6] ?>': false,
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
                  max: 5,
                  splitNumber:5,
                  name: 'y'
              },
              series: [{
                      name: '<?php echo $fenqu_cfg_5[0] ?>',
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
                      data: yfq50Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[1] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq51Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[2] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq52Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[3] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq53Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[4] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq54Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[5] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq55Data
                  },{
                      name: '<?php echo $fenqu_cfg_5[6] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq56Data
                  }]
            };
            myChart2.setOption(option2);

            var option3 = {
              title: {
                  text: title3,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: <?php echo json_encode($fenqu_cfg_4) ?>,
                  selected: {  
                      '<?php echo $fenqu_cfg_4[1] ?>': false,
                      '<?php echo $fenqu_cfg_4[2] ?>': false,
                      '<?php echo $fenqu_cfg_4[3] ?>': false,
                      '<?php echo $fenqu_cfg_4[4] ?>': false,
                      '<?php echo $fenqu_cfg_4[5] ?>': false,
                      '<?php echo $fenqu_cfg_4[6] ?>': false,
                      '<?php echo $fenqu_cfg_4[7] ?>': false,
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
                  max: 4,
                  splitNumber:4,
                  name: 'y'
              },
              series: [{
                      name: '<?php echo $fenqu_cfg_4[0] ?>',
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
                      data: yfq40Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[1] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq41Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[2] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq42Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[3] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq43Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[4] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq44Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[5] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq45Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[6] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq46Data
                  },{
                      name: '<?php echo $fenqu_cfg_4[7] ?>',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: yfq47Data
                  }]
            };
            myChart3.setOption(option3);
        }
    );
    </script>