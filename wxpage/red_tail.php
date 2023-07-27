<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';


LoginCheck();

$tailarr = array();
for ($i=0; $i <= 9; $i++) { 
    $tailarr[$i] = 0;
}
$alltail = array_keys($tailarr);
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球尾数走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val()+"&tailnum="+$(this).val();
    }

    $(document).ready(function() {
        $('#tailnum').change(function(){
            window.location.href = "?tailnum="+$("#tailnum").val()+"&limit_num="+$("#limit_num").val();
        })
        $('#limit_num').change(function(){
            window.location.href = "?tailnum="+$("#tailnum").val()+"&limit_num="+$("#limit_num").val();
        })
        $("#dataCalcBtn").click(function() {
            $(this).html('计算中...');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'red_tail'
                },
                success: function(data) {
                    // alert("成功计算"+data+"期");
                    window.location.reload();
                }
            })
        })
    })
    </script>
    <style type="text/css">
        .red_ball{
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
        .blue_ball{
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
    </style>
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
          <div class="form-group">
            <label for="cp_dayid">选择红尾</label>
            <select class="form-control" name="tailnum" id="tailnum" onchange="godayurl()">
                <option value="-1">--请选择--</option>
                <?php foreach (array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9) as $tail) { ?>
                <option value="<?php echo $tail ?>" <?php echo isset($tailnum) && $tailnum == $tail ? 'selected' : ''; ?>><?php echo $tail . '尾'; ?></option>
                <?php } ?>
            </select>
          </div>
          <!-- <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a> -->
        </form> 
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">统计红尾</a>
        <?php } ?>
        
        <?php
           $preData = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
           $pre_red = explode(',', $preData['red_num']);
           $pre_tail = array();
           foreach ($pre_red as $red) {
                $pre_tail[] = $red % 10;
           }
           $pre_tail = array_unique($pre_tail);
           sort($pre_tail);
        ?>
        
        <div class="clearfix"></div>
        <p class="lead"></p>
        <blockquote>
            <p><?php echo $preData['cp_dayid'] . '期开出尾数: ' . implode(" ", $pre_tail); ?>，未开出尾数：<?php echo implode(" ", array_diff($alltail, $pre_tail)); ?></p>
        </blockquote>
        <div class="bs-example" data-example-id="contextual-table">
            <div class="clearfix"></div>
            <div class="center-block" id="chart1" style="height: 300px;"></div>
            <div class="center-block" id="chart2" style="height: 300px;<?php echo isset($width) ? ' width: '.$width.'px;' : '';?>"></div>
            <div class="center-block" id="chart3" style="height: 300px;"></div>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

<?php

$suanfa = array();
$suanfa['date']    = array();
$suanfa['tail'] = array();
$suanfa['repeat_tail'] = array();
$suanfa['diff_tail'] = array();

$suanfa['tail_num']   = array();
$suanfa['small_tail'] = array();
$suanfa['big_tail']   = array();
$suanfa['num4_tail']  = array();
$suanfa['num3_tail']  = array();
$suanfa['odd_tail']   = array();
$suanfa['even_tail']  = array();
$suanfa['prime_tail'] = array();
$suanfa['comb_tail']  = array();
$suanfa['nopc_tail']  = array();

$limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 30;
$_tailnum = 6;
if(isset($tailnum)){
    if($tailnum != -1){
        $_tailnum = $tailnum;
    }
}
$tailnum = $_tailnum;
// $tailnum = isset($tailnum) && $tailnum == -1 ? 6 : $tailnum;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_tail` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $red_tail = unserialize($row['red_tail']);
    $tail_win = $red_tail[$tailnum];
    $suanfa['date'][] = $xdate;
    $suanfa['tail'][] = $tail_win;

    $red_tail_detail = unserialize($row['red_tail_detail']);

    $suanfa['tail_num'][]   = $red_tail_detail['tail_num'];
    $suanfa['small_tail'][] = $red_tail_detail['small_tail'];
    $suanfa['big_tail'][]   = $red_tail_detail['big_tail'];
    $suanfa['num4_tail'][]  = $red_tail_detail['num4_tail'];
    $suanfa['num3_tail'][]  = $red_tail_detail['num3_tail'];
    $suanfa['odd_tail'][]   = $red_tail_detail['odd_tail'];
    $suanfa['even_tail'][]  = $red_tail_detail['even_tail'];
    $suanfa['prime_tail'][] = $red_tail_detail['prime_tail'];
    $suanfa['comb_tail'][]  = $red_tail_detail['comb_tail'];
    $suanfa['nopc_tail'][]  = $red_tail_detail['nopc_tail'];
    
    $suanfa['repeat_tail'][] = $row['repeat_tail'];
    $suanfa['diff_tail'][]   = $row['diff_tail'];
}

$suanfa['date']        = array_reverse($suanfa['date']);
$suanfa['tail']        = array_reverse($suanfa['tail']);

$suanfa['tail_num']   = array_reverse($suanfa['tail_num']);
$suanfa['small_tail'] = array_reverse($suanfa['small_tail']);
$suanfa['big_tail']   = array_reverse($suanfa['big_tail']);
$suanfa['num4_tail']  = array_reverse($suanfa['num4_tail']);
$suanfa['num3_tail']  = array_reverse($suanfa['num3_tail']);
$suanfa['odd_tail']   = array_reverse($suanfa['odd_tail']);
$suanfa['even_tail']  = array_reverse($suanfa['even_tail']);
$suanfa['prime_tail'] = array_reverse($suanfa['prime_tail']);
$suanfa['comb_tail']  = array_reverse($suanfa['comb_tail']);
$suanfa['nopc_tail']  = array_reverse($suanfa['nopc_tail']);

$suanfa['repeat_tail'] = array_reverse($suanfa['repeat_tail']);
$suanfa['diff_tail']   = array_reverse($suanfa['diff_tail']);

?>
</html>
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
        var title = '红球<?php echo $tailnum; ?>尾走势图';
        var xData = <?php echo json_encode($suanfa['date']) ?>;

        var yData = <?php echo json_encode($suanfa['tail']) ?>;
        var myChart = ec.init(document.getElementById('chart1'));
        var myChart2 = ec.init(document.getElementById('chart2'));
        var myChart3 = ec.init(document.getElementById('chart3'));

        var y0Data = <?php echo json_encode($suanfa['tail_num']) ?>;
        var y1Data = <?php echo json_encode($suanfa['small_tail']) ?>;
        var y2Data = <?php echo json_encode($suanfa['big_tail']) ?>;
        var y3Data = <?php echo json_encode($suanfa['num4_tail']) ?>;
        var y4Data = <?php echo json_encode($suanfa['num3_tail']) ?>;
        var y5Data = <?php echo json_encode($suanfa['odd_tail']) ?>;
        var y6Data = <?php echo json_encode($suanfa['even_tail']) ?>;
        var y61Data = <?php echo json_encode($suanfa['prime_tail']) ?>;
        var y62Data = <?php echo json_encode($suanfa['comb_tail']) ?>;
        var y63Data = <?php echo json_encode($suanfa['nopc_tail']) ?>;

        var y7Data = <?php echo json_encode($suanfa['repeat_tail']) ?>;
        var y8Data = <?php echo json_encode($suanfa['diff_tail']) ?>;
        
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
                max: 4,
                name: 'y',
                splitNumber: 4
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
        myChart.setOption(option);

        var option3 = {
            title: {
                text: '与上期重尾差尾命中数',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['与上期重合尾数个数','上期未出尾数出球个数'],
                selected: {  
                    '上期未出尾数出球个数': false,
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
                max: 4,
                name: 'y',
                splitNumber: 4
            },
            series: [
                {
                    name: '与上期重合尾数个数',
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
                    data: y7Data
                },{
                    name: '上期未出尾数出球个数',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#00A2E8',
                        lineStyle:{
                            color:'#00A2E8'  
                        } 
                      }
                    },
                    data: y8Data
                }
            ]
        };
        myChart3.setOption(option3);

        var option2 = {
            title: {
                text: '红球尾数指标',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c}'
            },
            legend: {
                left: 'left',
                data: ['红球尾数','尾数4','尾数3','质数尾','合数尾','非质合尾','小尾数','大尾数','奇数尾','偶数尾'],
                selected: {  
                    '尾数4': false,
                    '尾数3': false,
                    '质数尾': false,
                    '合数尾': false,
                    '非质合尾': false,
                    '小尾数': false,
                    '大尾数': false,
                    '奇数尾': false,
                    '偶数尾': false,
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
                    name: '红球尾数',
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
                    data: y0Data
                },{
                    name: '尾数4',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#930000',
                        lineStyle:{
                            color:'#930000'  
                        } 
                      }
                    },
                    data: y3Data
                },{
                    name: '尾数3',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#D9006C',
                        lineStyle:{
                            color:'#D9006C'  
                        } 
                      }
                    },
                    data: y4Data
                },{
                    name: '质数尾',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        // color:'#00A600',
                        // lineStyle:{
                        //     color:'#00A600'  
                        // } 
                      }
                    },
                    data: y61Data
                },{
                    name: '合数尾',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#7D26CD',
                        lineStyle:{
                            color:'#7D26CD'  
                        } 
                      }
                    },
                    data: y62Data
                },{
                    name: '非质合尾',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#000000',
                        lineStyle:{
                            color:'#000000'  
                        } 
                      }
                    },
                    data: y63Data
                },
                {
                    name: '小尾数',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#00A2E8',
                        lineStyle:{
                            color:'#00A2E8'  
                        } 
                      }
                    },
                    data: y1Data
                },{
                    name: '大尾数',
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
                    data: y2Data
                },{
                    name: '奇数尾',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#BB3D00',
                        lineStyle:{
                            color:'#BB3D00'  
                        } 
                      }
                    },
                    data: y5Data
                },{
                    name: '偶数尾',
                    type: 'line',
                    itemStyle : { 
                      normal: {
                        label : {show: true},
                        color:'#00A600',
                        lineStyle:{
                            color:'#00A600'  
                        } 
                      }
                    },
                    data: y6Data
                }
            ]
        };
        myChart2.setOption(option2);
    }
);
</script>