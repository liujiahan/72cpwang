<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$allRed = array();
for ($i=1; $i < 34; $i++) { 
    $i<10 && $i = '0' . $i;
    $allRed[] = $i;
}
$allBlue = array();
for ($i=1; $i < 17; $i++) { 
    $i<10 && $i = '0' . $i;
    $allBlue[] = $i;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>黄金点位选号法 - <?php echo $cfg_seotitle; ?></title>
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
        $('#cp_dayid').change(function(){
            window.location.href = "?cp_dayid="+$(this).val();
        })
        $("#doData").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'red_gold'
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
        </form>
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">黄金点位推测红蓝</a>
        <?php } ?>
        
        <div class="clearfix"></div>
        <div class="center-block" id="chart2" style="height: 250px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">下期</th>
                        <th width="25%">推测红球</th>
                        <th width="25%">剩余红球</th>
                        <th width="20%">推测蓝球</th>
                        <th width="20%">剩余蓝球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $new = $dosql->GetOne("SELECT MAX(cp_dayid) as max FROM `#@__caipiao_history`");
                        $new_cp_dayid = $new['max'] + 1;

                        $before_5red  = array();
                        $before_5blue = array();
                        $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$new_cp_dayid' ORDER BY cp_dayid DESC LIMIT 5");
                        while($row2 = $dosql->GetArray()){
                        	$red_arr = explode(',', $row2['red_num']);
                        	foreach ($red_arr as $key => $red_v) {
                        		$index = $key + 1;
                        		if(!isset($before_5red[$index])){
                        			$before_5red[$index] = array();
                        		}
                        		$before_5red[$index][] = $red_v;
                        	}
                        	$before_5blue[] = $row2['blue_num'];
                        }
                        //预测红球数
                        $yuce_red = array();
                        foreach ($before_5red as $redball) {
                        	$redball_sum = array_sum($redball);
                        	$redball_avg = intval($redball_sum / 5);
                        	$yuce_red[] = $redball_avg - 1 < 10 ? '0'.($redball_avg - 1) : $redball_avg - 1;
                        	$yuce_red[] = $redball_avg + 1 < 10 ? '0'.($redball_avg + 1) : $redball_avg + 1;
                        }
                        $yuce_red = array_unique($yuce_red);

                        //预测蓝球数
                        $yuce_blue    = array();
                        $blueball_sum = array_sum($before_5blue);
                        $blueball_avg = intval($blueball_sum / 5);
                        
                        $yuce_blue[]  = $blueball_avg;
                        $yuce_blue[]  = $blueball_avg - 1 < 10 ? '0'.($blueball_avg - 1) : $blueball_avg - 1;
                        $yuce_blue[]  = $blueball_avg - 2 < 10 ? '0'.($blueball_avg - 2) : $blueball_avg - 2;
                        $yuce_blue[]  = $blueball_avg + 1 < 10 ? '0'.($blueball_avg + 1) : $blueball_avg + 1;
                        $yuce_blue[]  = $blueball_avg + 2 < 10 ? '0'.($blueball_avg + 2) : $blueball_avg + 2;

                    ?>
                    <tr class="info">
                        <td><?php echo nextCpDayId($new['max']) ?></td>
                        <td>
                        <?php 
                            foreach ($yuce_red as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                        	$diffRed = array_diff($allRed, $yuce_red);
                            foreach ($diffRed as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($yuce_blue as $tmp_blue) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                        	$diffBlue = array_diff($allBlue, $yuce_blue);
                            foreach ($diffBlue as $tmp_blue) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_blue . '</button>';
                            }
                        ?>
                        </td>
                    </tr>
                </tbody>
            </table>

	        <div class="center-block" id="chart1" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>

            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">期数</th>
                        <th width="5%">开奖号码</th>
                        <th width="15%">猜中红球</th>
                        <th width="10%">猜中数占比</th>
                        <th width="15%">预测蓝球</th>
                        <th width="25%">红球频率</th>
                        <th width="25%">33剩余红球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sql = "SELECT * FROM `#@__caipiao_gold_analysis` ORDER BY cp_dayid DESC";
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
                        <td>
                        <?php 
                            $yuce_red_list = explode(',', $row['yuce_red_list']);
                            foreach ($yuce_red_list as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <?php 
                            $yuce_red = explode(',', $row['yuce_red']);
                        ?>
                        <td><?php echo $row['yuce_red_num'] . '/' . count($yuce_red) ?></td>
                        <td>
                        <?php 
                        	$yuce_blue = explode(',', $row['yuce_blue']);
                            foreach ($yuce_blue as $tmp_blue) {
                                $style = $tmp_blue == $row['blue_num'] ? 'danger' : 'primary';
                                echo '<button class="btn btn-'.$style.'" type="button">'.$tmp_blue . '</span></button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                        	$red_num = explode(',', $row['red_num']);
                        	$yuce_red = array_unique($yuce_red);
                            foreach ($yuce_red as $tmp_red) {
                                $style = in_array($tmp_red, $red_num) ? 'danger' : 'primary';
                                echo '<button class="btn btn-'.$style.'" type="button">'.$tmp_red . '</span></button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $diffRed = array_diff($allRed, $yuce_red);
                            foreach ($diffRed as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red .'</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span>
                                    </button>';
                                }
                            }
                        ?>
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

<?php

$red_gold = array();
$red_gold['date']          = array();
$red_gold['yuce_red_num']  = array();
$red_gold['yuce_blue_num'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_gold_analysis` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $red_gold['date'][] = $xdate;
	$red_gold['yuce_red_num'][]  = $row['yuce_red_num'];
	$red_gold['yuce_blue_num'][] = $row['yuce_blue_num'];
}

$red_gold['date']          = array_reverse($red_gold['date']);
$red_gold['yuce_red_num']  = array_reverse($red_gold['yuce_red_num']);
$red_gold['yuce_blue_num'] = array_reverse($red_gold['yuce_blue_num']);

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
        var title = '黄金点位——红球命中个数走势图';
        var title2 = '黄金点位——蓝球命中走势图[1命中]';
        var xData = <?php echo json_encode($red_gold['date']) ?>;

        var yData = <?php echo json_encode($red_gold['yuce_red_num']) ?>;
        var y2Data = <?php echo json_encode($red_gold['yuce_blue_num']) ?>;
        var myChart = ec.init(document.getElementById('chart1'));
        var myChart2 = ec.init(document.getElementById('chart2'));
        
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
                max: 6,
                name: 'y',
                splitNumber: 6
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
                type: 'value',
                min: 0,
                max: 1,
                name: 'y',
                splitNumber: 1
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
                    data: y2Data
                }
            ]
        };
        myChart.setOption(option);
        myChart2.setOption(option2);
    }
);
</script>