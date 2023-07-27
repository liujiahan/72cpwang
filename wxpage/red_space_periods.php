<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$redRoad3 = array();
$redRoad6 = array();
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
    <title>间隔期数选号法 - <?php echo $cfg_seotitle; ?></title>
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
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'red_space_periods'},
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
            <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form>
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">间隔期数选号法</a>
        <?php } ?>      
        <div class="clearfix"></div>
        <div class="center-block" id="chart" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
        
        <div class="bs-example" data-example-id="contextual-table">
        <table class="table">
            <thead>
                <tr>
                    <th width="10%">期数</th>
                    <th width="15%">开奖号</th>
                    <th width="10%">中出个数</th>
                    <th width="65%">遗漏排序中奖</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $sql = "SELECT * FROM `#@__caipiao_red_space_periods` ORDER BY cp_dayid DESC";
                    if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                        $sql .= " LIMIT {$no_admin_limit}";
                    }
                    $dopage->GetPage($sql);
                    $i = 0;
                    while($row = $dosql->GetArray()){
                        $i++;
                ?>
                <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                    
                    <td><?php echo $row['cp_dayid'] ?></td>
                    <td><?php echo $row['opencode'] ?></td>
                    <?php 
                        $miss_win_num  = unserialize($row['miss_win_num']);
                        $red_miss_sort = unserialize($row['red_miss_sort']);
                    ?>
                    <td>
                    <?php 
                    // echo array_sum($miss_win_num); 
                    echo '<button class="btn btn-primary" type="button">'.array_sum($miss_win_num).'</button>';
                    ?></td>
                    <td>
                    <?php 
                    foreach ($red_miss_sort as $miss_num => $sort) {
                        echo '<button class="btn btn-primary btn-sm" type="button">间隔'.$miss_num.'</button>';
                        echo '<button class="btn btn-info btn-sm" type="button">'.$sort.'球</button>';
                        echo '<button class="btn btn-danger btn-sm" type="button">'.$miss_win_num[$miss_num].'中</button>';
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    ?></td>
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

$space_periods = array();
$space_periods['date']   = array();
$space_periods['win']    = array();
$space_periods['miss_0'] = array();
$space_periods['miss_1'] = array();
$space_periods['miss_2'] = array();
$space_periods['miss_3'] = array();
$space_periods['miss_4'] = array();
$space_periods['miss_5'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_red_space_periods` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $miss_win_num = unserialize($row['miss_win_num']);
    $win_sum      = array_sum($miss_win_num);

    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $space_periods['date'][] = $xdate;

    $space_periods['miss_0'][] = isset($miss_win_num[0]) ? $miss_win_num[0] : 0;
    $space_periods['miss_1'][] = isset($miss_win_num[1]) ? $miss_win_num[1] : 0;
    $space_periods['miss_2'][] = isset($miss_win_num[2]) ? $miss_win_num[2] : 0;
    $space_periods['miss_3'][] = isset($miss_win_num[3]) ? $miss_win_num[3] : 0;
    $space_periods['miss_4'][] = isset($miss_win_num[4]) ? $miss_win_num[4] : 0;
    $space_periods['miss_5'][] = $win_sum < 6 ? 6-$win_sum : 0;

    $space_periods['win'][] = $win_sum;
}

foreach ($space_periods as $key => &$value) {
  $value = array_reverse($value);
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
            var myChart = ec.init(document.getElementById('chart'));
            var title   = '热号各遗漏命中走势图';

            var xData  = <?php echo json_encode($space_periods['date']) ?>;
            var y1Data = <?php echo json_encode($space_periods['win']) ?>;

            var missy0Data = <?php echo json_encode($space_periods['miss_0']) ?>;
            var missy1Data = <?php echo json_encode($space_periods['miss_1']) ?>;
            var missy2Data = <?php echo json_encode($space_periods['miss_2']) ?>;
            var missy3Data = <?php echo json_encode($space_periods['miss_3']) ?>;
            var missy4Data = <?php echo json_encode($space_periods['miss_4']) ?>;
            var missy5Data = <?php echo json_encode($space_periods['miss_5']) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['重号', '遗漏1期', '遗漏2期', '遗漏3期', '遗漏4期', '遗漏5期及以上'],
                  selected: {  
                      '遗漏1期': false,
                      '遗漏2期': false,
                      '遗漏3期': false,
                      '遗漏4期': false,
                      '遗漏5期及以上': false,
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
                      name: '重号',
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
                      data: missy0Data
                  },{
                      name: '遗漏1期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: missy1Data
                  },{
                      name: '遗漏2期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: missy2Data
                  },{
                      name: '遗漏3期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: missy3Data
                  },{
                      name: '遗漏4期',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: missy4Data
                  },{
                      name: '遗漏5期及以上',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                        }
                      },
                      data: missy5Data
                  }]
            };
            myChart.setOption(option);
        }
    );
    </script>