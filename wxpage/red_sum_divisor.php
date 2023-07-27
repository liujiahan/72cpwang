<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

$allRed = array();
for ($i=1; $i < 34; $i++) { 
    $i<10 && $i = '0' . $i;
    $allRed[] = $i;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>和值除数定胆法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val();
    }
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
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
                    action: 'red_sum_divisor'
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
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid">
                <option value="">--请选择--</option>
                <?php 
                    $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
                    $cp_day = array();
                    $max_dayid = 0;
                    while($row = $dosql->GetArray()){
                        $sel_cp_dayid = $row['cp_dayid'];
                        if($max_dayid == 0){
                            $max_dayid = $sel_cp_dayid;
                            $cp_dayid = $max_dayid;
                        }
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
                <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
              <?php } ?>
          </select>
          </div>
          <!-- <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a> -->
        </form> 
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">和值除数定胆</a>
        <?php } ?>
        
        <div class="clearfix"></div>
        <div class="center-block" id="chart1" style="height: 300px;"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <?php if(isset($cp_dayid) && !empty($cp_dayid)){ ?>
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="20%">猜中红球</th>
                        <th width="10%">猜中数占比</th>
                        <th width="25%">红球频率</th>
                        <th width="25%">33剩余红球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $next_cydayid = $cp_dayid + 1;
                        $next = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$next_cydayid'");

                        $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                        //开奖红球数组
                        $red_num = explode(',', $row['red_num']);
                        $opencode = $row['opencode'];
                        //红球之和
                        $red_sum = 0;
                        foreach ($red_num as $redv) {
                            $red_sum += $redv;
                        }
                        //加减乘除结果数组
                        $red_new_list = array();
                        $next_redlist = array();

                        foreach ($red_num as $redk => $redv) {
                            $red_new_list[$redk+1] = array();
                            $red_new_list[$redk+1]['red'] = $redv;
                            $red_new_list[$redk+1]['list'] = array();

                            $tmp_num = (intval(($red_sum - $redv) / $redv)) % 10;
                            $tmp_num > 0 && $tmp_num < 10 && $tmp_num = '0' . $tmp_num;
                            if($tmp_num == 0){
                                foreach (array(10, 20, 30) as $num) {
                                    array_push($red_new_list[$redk+1]['list'], $num);
                                    if(!isset($next_redlist[$num])){
                                        $next_redlist[$num] = 0;
                                    }
                                    $next_redlist[$num]++;
                                }
                            }else{
                                foreach (array($tmp_num, $tmp_num + 10, $tmp_num + 20, $tmp_num + 30) as $k => $num) {
                                    if($k == 3){
                                        if($num <= 33){
                                            array_push($red_new_list[$redk+1]['list'], $num);
                                            if(!isset($next_redlist[$num])){
                                                $next_redlist[$num] = 0;
                                            }
                                            $next_redlist[$num]++;
                                        }
                                    }else{
                                        array_push($red_new_list[$redk+1]['list'], $num);
                                        if(!isset($next_redlist[$num])){
                                            $next_redlist[$num] = 0;
                                        }
                                        $next_redlist[$num]++;
                                    }
                                }
                            }
                        }

                        $next_redlist = array_keys($next_redlist);
                        sort($next_redlist);
                        
                        //猜中的红球
                        $get_red_list = array();
                        if(isset($next['red_num'])){
                            $next_red_num = explode(',', $next['red_num']);
                            foreach ($next_redlist as $yc_red) {
                                if(in_array($yc_red, $next_red_num)){
                                    $get_red_list[] = $yc_red;
                                }
                            }
                        }
                        sort($get_red_list);
                        $get_red_pinlv = array();
                        foreach ($red_new_list as $k => $v) {
                            foreach ($v['list'] as $num) {
                                if(!isset($get_red_pinlv[$num])){
                                    $get_red_pinlv[$num] = 0;
                                }
                                $get_red_pinlv[$num]++;
                            }
                        }
                        $get_red_num = count($get_red_list);
                        // $get_red_list = implode(',', $get_red_list);
                        ksort($get_red_pinlv);

                    ?>
                    <tr class="info">
                        <td><?php echo $cp_dayid ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <!-- <td><?php echo implode(',', $next_redlist) ?></td> -->
                        <td>
                        <?php 
                            foreach ($get_red_list as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <td><?php echo $get_red_num . '/' . count($next_redlist) ?></td>
                        <td>
                        <?php 
                            foreach ($get_red_pinlv as $tmp_red => $tmp_red_pl) {
                                if(in_array($tmp_red, $get_red_list)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_red_pl.'</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_red_pl.'</span>
                                    </button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $diffRed = array_diff($allRed, array_keys($get_red_pinlv));
                            foreach ($diffRed as $tmp_red) {
                                if(isset($next_red_num) && in_array($tmp_red, $next_red_num)){
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
                </tbody>
            </table>
            <?php } ?>

            

            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="20%">猜中红球</th>
                        <th width="10%">猜中数占比</th>
                        <th width="25%">红球频率</th>
                        <th width="25%">33剩余红球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $limit_num = isset($limit_num) ? $limit_num : 30;
                        $sql = "SELECT * FROM `#@__caipiao_sfone` ORDER BY cp_dayid DESC LIMIT $limit_num";
                        $dosql->Execute($sql);
                        $i = 0;
                        while($row = $dosql->GetArray()){
                            $i++;
                            $next_cydayid = $row['cp_dayid']+1;
                            $next = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$next_cydayid'");
                            $next_red_num = isset($next['red_num']) ? explode(",", $next['red_num']) : array();
                    ?>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td>
                        <?php 
                            $get_red_list = explode(',', $row['get_red_list']);
                            foreach ($get_red_list as $tmp_red) {
                                echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</button>';
                            }
                        ?>
                        </td>
                        <?php 
                            $get_red_list = explode(',', $row['get_red_list']);
                            $get_red_pinlv = unserialize($row['get_red_pinlv']);
                        ?>
                        <td><?php echo $row['get_red_num'] . '/' . count($get_red_pinlv) ?></td>
                        <td>
                        <?php 
                            foreach ($get_red_pinlv as $tmp_red => $tmp_red_pl) {
                                $style = in_array($tmp_red, $get_red_list) ? 'danger' : 'primary';
                                echo '<button class="btn btn-'.$style.'" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_red_pl.'</span></button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $diffRed = array_diff($allRed, array_keys($get_red_pinlv));
                            foreach ($diffRed as $tmp_red) {
                                if(isset($next_red_num) && in_array($tmp_red, $next_red_num)){
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
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

<?php

$suanfa = array();
$suanfa['date']    = array();
$suanfa['win_num'] = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_sfone` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $suanfa['date'][] = $xdate;
    $suanfa['win_num'][] = $row['get_red_num'];
}

$suanfa['date']    = array_reverse($suanfa['date']);
$suanfa['win_num'] = array_reverse($suanfa['win_num']);

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
        var title = '和值除数定胆命中数走势图';
        var xData = <?php echo json_encode($suanfa['date']) ?>;

        var yData = <?php echo json_encode($suanfa['win_num']) ?>;
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
