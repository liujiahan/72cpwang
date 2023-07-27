<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
$index = 0;
$blueData = array();
$blueDays = array();
while($row = $dosql->GetArray()){
    $blueData[$index] = $row['blue_num'];
    $blueDays[$index] = $row['cp_dayid'];
    $index++;
}

$rest = array();
// $data = array(0=>0, 1=>0);
$data = array();
foreach ($blueData as $index => $blue) {
    if(isset($blueData[$index+1])){
        $blue22 = $blueData[$index+1];
        $tail_1 = $blue % 10;
        $tail_2 = $blue22 % 10;
        if($tail_1 == $tail_2 && $blue != $blue22){
            $dindex = $tail_1 % 2;
            $str = $blueDays[$index] . '期出 蓝' . $blue . '---' . $blueDays[$index+1].'期出 蓝'.$blue22 . '---';
            if(isset($blueData[$index+2])){
                $str .= $blueDays[$index+2].'期出 蓝'.$blueData[$index+2];
                $dindex2 = $blueData[$index+2] % 2;
                if(!isset($data[$dindex])){
                    $data[$dindex] = array();
                }
                if(!isset($data[$dindex][$dindex2])){
                    $data[$dindex][$dindex2] = 0;
                }
                $data[$dindex][$dindex2]++;
            }
            $rest[] = $str;
        }
    }
}
echo count($rest);
echo "<br/>";
foreach ($rest as $key => $value) {
    echo $value;
    echo "<br/>";
}
die;
echo "<pre>";
print_r($rest);
echo "</pre>";

die;

$redsList = array_reverse($redsList);
// print_r($redsList);die;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no">
    <meta id="viewport" name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
    <title>支行数据对比 - 网络金融</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>

<body>
    <div style="padding: 20px 0;">
        <div id="data1" style="height:500px;"></div>
        <!-- <div id="data2" style="height:400px;"></div> -->
    </div>
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

    var myChart = echarts.init(document.getElementById('data1'));
    option = {
        title: {
            text: '双色球指数',
            left: 0
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            }
        },
        legend: {
            data: ['日K', '5日线', '10日线', '25日线', '50日线']
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
                name: '日K',
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
                name: '5日线',
                type: 'line',
                data: calculateMA(5),
                smooth: true,
                lineStyle: {
                    normal: {opacity: 1, color:'#FF7F50'},
                    
                }
            },
            {
                name: '10日线',
                type: 'line',
                data: calculateMA(10),
                smooth: true,
                lineStyle: {
                    normal: {opacity: 1, color:'#87CEFA'},
                }
            },
            {
                name: '25日线',
                type: 'line',
                data: calculateMA(25),
                smooth: true,
                lineStyle: {
                    normal: {opacity: 1, color:'#DA70D6'},
                }
            },
            {
                name: '50日线',
                type: 'line',
                data: calculateMA(50),
                smooth: true,
                lineStyle: {
                    normal: {opacity: 1, color:'#32CD32'},
                }
            },

        ]
    };
    myChart.setOption(option);
    </script>
</body>

</html>
