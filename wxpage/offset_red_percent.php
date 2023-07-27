<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

$redSpace = array();

foreach (array(3, 4, 5, 6, 7, 8, 9, 10, 11) as $qujian) {
    if(!isset($redSpace[$qujian])){
        $redSpace[$qujian] = array();
    }
    $pg = ceil(33 / $qujian);
    for ($i=0; $i < $pg; $i++) {
        $left  = $i * $qujian + 1;
        $right = ($i+1) * $qujian > 33 ? 33 : ($i+1) * $qujian;
        $redSpace[$qujian][] = array($left, $right);
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>[精选热]百分比预测法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?limit_num=" + $("#limit_num").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_offset_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'offset_red_percent'
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
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">百分比预测法</a>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="center-block" id="chart" style="height: 300px;"></div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10%">期数</th>
                            <th width="25%">开奖号</th>
                            <th width="10%">推测红球个数</th>
                            <th width="30%">百分比预测红球</th>
                            <th width="10%">命中个数</th>
                            <th width="15%">命中红球</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $maxid = 0;
                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 10");
                            while($row = $dosql->GetArray()){
                                $redBalls10[] = explode(',', $row['red_num']);
                                if($maxid==0){
                                    $maxid = $row['cp_dayid'];
                                }
                            }
                            $redBalls05 = array_slice($redBalls10, 0, 5);

                            $reds10 = array();
                            $reds05 = array();
                            foreach ($redBalls10 as $reds) {
                                foreach ($reds as $red) {
                                    if(!isset($reds10[$red])){
                                        $reds10[$red] = 0;
                                    }
                                    $reds10[$red]++;
                                }
                            }

                            foreach ($redBalls05 as $reds) {
                                foreach ($reds as $red) {
                                    if(!isset($reds05[$red])){
                                        $reds05[$red] = 0;
                                    }
                                    $reds05[$red]++;
                                }
                            }

                            $tuice_reds = array();
                            foreach ($reds10 as $red => $num) {
                                if($num < 2){
                                    continue;
                                }
                                if(isset($reds05[$red]) && $reds05[$red] > 0){
                                    $tuice_reds[] = $red;
                                }
                            }
                            sort($tuice_reds);
                        ?>
                        <tr>
                            <td><?php echo nextCpDayId($maxid) ?></td>
                            <td></td>
                            <td><?php echo count($tuice_reds) ?></td>
                            <td>
                                <?php foreach ($tuice_reds as $tmp_red) { ?>
                                    <button class="btn btn-primary" type="button"><?php echo $tmp_red; ?></span></button>
                                <?php } ?>
                            </td>
                            <td>
                                <!-- <button class="btn btn-info" type="button"><?php echo $row['win_num']; ?></span></button> -->
                            </td>
                            <td>
                            </td>
                        </tr>  
                        <?php


                            $sql = "SELECT * FROM `#@__caipiao_red_percent` ORDER BY cp_dayid DESC";
                            if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                                $sql .= " LIMIT {$no_admin_limit}";
                            }

                            $dopage->GetPage($sql,10);
                            while($row = $dosql->GetArray()){
                                $tuice_reds = explode(',', $row['tuice_reds']);
                                $red_num = explode(',', $row['red_num']);

                        ?>
                        <tr>
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td>
                                <?php foreach ($red_num as $tmp_red) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
                                <?php } ?>
                            </td>
                            <td><?php echo count($tuice_reds) ?></td>
                            <td>
                                <?php foreach ($tuice_reds as $tmp_red) { ?>
                                    <button class="btn btn-primary" type="button"><?php echo $tmp_red; ?></span></button>
                                <?php } ?>
                            </td>
                            <td>
                                <button class="btn btn-info" type="button"><?php echo $row['win_num']; ?></span></button>
                            </td>
                            <td>
                                <?php foreach ($tuice_reds as $tmp_red) { ?>
	                                <?php if (in_array($tmp_red, $red_num)) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
	                                <?php } ?>
                                <?php } ?>
                            </td>
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

$redPercent = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
if(isset($cp_dayid) && !empty($cp_dayid)){
    $dosql->Execute("SELECT * FROM `#@__caipiao_red_percent` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT $limit_num");
}else{
    $dosql->Execute("SELECT * FROM `#@__caipiao_red_percent` ORDER BY cp_dayid DESC LIMIT $limit_num");
}
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    $redPercent['date'][] = $xdate;
    $redPercent['win_num'][] = $row['win_num'];
}

$redPercent['date'] = array_reverse($redPercent['date']);
$redPercent['win_num'] = array_reverse($redPercent['win_num']);

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
            var myChart  = ec.init(document.getElementById('chart'));
            var title    = '百分比预测红球命中个数走势图';

            var xData  = <?php echo json_encode($redPercent['date']) ?>;
            var y1Data = <?php echo json_encode($redPercent['win_num']) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
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
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [{
                      name: title,
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
                  }]
            };
            myChart.setOption(option);
        }
    );
    </script>