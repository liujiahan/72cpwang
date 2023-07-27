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
    <title>频率趋势选号法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
    	var cur_red = '';
    	if($('#cur_red').val() != ''){
    		cur_red = $('#cur_red').val();
    	}
    	if($('#cur_red2').val() != ''){
    		cur_red = $('#cur_red2').val();
    	}
    	if($('#cur_red3').val() != ''){
    		cur_red = $('#cur_red3').val();
    	}

        var nextWin;
        if ($("#nextWin").is(":checked")) {
            nextWin = 1;
        } else {
            nextWin = 0;
        }
        var nextNoWin;
        if ($("#nextNoWin").is(":checked")) {
            nextNoWin = 1;
        } else {
            nextNoWin = 0;
        }
        window.location.href = "?limit_num="+$("#limit_num").val()+"&cur_red="+cur_red+'&nextWin='+nextWin+'&nextNoWin='+nextNoWin;
    }
    function gourl(){
        var nextWin;
        if ($("#nextWin").is(":checked")) {
            nextWin = 1;
        } else {
            nextWin = 0;
        }
        var nextNoWin;
        if ($("#nextNoWin").is(":checked")) {
            nextNoWin = 1;
        } else {
            nextNoWin = 0;
        }
        window.location.href = "?cur_red="+$("#cur_red").val()+"&limit_num="+$("#limit_num").val()+'&nextWin='+nextWin+'&nextNoWin='+nextNoWin;
    }
    function gourl2(){
        var nextWin;
        if ($("#nextWin").is(":checked")) {
            nextWin = 1;
        } else {
            nextWin = 0;
        }
        var nextNoWin;
        if ($("#nextNoWin").is(":checked")) {
            nextNoWin = 1;
        } else {
            nextNoWin = 0;
        }
        window.location.href = "?cur_red="+$("#cur_red2").val()+"&limit_num="+$("#limit_num").val()+'&nextWin='+nextWin+'&nextNoWin='+nextNoWin;
    }
    function gourl3(){
        var nextWin;
        if ($("#nextWin").is(":checked")) {
            nextWin = 1;
        } else {
            nextWin = 0;
        }
        var nextNoWin;
        if ($("#nextNoWin").is(":checked")) {
            nextNoWin = 1;
        } else {
            nextNoWin = 0;
        }
        window.location.href = "?cur_red="+$("#cur_red3").val()+"&limit_num="+$("#limit_num").val()+'&nextWin='+nextWin+'&nextNoWin='+nextNoWin;
    }
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
            <!-- <option value="">--请选择--</option> -->
            <?php 
            $limit_num = isset($limit_num) ? $limit_num : 300;
            foreach (getSelArr() as $daynum => $daytxt) { ?>
              <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
            <?php } ?>
        </select>
        </div>
        <div class="form-group">
        <label for="exampleInputEmail2">33红球</label>
        <select class="form-control" name="cur_red" id="cur_red" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 1){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>

        <select class="form-control" name="cur_red2" id="cur_red2" onchange="gourl2()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 2){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>
        <select class="form-control" name="cur_red3" id="cur_red3" onchange="gourl3()">
            <option value="">--请选择--</option>
            <?php 
            for($i=1; $i<34; $i++){ 
                $i < 10 && $i = '0' . $i;
                if($i % 3 != 0){
                    continue;
                }
            ?>
            <option value="<?php echo $i ?>" <?php echo isset($cur_red) && $cur_red == $i ? 'selected' : ''; ?>>
                <?php echo $i; ?>
            </option>
            <?php } ?>
        </select>
        </div>
        <div class="form-group">
             <div class="checkbox">
               <label>
                 <input type="checkbox" id="nextWin" value="1" <?php echo isset($nextWin) && $nextWin == 1 ? 'checked' : '' ?>> 中
               </label>
             </div>
             <div class="checkbox">
               <label>
                 <input type="checkbox" id="nextNoWin" value="1" <?php echo isset($nextNoWin) && $nextNoWin == 1 ? 'checked' : '' ?>> 不中
               </label>
             </div>
         </div>
        <a href="javascript:;" class="btn btn-primary" onclick="godayurl()" role="button">查询</a>
        </form>        

        <!-- <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">频率趋势选号法</a> -->

        <?php 
          $redsList = array();
          $limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 300;
          $limit_num = $limit_num + 5;

          $myred = isset($cur_red) && !empty($cur_red) ? $cur_red : 01;
          $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit_num");
          $nextdata = array();
          while($row = $dosql->GetArray()){
              if(isset($nextWin) && $nextWin == 1 && !$nextdata){
                array_push($nextdata, $row['cp_dayid']+1);
                array_push($nextdata, 1);
                array_push($nextdata, 1);
                array_push($nextdata, 1);
                array_push($nextdata, 1);

                $redsList[] = $nextdata;
              }
              if(isset($nextNoWin) && $nextNoWin == 1 && !$nextdata){
                array_push($nextdata, $row['cp_dayid']+1);
                array_push($nextdata, 0);
                array_push($nextdata, 0);
                array_push($nextdata, 0);
                array_push($nextdata, 0);

                $redsList[] = $nextdata;
              }
              $tmp = array();
              array_push($tmp, $row['cp_dayid']);

              $red_num = explode(',', $row['red_num']);
              $iswin = in_array($myred, $red_num) ? 1 : 0;
              array_push($tmp, $iswin);
              array_push($tmp, $iswin);
              array_push($tmp, $iswin);
              array_push($tmp, $iswin);

              $redsList[] = $tmp;
          }

          $redsList = array_reverse($redsList);

        ?>
        <div class="clearfix"></div>
        <div class="center-block" id="sqq_kchart" style="height: 500px;"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<script type="text/javascript" src="js/echartsv3/echarts.js"></script>
<script type="text/javascript">
var data0 = splitData(<?php echo json_encode($redsList) ?>);

function splitData(rawData) {
    var categoryData = [];
    var values = []
    for (var i = 0; i < rawData.length; i++) {
        categoryData.push(rawData[i].splice(0, 1)[0]);
        values.push(rawData[i])
    }
    return {
        categoryData: categoryData,
        values: values
    };
}

function calculateMA(dayCount) {
    var result = [];
    for (var i = 0, len = data0.values.length; i < len; i++) {
        if (i < dayCount) {
            result.push('-');
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += data0.values[i - j][1];
        }
        result.push(sum / dayCount);
    }
    return result;
}

var myChart = echarts.init(document.getElementById('sqq_kchart'));
option = {
    title: {
        text: '红球指数',
        left: 0
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'cross'
        }
    },
    legend: {
        data: ['期K', '5期均线', '10期均线', '25期均线', '50期均线'],
        selected: {  
            '50日线': false,
        }

    },
    grid: {
        left: '10%',
        right: '10%',
        bottom: '15%'
    },
    xAxis: {
        type: 'category',
        data: data0.categoryData,
        scale: true,
        boundaryGap : false,
        axisLine: {onZero: false},
        splitLine: {show: false},
        splitNumber: 20,
        min: 'dataMin',
        max: 'dataMax'
    },
    yAxis: {
        scale: true,
        splitArea: {
            show: true
        }
    },
    dataZoom: [
        {
            type: 'inside',
            start: 50,
            end: 100
        },
        {
            show: true,
            type: 'slider',
            y: '90%',
            start: 50,
            end: 100
        }
    ],
    series: [
        {
            name: '期K',
            type: 'candlestick',
            data: data0.values,
            markPoint: {
                label: {
                    normal: {
                        formatter: function (param) {
                            return param != null ? Math.round(param.value) : '';
                        }
                    }
                },
                data: [
                    {
                        name: 'XX标点',
                        coord: ['2013/5/31', 2300],
                        value: 2300,
                        itemStyle: {
                            normal: {color: 'rgb(41,60,85)'}
                        }
                    },
                    {
                        name: 'highest value',
                        type: 'max',
                        valueDim: 'highest'
                    },
                    {
                        name: 'lowest value',
                        type: 'min',
                        valueDim: 'lowest'
                    },
                    {
                        name: 'average value on close',
                        type: 'average',
                        valueDim: 'close'
                    }
                ],
                tooltip: {
                    formatter: function (param) {
                        return param.name + '<br>' + (param.data.coord || '');
                    }
                }
            },
            markLine: {
                symbol: ['none', 'none'],
                data: [
                    [
                        {
                            name: 'from lowest to highest',
                            type: 'min',
                            valueDim: 'lowest',
                            symbol: 'circle',
                            symbolSize: 10,
                            label: {
                                normal: {show: false},
                                emphasis: {show: false}
                            }
                        },
                        {
                            type: 'max',
                            valueDim: 'highest',
                            symbol: 'circle',
                            symbolSize: 10,
                            label: {
                                normal: {show: false},
                                emphasis: {show: false}
                            }
                        }
                    ],
                    {
                        name: 'min line on close',
                        type: 'min',
                        valueDim: 'close'
                    },
                    {
                        name: 'max line on close',
                        type: 'max',
                        valueDim: 'close'
                    }
                ]
            }
        },
        {
            name: '5期均线',
            type: 'line',
            data: calculateMA(5),
            smooth: false,
            lineStyle: {
                normal: {opacity: 1, color:'#FF7F50'},
                
            }
        },
        {
            name: '10期均线',
            type: 'line',
            data: calculateMA(10),
            smooth: false,
            lineStyle: {
                normal: {opacity: 1, color:'#87CEFA'},
            }
        },
        {
            name: '25期均线',
            type: 'line',
            data: calculateMA(25),
            smooth: false,
            lineStyle: {
                normal: {opacity: 1, color:'#DA70D6'},
            }
        },
        {
            name: '50期均线',
            type: 'line',
            data: calculateMA(50),
            smooth: false,
            lineStyle: {
                normal: {opacity: 1, color:'#32CD32'},
            }
        },

    ]
};
myChart.setOption(option);
</script>