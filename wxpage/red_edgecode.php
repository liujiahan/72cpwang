<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';


LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球边码命中走势图 - <?php echo $cfg_seotitle; ?></title>
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
                    action: 'red_edgecode'
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
          <!-- <div class="form-group">
            <label for="cp_dayid">选择红尾</label>
            <select class="form-control" name="tailnum" id="tailnum" onchange="godayurl()">
                <option value="-1">--请选择--</option>
                <?php foreach (array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9) as $tail) { ?>
                <option value="<?php echo $tail ?>" <?php echo isset($tailnum) && $tailnum == $tail ? 'selected' : ''; ?>><?php echo $tail . '尾'; ?></option>
                <?php } ?>
            </select>
          </div> -->
          <!-- <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a> -->
        </form> 
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">统计边码命中</a>
        <?php } ?>
        
        <?php
            $data = redEdgeCode();
        ?>
        
        <div class="clearfix"></div>
        <p class="lead"></p>
        <blockquote>
            <p><?php echo nextCpDayId($data['cp_dayid']) . '期边码红球（'.count($data['redList']).'个）: ' . implode(" ", $data['redList']); ?></p>
        </blockquote>
        <div class="bs-example" data-example-id="contextual-table">
            <div class="clearfix"></div>
            <div class="center-block" id="chart" style="height: 400px;"></div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10%">期数</th>
                            <th width="50%">边码</th>
                            <th width="20%">边码命中</th>
                            <th width="20%">开奖号</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT * FROM `#@__caipiao_red_edgecode` ORDER BY cp_dayid DESC";
                            if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                                $sql .= " LIMIT {$no_admin_limit}";
                            }

                            $dopage->GetPage($sql);
                            while($row = $dosql->GetArray()){
                                $red_num = explode(',', $row['red_num']);
                                $red_edgecode = explode(',', $row['red_edgecode']);

                        ?>
                        <tr>
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td>
                                <?php foreach ($red_edgecode as $tmp_red) { ?>
                                    <?php if (in_array($tmp_red, $red_num)) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
                                    <?php }else{ ?>
                                    <button class="btn btn-primary" type="button"><?php echo $tmp_red; ?></span></button>
                                    <?php } ?>
                                <?php } ?>
                            </td>

                            <td>
                                <button class="btn btn-info" type="button"><?php echo count($red_edgecode) . '中' . $row['win_num']; ?></span></button>
                            </td>
                            <td>
                                <?php foreach ($red_num as $tmp_red) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
                                <?php } ?>
                            </td>
                        </tr>          
                        <?php } ?>
                    </tbody>
                </table>
                <?php echo $dopage->GetList(); ?>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

<?php

$suanfa = array();
$suanfa['date']    = array();
$suanfa['win_num'] = array();

$limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 30;
$dosql->Execute("SELECT * FROM `#@__caipiao_red_edgecode` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    }
    $suanfa['date'][] = $xdate;
    $suanfa['win_num'][] = $row['win_num'];
}

$suanfa['date']       = array_reverse($suanfa['date']);
$suanfa['win_num']       = array_reverse($suanfa['win_num']);

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
        var title = '红球边码命中走势图';
        var myChart = ec.init(document.getElementById('chart'));
        var xData = <?php echo json_encode($suanfa['date']) ?>;
        var yData = <?php echo json_encode($suanfa['win_num']) ?>;

        
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
        myChart.setOption(option);
    }
);
</script>