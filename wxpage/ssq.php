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
        <div id="data1" style="height:300px;"></div>
        <div id="data2" style="height:400px;"></div>
    </div>
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
            var myChart = ec.init(document.getElementById('data1'));
            var option2 = {
              title: {
                  text: '对数轴示例',
                  left: 'center'
              },
              tooltip: {
                  trigger: 'item',
                  formatter: '{a} <br/>{b} : {c}'
              },
              legend: {
                  left: 'left',
                  data: ['命中数']
              },
              xAxis: {
                  type: 'category',
                  name: 'x',
                  splitLine: {show: false},
                  data: ['2017001', '2017002', '2017003', '2017004', '2017005', '2017006', '2017007', '2017008', '2017009', '2017001', '2017002', '2017003', '2017004', '2017005', '2017006', '2017007', '2017008', '2017009']
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
                  name: 'y'
              },
              series: [
                  {
                      name: '命中数',
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
                      data: [5, 4, 2, 3, 0, 3, 6, 5, 3, 5, 4, 2, 3, 0, 3, 6, 5, 3]
                  }
              ]
          };
            myChart.setOption(option2);
        }
    );
    </script>
</body>

</html>
