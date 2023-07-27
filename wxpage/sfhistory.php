<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球历史数据 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+"&year_num="+$("#year_num").val();
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
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid">
                <option value="">--请选择--</option>
                <?php 
                    $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid>=2017001 ORDER BY cp_dayid DESC");
                    $cp_day = array();
                    $max_dayid = 0;
                    while($row = $dosql->GetArray()){
                        $sel_cp_dayid = $row['cp_dayid'];
                        if($max_dayid == 0){
                            $max_dayid = $sel_cp_dayid;
                        }
                ?>
                <option value="<?php echo $sel_cp_dayid ?>" <?php echo isset($cp_dayid) && $sel_cp_dayid == $cp_dayid ? 'selected' : $max_dayid == $sel_cp_dayid ? 'selected' : ''; ?>><?php echo $sel_cp_dayid; ?></option>
                <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">选择年份来对比</label>
            <select class="form-control" name="year_num" id="year_num">
                <option value="">--请选择--</option>
                <?php 

                    $year_num = isset($year_num) ? $year_num : 1;
                    $years = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
                    foreach ($years as $key => $yearid) {
                ?>
                <option value="<?php echo $yearid ?>" <?php echo isset($year_num) && $year_num == $yearid ? 'selected' : ''; ?>><?php echo $yearid; ?>年</option>
                <?php } ?>
            </select>
          </div>
          <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form> 
        <!-- <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">加减乘除</a> -->
        
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">去年同期</th>
                        <th width="10%">开奖号码</th>
                        <th width="15%">近<?php echo $year_num ?>年同期红球出现个数</th>
                        <th width="27%">近<?php echo $year_num ?>年同期红球</th>
                        <th width="11%">出球个数</th>
                        <th width="27%">剩余的红球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $max_dayid = $dosql->GetOne("SELECT MAX(cp_dayid) as max FROM `#@__caipiao_history`");
                        // print_r($max_dayid);die;
                        $next_dayid = $max_dayid['max']+1;
                        $next_dayid = $next_dayid - 1000;

                        $allRed = array();
                        for ($i=1; $i < 34; $i++) { 
                            $i<10 && $i = '0' . $i;
                            $allRed[] = $i;
                        }

                        $historyRed = array();
                        $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$next_dayid'");
                        if(isset($row['id'])){
                            $cp_dayid = $row['cp_dayid'];
                            $red_num  = explode(',', $row['red_num']);

                            $cp_dayids = array();
                            for ($i=1; $i <= $year_num ; $i++) { 
                                $cp_dayids[] = $cp_dayid - $i * 1000;
                            }

                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid IN (".implode(',', $cp_dayids).")", "history");
                            $red_history_data = array();
                            while($row2 = $dosql->GetArray('history')){
                                $red_history_list = explode(',', $row2['red_num']);
                                foreach ($red_history_list as $tmp_red) {
                                    if(!isset($red_history_data[$tmp_red])){
                                        $red_history_data[$tmp_red] = 0;
                                    }
                                    $red_history_data[$tmp_red]++;
                                }
                            }
                            $red_history_data = array_keys($red_history_data);
                            sort($red_history_data);
                            $win_red_data     = array_intersect($red_num, $red_history_data);

                            $echart['win_red_num'][] = count($win_red_data);
                            
                            $cp_dayid_tmp = $cp_dayid - intval(date('Y').'000');
                            $cp_dayid_tmp < 10 && $cp_dayid_tmp = "00" . $cp_dayid_tmp;
                            $cp_dayid_tmp >= 10 && $cp_dayid_tmp < 100 && $cp_dayid_tmp = "0" . $cp_dayid_tmp;
                            
                            $echart['cp_dayid'][] = $cp_dayid_tmp;

                            if(!isset($historyRed[$cp_dayid])){
                                $historyRed[$cp_dayid] = array();
                                $historyRed[$cp_dayid]['cp_dayid'] = $row['cp_dayid'];
                                $historyRed[$cp_dayid]['opencode'] = $row['opencode'];
                                $historyRed[$cp_dayid]['red_num'] = explode(',', $row['red_num']);
                                $historyRed[$cp_dayid]['list'] = $red_history_data;
                            }
                        }

                        $historyRed = array_reverse($historyRed);
                    ?>

                    <?php foreach ($historyRed as $cp_dayid => $reddata) { ?>
                    <tr class="default">
                        <td><?php echo $reddata['cp_dayid'] ?></td>
                        <td><?php echo $reddata['opencode'] ?></td>
                        <td><?php echo count($reddata['list']) ?>个</td>
                        <td>
                        <?php
                            $win_num = 0;
                            foreach ($reddata['list'] as $tmp_red) {
                                if(in_array($tmp_red, $reddata['red_num'])){
                                    $win_num++;
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                                }
                            }
                        ?>
                        </td>
                        <td><?php echo $win_num ?></td>
                        <td>
                        <?php
                            foreach (array_diff($allRed, $reddata['list']) as $tmp_red) {
                                if(in_array($tmp_red, $reddata['red_num'])){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                                }
                            }
                        ?> 
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <div class="clearfix"></div>
            <div class="center-block" id="chart" style="height: 300px; width: 100%;"></div>


            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">期数</th>
                        <th width="10%">开奖号码</th>
                        <th width="15%">近<?php echo $year_num ?>年同期红球出现个数</th>
                        <th width="27%">近<?php echo $year_num ?>年同期红球</th>
                        <th width="11%">出球个数</th>
                        <th width="27%">剩余的红球</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        $sql = "SELECT * FROM `#@__caipiao_history` ";
                        
                        // if(!empty($cp_dayid)){
                        //     $sql .= " AND cp_dayid=$cp_dayid";
                        // }
                        $sql .= "  ORDER BY cp_dayid DESC";
                        if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                            $sql .= " LIMIT {$no_admin_limit}";
                        }

                        $dopage->GetPage($sql);

                        $allRed = array();
                        for ($i=1; $i < 34; $i++) { 
                            $i<10 && $i = '0' . $i;
                            $allRed[] = $i;
                        }

                        $historyRed = array();
                        $echart     = array();
                        $echart['cp_dayid']    = array();
                        $echart['win_red_num'] = array();
                        while($row = $dosql->GetArray()){
                            $cp_dayid = $row['cp_dayid'];
                            $red_num  = explode(',', $row['red_num']);

                            $cp_dayids = array();
                            for ($i=1; $i <= $year_num ; $i++) { 
                                $cp_dayids[] = $cp_dayid - $i * 1000;
                            }

                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid IN (".implode(',', $cp_dayids).")", "history");
                            $red_history_data = array();
                            while($row2 = $dosql->GetArray('history')){
                                $red_history_list = explode(',', $row2['red_num']);
                                foreach ($red_history_list as $tmp_red) {
                                    if(!isset($red_history_data[$tmp_red])){
                                        $red_history_data[$tmp_red] = 0;
                                    }
                                    $red_history_data[$tmp_red]++;
                                }
                            }
                            $red_history_data = array_keys($red_history_data);
                            sort($red_history_data);
                            $win_red_data     = array_intersect($red_num, $red_history_data);

                            $echart['win_red_num'][] = count($win_red_data);
                            
                            $cp_dayid_tmp = $cp_dayid - intval(date('Y').'000');
                            // $cp_dayid_tmp < 10 && $cp_dayid_tmp = "00" . $cp_dayid_tmp;
                            // $cp_dayid_tmp >= 10 && $cp_dayid_tmp < 100 && $cp_dayid_tmp = "0" . $cp_dayid_tmp;
                            
                            $echart['cp_dayid'][] = $cp_dayid_tmp;

                            if(!isset($historyRed[$cp_dayid])){
                                $historyRed[$cp_dayid] = array();
                                $historyRed[$cp_dayid]['cp_dayid'] = $row['cp_dayid'];
                                $historyRed[$cp_dayid]['opencode'] = $row['opencode'];
                                $historyRed[$cp_dayid]['red_num'] = explode(',', $row['red_num']);
                                $historyRed[$cp_dayid]['list'] = $red_history_data;
                            }
                        }
                        $historyRed = array_reverse($historyRed);
                    ?>

                    <?php foreach ($historyRed as $cp_dayid => $reddata) { ?>
                    <tr class="default">
                        <td><?php echo $reddata['cp_dayid'] ?></td>
                        <td><?php echo $reddata['opencode'] ?></td>
                        <td><?php echo count($reddata['list']) ?>个</td>
                        <td>
                        <?php
                            $win_num = 0;
                            foreach ($reddata['list'] as $tmp_red) {
                                if(in_array($tmp_red, $reddata['red_num'])){
                                    $win_num++;
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
                                }
                            }
                        ?>
                        </td>
                        <td><?php echo $win_num ?></td>
                        <td>
                        <?php
                            foreach (array_diff($allRed, $reddata['list']) as $tmp_red) {
                                if(in_array($tmp_red, $reddata['red_num'])){
                                    echo '<button class="btn btn-danger" type="button">'.$tmp_red . '</span>
                                    </button>';
                                }else{
                                    echo '<button class="btn btn-primary" type="button">'.$tmp_red . '</span></button>';
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
            var myChart = ec.init(document.getElementById('chart'));
            var option2 = {
              title: {
                  text: '红球同期近<?php echo $year_num ?>年，再次出球个数走势图',
                  left: 'center'
              },
              tooltip: {
                  trigger: 'item',
                  formatter: '{a} <br/>{b} : {c}'
              },
              legend: {
                  left: 'left',
                  data: ['历史同期出球个数走势图']
              },
              xAxis: {
                  type: 'category',
                  name: 'x',
                  splitLine: {show: false},
                  data: <?php echo json_encode($echart['cp_dayid']) ?>
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
                  min: 0,
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [
                  {
                      name: '历史同期出球个数走势图',
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
                      data: <?php echo json_encode($echart['win_red_num']) ?>
                  }
              ]
          };
            myChart.setOption(option2);
        }
    );
    </script>