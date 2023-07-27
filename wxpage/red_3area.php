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
        <div class="center-block" id="chart0" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
        <div class="center-block" id="chart1" style="height: 420px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

$arrs = array(
    3 => array(
      '1-11'   => array(1, 11),
      '12-22'   => array(12, 22),
      '23-33'  => array(23, 33),
    ),
);

$fenqu_cfg_3 = array_keys($arrs[3]);

//大小四区间
$suanfa = array();
$suanfa['date']   = array();
$suanfa['fenqu3'] = array();
$suanfa['fenqu3_5num'] = array();

$fenqu3_5num_max = 0;
$limit_num = isset($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_location_cross` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $suanfa['date'][] = $xdate;

    $fenqu3   = unserialize($row['fenqu3']);
    $fenqu3_5num   = unserialize($row['fenqu3_5num']);

    foreach ($fenqu3 as $key => $value) {
      if(!isset($suanfa['fenqu3'][$key])){
        $suanfa['fenqu3'][$key] = array();
      }
      $suanfa['fenqu3'][$key][] = $value;
    }

    foreach ($fenqu3_5num as $key => $value) {
      if(!isset($suanfa['fenqu3_5num'][$key])){
        $suanfa['fenqu3_5num'][$key] = array();
      }
      $suanfa['fenqu3_5num'][$key][] = $value;
      if($fenqu3_5num_max < $value) $fenqu3_5num_max = $value;
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
            var myChart1  = ec.init(document.getElementById('chart1'));
            var title0    = '3区间命中走势图';
            var title1    = '5期3区命中数走势图';

            var xData  = <?php echo json_encode($suanfa['date']) ?>;
            var yfq30Data = <?php echo json_encode($suanfa['fenqu3'][0]) ?>;
            var yfq31Data = <?php echo json_encode($suanfa['fenqu3'][1]) ?>;
            var yfq32Data = <?php echo json_encode($suanfa['fenqu3'][2]) ?>;

            var yfq350Data = <?php echo json_encode($suanfa['fenqu3_5num'][0]) ?>;
            var yfq351Data = <?php echo json_encode($suanfa['fenqu3_5num'][1]) ?>;
            var yfq352Data = <?php echo json_encode($suanfa['fenqu3_5num'][2]) ?>;

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

            var option1 = {
              title: {
                  text: title1,
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
                  max: <?php echo $fenqu3_5num_max ?>,
                  splitNumber:<?php echo $fenqu3_5num_max ?>,
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
                      data: yfq350Data
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
                      data: yfq351Data
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
                      data: yfq352Data
                  }]
            };
            myChart1.setOption(option1);
        }
    );
    </script>