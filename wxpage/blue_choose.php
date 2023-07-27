<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>蓝球正主选号 - <?php echo $cfg_seotitle; ?></title>
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
        $("#doData").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'ajax/blue_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'blue_choose'
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

        <!-- <h1><small>蓝球16数除4余数归纳</small></h1>
        <blockquote>
            <p>除四余0号：04 08 12 16&nbsp;&nbsp;&nbsp;&nbsp;除四余1号：01 05 09 13&nbsp;&nbsp;&nbsp;&nbsp;除四余2号：02 06 10 14&nbsp;&nbsp;&nbsp;&nbsp;除四余3号：03 07 11 15</p>
            <p>除三余0号：03 06 09 12 15&nbsp;&nbsp;&nbsp;&nbsp;除三余1号：01 04 07 10 13 16&nbsp;&nbsp;&nbsp;&nbsp;除三余2号：02 05 08 11 14</p>
        </blockquote> -->
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
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">蓝球正主选号推测蓝号</a>
        <?php } ?>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期号</th>
                        <th width="10%">开奖号码</th>
                        <th width="20%">正选号</th>
                        <th width="20%">正选号命中</th>
                        <th width="20%">主选号</th>
                        <th width="20%">主选号命中</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        $allBlue = array();
                        for ($i=1; $i < 16; $i++) { 
                            $i<10 && $i = '0' . $i;
                            $allBlue[] = $i;
                        }

                        $next1 = $dosql->GetOne("SELECT * FROM `#@__caipiao_blue_choose` ORDER BY cp_dayid DESC LIMIT 1");
                    	$blue1 = $next1['blue_num'];

                        $next2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_blue_choose` WHERE cp_dayid<{$next1['cp_dayid']} ORDER BY cp_dayid DESC LIMIT 1");
                    	$blue2 = $next2['blue_num'];

                        $next3 = $dosql->GetOne("SELECT * FROM `#@__caipiao_blue_choose` WHERE cp_dayid<{$next2['cp_dayid']} ORDER BY cp_dayid DESC LIMIT 1");
                    	$blue3 = $next3['blue_num'];

                    	$choose = plusminusGetBlue($blue1, $blue2, $blue3);
                    ?>
                    <tr class="default">
                        <td><?php echo nextCpDayId($next1['cp_dayid']) ?></td>
                        <td><?php //echo $row['opencode'] ?></td>
                        <td><?php echo implode(',', $choose['first_choose']) ?></td>
                        <td><?php //echo $row['first_win'] == 1 ? '命中' : '未命中' ?></td>
                        <!-- <td><?php echo $row['second_choose'] ?></td> -->
                        <td><?php echo implode(',', $choose['second_choose']) ?></td>
                        <td><?php //echo $row['second_win'] == 1 ? '命中' : '未命中' ?></td>
                    </tr>
                </tbody>
            </table>

	        <div class="clearfix"></div>
	        <div class="center-block" id="chart" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
	        <div class="center-block" id="chart2" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期号</th>
                        <th width="10%">开奖号码</th>
                        <th width="20%">正选号</th>
                        <th width="20%">正选号命中</th>
                        <th width="20%">主选号</th>
                        <th width="20%">主选号命中</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        $allBlue = array();
                        for ($i=1; $i < 16; $i++) { 
                            $i<10 && $i = '0' . $i;
                            $allBlue[] = $i;
                        }

                        $sql = "SELECT * FROM `#@__caipiao_blue_choose` ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }

                        $dopage->GetPage($sql);
                        while($row = $dosql->GetArray()){
                    ?>
                    <tr class="default">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['first_choose'] ?></td>
                        <!-- <td><?php echo implode(', ', $row['first_choose']) ?></td> -->
                        <td><?php echo $row['first_win'] == 1 ? '命中' : '未命中' ?></td>
                        <td><?php echo $row['second_choose'] ?></td>
                        <!-- <td><?php echo implode(', ', $row['second_choose']) ?></td> -->
                        <td><?php echo $row['second_win'] == 1 ? '命中' : '未命中' ?></td>
                        <!-- <td>
                        <?php
                            foreach (array_diff($allBlue, array_keys($bluedata['list'])) as $tmp_blue) {
                                if($tmp_blue == $bluedata['blue_num']){
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_blue . ' <span class="badge">' . $num.'</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-info" type="button">'.$tmp_blue . ' <span class="badge">' . $num.'</span></button>';
                                }
                            }
                        ?> 
                        </td> -->
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

$blue_choose = array();
$blue_choose['date']    = array();
$blue_choose['first_win'] = array();
$blue_choose['second_win'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_blue_choose` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $blue_choose['date'][]       = $xdate;
    $blue_choose['first_win'][]  = $row['first_win'];
    $blue_choose['second_win'][] = $row['second_win'];
}

$blue_choose['date']       = array_reverse($blue_choose['date']);
$blue_choose['first_win']  = array_reverse($blue_choose['first_win']);
$blue_choose['second_win'] = array_reverse($blue_choose['second_win']);

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
            var myChart = ec.init(document.getElementById('chart'));
            var myChart2 = ec.init(document.getElementById('chart2'));
            var title = '正选号命中走势图';
            var title2 = '主选号命中走势图';
            var xData = <?php echo json_encode($blue_choose['date']) ?>;
            var yData = <?php echo json_encode($blue_choose['first_win']) ?>;
            var yData2 = <?php echo json_encode($blue_choose['second_win']) ?>;

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
                  type: 'log',
                  min: 0,
                  max: 1,
                  splitNumber:1,
                  name: 'y'
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
                  max: 1,
                  splitNumber:1,
                  name: 'y'
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
                      data: yData2
                  }
              ]
          };
            myChart.setOption(option);
            myChart2.setOption(option2);
        }
    );
    </script>