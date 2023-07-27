<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>百合算法分析 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="/static/bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val();
    }
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val()+'&cp_dayid='+$("#cp_dayid").val();
    }
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="godayurl()">
                <option value="">--请选择--</option>
                <?php 
                    $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 30");
                    $cp_day = array();
                    $max_dayid = 0;
                    while($row = $dosql->GetArray()){
                        $sel_cp_dayid = $row['cp_dayid'];
                        // if($max_dayid == 0){
                        //     $max_dayid = $sel_cp_dayid;
                        //     $cp_dayid = $max_dayid;
                        // }
                ?>
                <option value="<?php echo $sel_cp_dayid ?>" <?php echo isset($cp_dayid) && $sel_cp_dayid == $cp_dayid ? 'selected' : ''; ?>><?php echo $sel_cp_dayid; ?></option>
                <?php } ?>
            </select>
          </div>
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
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <?php 
                    $nextBeiHe = nextBaiHe();
                ?>
                <thead>
                    <tr>
                        <th width="">期数</th>
                        <th width="">百分比（<?php echo count($nextBeiHe['data_percent_reds']) ?>）</th>
                        <th width="">和数值（<?php echo count($nextBeiHe['data_sum_reds']) ?>）</th>
                        <th width="">合集（<?php echo count($nextBeiHe['merge_data']) ?>）</th>
                        <th width="">余集（<?php echo count($nextBeiHe['other_reds']) ?>）</th>
                        <th width="">交集（<?php echo count($nextBeiHe['jiaoji_reds']) ?>）</th>
                        <th width="">百【减和】（<?php echo count($nextBeiHe['percent_reds']) ?>）</th>
                        <th width="">和【减百】（<?php echo count($nextBeiHe['sum_reds']) ?>）</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="info">
                        <td><?php echo $nextBeiHe['cp_dayid'] ?></td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['data_percent_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['data_sum_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['merge_data'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['other_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['jiaoji_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['percent_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            foreach ($nextBeiHe['sum_reds'] as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                    </tr>
                    <tr class="info">
                        <td><?php echo $nextBeiHe['cp_dayid'] ?></td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['data_percent_reds']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['data_sum_reds']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['merge_data']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['other_reds']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['jiaoji_reds']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['percent_reds']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $nextBeiHe['sum_reds']);
                        ?>
                        </td>
                    </tr>
                </tbody>
            </table>  


            <div class="center-block" id="baihe_chart" style="height: 300px;"></div>          
            <div class="center-block" id="baihe_other_chart" style="height: 300px;"></div>          

            <table class="table">
                <thead>
                    <tr>
                        <th width="">期数</th>
                        <th width="">开奖号码</th>
                        <th width="">百合命中</th>
                        <th width="">百分比预测、和值除数定胆预测</th>
                        <th width="">百合交集、差集</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $sql = "SELECT * FROM `#@__caipiao_baihe` ORDER BY cp_dayid DESC LIMIT 5";
                        if(!empty($cp_dayid)){
                            $sql = "SELECT * FROM `#@__caipiao_baihe` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT 5";
                        }
                        $dosql->Execute($sql);
                        // $dopage->GetPage($sql, 10);
                        while($row = $dosql->GetArray()){
                            $red_num = explode(",", $row['red_num']);

                            $baihe_reds = explode(',', $row['baihe_reds']);
                    ?>
                    <tr>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['baihe_win'] . '/' . count($baihe_reds) ?></td>
                        <td>
                        <?php 

                            echo "百：【".$row['percent_reds_win']."】" ;
                            echo "<br/>";
                            $row['percent_reds'] = explode(',', $row['percent_reds']);
                            foreach ($row['percent_reds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
                            echo "<br/>";
                            echo "和：【".$row['sum_reds_win']."】";
                            echo "<br/>";
                            $row['sum_reds'] = explode(',', $row['sum_reds']);
                            foreach ($row['sum_reds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
                            echo "<br/>";
                            echo "余：【".$row['other_reds_win']."】";
                            echo "<br/>";
                            $row['other_reds'] = explode(',', $row['other_reds']);
                            foreach ($row['other_reds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo "交【百和】：【".$row['jiaoji_reds_win']."】";
                            echo "<br/>";
                            $row['jiaoji_reds'] = explode(',', $row['jiaoji_reds']);
                            foreach ($row['jiaoji_reds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
                            echo "<br/>";

                            echo "<br/>";
                            echo "百【减和】：【".$row['percent_jreds_win']."】";
                            echo "<br/>";
                            $row['percent_jreds'] = explode(',', $row['percent_jreds']);
                            foreach ($row['percent_jreds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
                            echo "<br/>";
                            echo "和【减百】：【".$row['sum_jreds_win']."】";
                            echo "<br/>";
                            $row['sum_jreds'] = explode(',', $row['sum_jreds']);
                            foreach ($row['sum_jreds'] as $tmp_red) {
                                if(in_array($tmp_red, $red_num)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                                }
                            };
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

$baihe = array();
$baihe['date']              = array();
$baihe['baihe_win']         = array();
$baihe['percent_reds_win']  = array();
$baihe['sum_reds_win']      = array();
$baihe['other_reds_win']    = array();
$baihe['jiaoji_reds_win']   = array();
$baihe['percent_jreds_win'] = array();
$baihe['sum_jreds_win']     = array();

$limit_num = isset($limit_num) ? $limit_num : 8;
$dosql->Execute("SELECT * FROM `#@__caipiao_baihe` ORDER BY cp_dayid DESC LIMIT $limit_num");
if(!empty($cp_dayid)){
    $dosql->Execute("SELECT * FROM `#@__caipiao_baihe` WHERE cp_dayid<='$cp_dayid' ORDER BY cp_dayid DESC LIMIT $limit_num");
}
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $baihe['date'][] = $xdate;
    $baihe['baihe_win'][]         = $row['baihe_win'];
    $baihe['percent_reds_win'][]  = $row['percent_reds_win'];
    $baihe['sum_reds_win'][]      = $row['sum_reds_win'];
    $baihe['other_reds_win'][]    = $row['other_reds_win'];
    $baihe['jiaoji_reds_win'][]   = $row['jiaoji_reds_win'];
    $baihe['percent_jreds_win'][] = $row['percent_jreds_win'];
    $baihe['sum_jreds_win'][]     = $row['sum_jreds_win'];
}

$baihe['date']              = array_reverse($baihe['date']);
$baihe['baihe_win']         = array_reverse($baihe['baihe_win']);
$baihe['percent_reds_win']  = array_reverse($baihe['percent_reds_win']);
$baihe['sum_reds_win']      = array_reverse($baihe['sum_reds_win']);
$baihe['other_reds_win']    = array_reverse($baihe['other_reds_win']);
$baihe['jiaoji_reds_win']   = array_reverse($baihe['jiaoji_reds_win']);
$baihe['percent_jreds_win'] = array_reverse($baihe['percent_jreds_win']);
$baihe['sum_jreds_win']     = array_reverse($baihe['sum_jreds_win']);

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
        var title = '百合算法命中走势图';
        var xData = <?php echo json_encode($baihe['date']) ?>;

        var yData          = <?php echo json_encode($baihe['baihe_win']) ?>;
        var percent_yData  = <?php echo json_encode($baihe['percent_reds_win']) ?>;
        var sum_yData      = <?php echo json_encode($baihe['sum_reds_win']) ?>;
        var other_yData    = <?php echo json_encode($baihe['other_reds_win']) ?>;
        var jiao_yData     = <?php echo json_encode($baihe['jiaoji_reds_win']) ?>;
        var percentJ_yData = <?php echo json_encode($baihe['percent_jreds_win']) ?>;
        var sumJ_yData     = <?php echo json_encode($baihe['sum_jreds_win']) ?>;

        var myChart = ec.init(document.getElementById('baihe_chart'));
        var myChart2 = ec.init(document.getElementById('baihe_other_chart'));
        
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
                data: ['百合','百分比','和值除胆','百合余'],
                selected: {  
                    '百分比': false,  
                    '和值除胆': false,  
                    '百合余': false 
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
                type: 'value',
                min: 0,
                max: 6,
                name: 'y',
                splitNumber: 6
            },
            series: [
                {
                    name: '百合',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#FD0C0C',
                        lineStyle:{
                            color:'#FD0C0C'  
                        } 
                      }
                    },
                    data: yData
                },
                {
                    name: '百分比',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#2F4554',
                        lineStyle:{
                            color:'#2F4554'  
                        } 
                      }
                    },
                    data: percent_yData
                },
                {
                    name: '和值除胆',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#A624AB',
                        lineStyle:{
                            color:'#A624AB'  
                        } 
                      }
                    },
                    data: sum_yData
                },
                {
                    name: '百合余',
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
                    data: other_yData
                }
            ]
        };
        myChart.setOption(option);

        var option2 = {
            title: {
                text: '百合算法交、差集命中走势图',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['百合交集','百减合','合减百'],
                selected: {  
                    '百减合': false,  
                    '合减百': false,
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
                type: 'value',
                min: 0,
                max: 6,
                name: 'y',
                splitNumber: 6
            },
            series: [
                {
                    name: '百合交集',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#FD0C0C',
                        lineStyle:{
                            color:'#FD0C0C'  
                        } 
                      }
                    },
                    data: jiao_yData
                },
                {
                    name: '百减合',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#8000FF',
                        lineStyle:{
                            color:'#8000FF'  
                        } 
                      }
                    },
                    data: percentJ_yData
                },
                {
                    name: '合减百',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#1373DF',
                        lineStyle:{
                            color:'#1373DF'  
                        } 
                      }
                    },
                    data: sumJ_yData
                }
            ]
        };
        myChart2.setOption(option2);
    }
);
</script>
