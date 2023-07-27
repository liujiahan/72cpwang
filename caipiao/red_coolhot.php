<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/suanfa.func.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球遗漏冷热走势 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="/static/bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
    }

    $(document).ready(function() {
        $('#cp_dayid').change(function(){
            window.location.href = "?cp_dayid="+$(this).val();
        })
    })
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
          <label for="exampleInputEmail2">选择期数</label>
          <select class="form-control" name="limit_num" id="limit_num" onchange="godayurl()">
              <option value="">--请选择--</option>
              <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
              <?php if($daynum>100)continue; ?>
                <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
              <?php } ?>
          </select>
          </div>
        </form>
        <div class="clearfix"></div>
        <div class="center-block" id="chart2" style="height: 250px;"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th width="20%">下期</th>
                        <th width="45%">热区遗漏值</th>
                        <th width="20%">温区遗漏值</th>
                        <th width="15%">冷区遗漏值</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $new = $dosql->GetOne("SELECT MAX(cp_dayid) as max FROM `#@__caipiao_history`");
                        $new_cp_dayid = $new['max'] + 1;

                        $missArr = array('hot'=>array(), 'warm'=>array(), 'cool'=>array());
                        $all_red_miss = redMissing();
                        foreach ($all_red_miss as $tmp_red => $tmp_miss) {
                        	if($tmp_miss >= 0 && $tmp_miss <= 4){
                        		$missArr['hot'][$tmp_red] = $tmp_miss;
                        	}else if($tmp_miss >= 5 && $tmp_miss <= 9){
                        		$missArr['warm'][$tmp_red] = $tmp_miss;
                        	}else if($tmp_miss > 9){
                        		$missArr['cool'][$tmp_red] = $tmp_miss;
                        	}
                        }
                    ?>

                    <tr class="info">
                        <td><?php echo $new_cp_dayid ?></td>
                        <td>
                        <?php 
                            foreach ($missArr['hot'] as $tmp_red => $tmp_miss) {
                                echo '<a href="red_kchart.php?cur_red='.$tmp_red.'" target="_blank"><button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button></a>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($missArr['warm'] as $tmp_red => $tmp_miss) {
                                echo '<a href="red_kchart.php?cur_red='.$tmp_red.'" target="_blank"><button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button></a>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($missArr['cool'] as $tmp_red => $tmp_miss) {
                                echo '<a href="red_kchart.php?cur_red='.$tmp_red.'" target="_blank"><button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button></a>';
                            }
                        ?>
                        </td>
                    </tr>
                    <tr class="info">
                        <td><?php echo $new_cp_dayid ?></td>
                        <td>
                        <?php 
                            echo "热球数".count($missArr['hot'])."个：" . implode(" ", array_keys($missArr['hot']));
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo "温球数".count($missArr['warm'])."个：" . implode(" ", array_keys($missArr['warm']));
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo "冷球数".count($missArr['cool'])."个：" . implode(" ", array_keys($missArr['cool']));
                        ?>
                        </td>
                    </tr>
                </tbody>
            </table>

	        <div class="center-block" id="chart1" style="height: 300px;"></div>

            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="7%">热-温-冷</th>
                        <th width="8%">遗漏和值</th>
                        <th width="20%">出球和遗漏</th>
                        <th width="50%">余球和遗漏</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sql = "SELECT * FROM `#@__caipiao_cool_hot` ORDER BY cp_dayid DESC LIMIT 5";
                        $dosql->Execute($sql);
                        // $dopage->GetPage($sql, 5);
                        $i = 0;
                        while($row = $dosql->GetArray()){
                            $i++;
                    ?>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['hot_num'] . ' - ' . $row['warm_num'] . ' - ' . $row['cool_num'] ?></td>
                        <td><?php echo $row['miss_sum'] ?></td>
                        <td>
                        <?php 
                        	$win_miss = unserialize($row['win_miss']);
                            foreach ($win_miss as $tmp_red => $tmp_miss) {
                                echo '<button class="btn btn-danger" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                            }
                        ?>
                        </td>
                        <td>
                        	<?php 
                        		$win_miss = array_keys($win_miss);
								$miss_content = unserialize($row['miss_content']);
                        	    foreach ($miss_content as $tmp_red => $tmp_miss) {
                        	    	if(in_array($tmp_red, $win_miss)){
                        	    		continue;
                        	    	}
                        	        echo '<button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                        	    }
                        	?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php //echo $dopage->GetList(); ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

<?php

$coolHot = array();
$coolHot['date']     = array();
$coolHot['hot_num']  = array();
$coolHot['warm_num'] = array();
$coolHot['cool_num'] = array();
$coolHot['cool_num'] = array();
$coolHot['max_min']  = array('max'=>0, 'min'=>0);

$limit_num = isset($limit_num) ? $limit_num : 8;

if(isset($tothis) && !empty($tothis)){
    $dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid<=$tothis ORDER BY cp_dayid DESC LIMIT $limit_num");
}else{
    $dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot` ORDER BY cp_dayid DESC LIMIT $limit_num");
}
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
	$coolHot['date'][]     = $xdate;
	$coolHot['hot_num'][]  = $row['hot_num'];
	$coolHot['warm_num'][] = $row['warm_num'];
	$coolHot['cool_num'][] = $row['cool_num'];
	$coolHot['miss_sum'][] = $row['miss_sum'];
	if($coolHot['max_min']['max'] < $row['miss_sum']){
		$coolHot['max_min']['max'] = $row['miss_sum'];
	}
	if($coolHot['max_min']['min'] == 0){
		$coolHot['max_min']['min'] = $row['miss_sum'];
	}
	if($coolHot['max_min']['min'] > $row['miss_sum']){
		$coolHot['max_min']['min'] = $row['miss_sum'];
	}
}

$coolHot['date']     = array_reverse($coolHot['date']);
$coolHot['hot_num']  = array_reverse($coolHot['hot_num']);
$coolHot['warm_num'] = array_reverse($coolHot['warm_num']);
$coolHot['cool_num'] = array_reverse($coolHot['cool_num']);
$coolHot['miss_sum'] = array_reverse($coolHot['miss_sum']);

?>
</html>
<script type="text/javascript" src="/static/js/echarts.js"></script>
<script type="text/javascript">
require.config({
    paths: {
        echarts: '/static/js'
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
        var title = '红球遗漏和值走势图';
        var title2 = '红球遗漏冷热出球走势图';
        var xData = <?php echo json_encode($coolHot['date']) ?>;

        var yData = <?php echo json_encode($coolHot['miss_sum']) ?>;
        var miss_sum_max = <?php echo json_encode($coolHot['max_min']['max']) ?>;
        var miss_sum_min = <?php echo json_encode($coolHot['max_min']['min']) ?>;

        var y_hotData = <?php echo json_encode($coolHot['hot_num']) ?>;
        var y_warmData = <?php echo json_encode($coolHot['warm_num']) ?>;
        var y_coolData = <?php echo json_encode($coolHot['cool_num']) ?>;

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
                min: miss_sum_min,
                max: miss_sum_max,
                name: 'y',
                splitNumber: 10
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
                data: ['遗漏热区出球数', '遗漏温区出球数', '遗漏冷区出球数']
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
                    name: '遗漏热区出球数',
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
                    data: y_hotData
                },{
                    name: '遗漏温区出球数',
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
                    data: y_warmData
                },{
                    name: '遗漏冷区出球数',
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
                    data: y_coolData
                }
            ]
        };
        myChart.setOption(option);
        myChart2.setOption(option2);
    }
);
</script>