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
    <title>红球近期走势 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?limit_num="+$("#limit_num").val()+"&itemid="+$("#itemid").val();
    }

    $(document).ready(function() {
        /*$('#cp_dayid').change(function(){
            gourl();
            // window.location.href = "?cp_dayid="+$(this).val()+"&year_num="+$("#year_num").val();
        })
        $('#year_num').change(function(){
            gourl();
        })*/
        $("#doData").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'cpsave.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'sfone'
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
                <select class="form-control" name="limit_num" id="limit_num" onchange="gourl()">
                <option value="">--请选择--</option>
                <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
                <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
                <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail2">观察项</label>
                <select class="form-control" name="itemid" id="itemid" onchange="gourl()">
                    <option value="">--请选择--</option>
                    <?php foreach (getSelItem() as $tmp_item => $item) { ?>
                    <option value="<?php echo $tmp_item ?>" <?php echo isset($itemid) && $itemid==$tmp_item ? 'selected' : '' ?>>
                        <?php echo $item ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
          <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form> 
        <!-- <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">加减乘除</a> -->
        
        <div class="bs-example" data-example-id="contextual-table">
            
            <div class="clearfix"></div>

            <table class="table">
                <thead>
                    <tr>
                        <th width="">期数</th>
                        <th width="">开奖号码</th>
                        <th width="">蓝球</th>
                        <th width="">大小比</th>
                        <th width="">奇偶比</th>
                        <th width="">区间比</th>
                        <th width="">质合比</th>
                        <th width="">遗漏冷热比</th>
                        <th width="">43尾球比</th>
                        <th width="">遗漏和</th>
                        <th width="">和数值</th>
                        <th width="">AC值</th>
                        <th width="">重号</th>
                        <th width="">尾数和</th>
                        <th width="">尾数组</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        $score = array();
                        $prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);

                        $tail4 = array(1, 2, 3);
                        $tail3 = array(4, 5, 6, 7, 8, 9, 0);

                        $limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 10;
                        $maxid = maxDayid();
                        $cp_dayid = $maxid - $limit_num;
                        // $cp_dayid = 2016068;

                        $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid >= $cp_dayid ORDER BY cp_dayid ASC";
                        $dosql->Execute($sql);
                        $itemsData = array();
                        while($row = $dosql->GetArray()){
                            $score[$row['cp_dayid']] = array();
                            $red_num = explode(',', $row['red_num']);
                            $bigsmall = array(0=>0, 1=>0);
                            $oddeven = array(0=>0, 1=>0);
                            $redarea = array(0=>0, 1=>0, 2=>0);
                            $primenum = array(0=>0, 1=>0);

                            $before_id = $row['cp_dayid'] - 1;
                            $one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<{$row['cp_dayid']} ORDER BY cp_dayid DESC");
                            $before_reds = explode(',', $one['red_num']);
                            $repeat_num = 0;

                            $weisum = 0;
                            $tailgroup = array();
                            $tail4_3 = array(0=>0, 1=>0);
                            foreach ($red_num as $red) {
                                $red > 16 && $bigsmall[0]++;
                                $red < 17 && $bigsmall[1]++;

                                $red % 2 == 1 && $oddeven[0]++;
                                $red % 2 == 0 && $oddeven[1]++;

                                $red < 12 && $redarea[0]++;
                                $red > 11 && $red < 23 && $redarea[1]++;
                                $red > 22 && $redarea[2]++;

                                in_array($red, $prime) && $primenum[0]++;
                                !in_array($red, $prime) && $red != 1 && $primenum[1]++;

                                $tail = $red % 10;
                                if(in_array($tail, $tail4)){
                                    $tail4_3[0]++;
                                }else{
                                    $tail4_3[1]++;
                                }
                                in_array($red, $before_reds) && $repeat_num++;


                                $weisum += $tail;
                                $tailgroup[] = $tail;
                            }
                            $tailgroup = array_unique($tailgroup);

                            $sum = array_sum($red_num);
                            $acnum = getAC($red_num);

                            $curmiss = getCurMiss($row['cp_dayid']);

                            //蓝球
                            $row['blue_num'] < 10 && $row['blue_num']              = '0' . $row['blue_num'];
                            $itemsData['blue'][$row['cp_dayid']][$row['blue_num']] = $row['blue_num'];
                            $itemsData['ac'][$row['cp_dayid']][$acnum]             = $acnum;
                            $itemsData['repeat'][$row['cp_dayid']][$repeat_num]    = $repeat_num;
                            $itemsData['bigsmall'][$row['cp_dayid']][implode(":", $bigsmall)] = implode(":", $bigsmall);
                            $itemsData['oddeven'][$row['cp_dayid']][implode(":", $oddeven)]   = implode(":", $oddeven);
                            $itemsData['primenum'][$row['cp_dayid']][implode(":", $primenum)] = implode(":", $primenum);
                            $itemsData['redarea'][$row['cp_dayid']][implode("", $redarea)]    = implode("", $redarea);
                            $itemsData['tail4_3'][$row['cp_dayid']][implode(":", $tail4_3)]   = implode(":", $tail4_3);
                            $itemsData['tailgroup'][$row['cp_dayid']][count($tailgroup)]      = count($tailgroup);

                            $indexScore = indexScore();

                            $score[$row['cp_dayid']][] = $bigsmall_score = $indexScore['bigsmall'][implode(":", $bigsmall)];
                            $score[$row['cp_dayid']][] = $oddeven_score = $indexScore['oddeven'][implode(":", $oddeven)];
                            $score[$row['cp_dayid']][] = $redarea_score = $indexScore['redarea'][implode(":", $redarea)];
                            $score[$row['cp_dayid']][] = $cool_hot_score = $indexScore['cool_hot'][implode(":", $curmiss['cool_hot'])];
                            $score[$row['cp_dayid']][] = $primenum_score = $indexScore['primenum'][implode(":", $primenum)];
                            $score[$row['cp_dayid']][] = $tail4_3_score = $indexScore['tail4_3'][implode(":", $tail4_3)];

                            $sumlist = itemsTable('sum');
                            foreach ($sumlist as $key => $sumstr) {
                                $sumarr = explode('-', $sumstr);
                                if($sum >= $sumarr[0] && $sum <= $sumarr[1]){
                                    $itemsData['sum'][$row['cp_dayid']][$sumstr] = $sum;
                                    break;
                                }
                            }

                            $taillist = itemsTable('tailnum');
                            foreach ($taillist as $key => $tailstr) {
                                $tailarr = explode('-', $tailstr);
                                if($weisum >= $tailarr[0] && $weisum <= $tailarr[1]){
                                    $itemsData['tailnum'][$row['cp_dayid']][$tailstr] = $weisum;
                                    break;
                                }
                            }

                            $misslist = itemsTable('misssum');
                            foreach ($misslist as $key => $missstr) {
                                $missarr = explode('-', $missstr);
                                if($curmiss['miss_sum'] >= $missarr[0] && $curmiss['miss_sum'] <= $missarr[1]){
                                    $itemsData['misssum'][$row['cp_dayid']][$missstr] = $curmiss['miss_sum'];
                                    break;
                                }
                            }
                            $itemsData['hotcool'][$row['cp_dayid']][implode(":", $curmiss['cool_hot'])] = implode(":", $curmiss['cool_hot']);
                    ?>
                    <tr class="default">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['blue_num'] ?></td>
                        <td><?php echo implode(":", $bigsmall) ?>[<span style="color:blue;"><?php echo $bigsmall_score ?></span>]</td>
                        <td><?php echo implode(":", $oddeven) ?>[<span style="color:blue;"><?php echo $oddeven_score ?></span>]</td>
                        <td><?php echo implode(":", $redarea) ?>[<span style="color:blue;"><?php echo $redarea_score ?></span>]</td>
                        <td><?php echo implode(":", $primenum) ?>[<span style="color:blue;"><?php echo $primenum_score ?></span>]</td>
                        <td><?php echo implode(":", $curmiss['cool_hot']) ?>[<span style="color:blue;"><?php echo $cool_hot_score ?></span>]</td>
                        <td><?php echo implode(":", $tail4_3) ?>[<span style="color:blue;"><?php echo $tail4_3_score ?></span>]</td>
                        <td><?php echo $curmiss['miss_sum'] ?></td>
                        <td><?php echo $sum ?></td>
                        <td><?php echo $acnum ?></td>
                        <td><?php echo $repeat_num ?></td>
                        <td><?php echo $weisum ?></td>
                        <td><?php echo count($tailgroup) ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="clearfix"></div>
            <div class="center-block" id="chart1" style="height: 300px;"></div>
            <?php if(isset($itemid) && !empty($itemid)) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期数（<?php echo getSelItem($itemid) ?>）</th>
                        <?php foreach (itemsTable($itemid) as $thtext) { ?>
                        <th><?php echo $thtext ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($itemsData[$itemid] as $cp_dayid => $row) {
                    ?>
                    <tr class="default">
                        <td><?php echo $cp_dayid ?></td>
                        <?php 
                        foreach (itemsTable($itemid) as $item_txt) { 
                            if(isset($row[$item_txt])){ 
                        ?>
                            <td><?php echo $row[$item_txt] ?></td>
                        <?php }else{ ?>
                            <td></td>
                        <?php } ?>
                    <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>
<?php

$cp_dayids = array();
$scoreArr = array();
foreach ($score as $cp_dayid => $sv) {
    $cp_dayids[] = $cp_dayid % date("Y");
    $sum = array_sum($sv);
    if(in_array(0, $sv)){
        $scoreArr[] = 0;
    }else if($sum <= 9){
        $scoreArr[] = 1;
    }else{
        $scoreArr[] = 2;
    }
}

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
        var title = '奖号冷热走势';
        var xData = <?php echo json_encode($cp_dayids) ?>;

        var yData = <?php echo json_encode($scoreArr) ?>;
        var myChart = ec.init(document.getElementById('chart1'));
        
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
                max: 2,
                name: 'y',
                splitNumber: 2
               
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