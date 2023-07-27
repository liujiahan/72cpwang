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
    <title>红球频率分区选号法 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">

    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+"&suanfa_id="+$("#suanfa_id").val()+"&isdo="+$("#isdo").val();
    }
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
    }
    $(document).ready(function() {
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'red_pinlv_fenqu'},
                success: function(data){
                    window.location.reload();
                }
            })
        })
        $("#duiJiangBtn2").click(function(){
            $.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'pinlv_fenqu_count'},
                success: function(data){
                    // window.location.reload();
                    alert(data);
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
            <?php $monthNum = date('m') * 4 * 3; ?>
            <?php for($i=$monthNum; $i>=1; $i--){ ?>
            <?php 
              $sel_cp_dayid = date('Y');
              if($i >= 100){
                $sel_cp_dayid .= $i;
              }else if($i >= 10 && $i < 100){
                $sel_cp_dayid .= '0'.$i;
              }else{
                $sel_cp_dayid .= '00'.$i;
              }
            ?>
            <option value="<?php echo $sel_cp_dayid ?>" <?php echo isset($cp_dayid) && $sel_cp_dayid == $cp_dayid ? 'selected' : ''; ?>>
                <?php echo $sel_cp_dayid; ?>
            </option>
            <?php } ?>
        </select>
        <div class="form-group">
        <label for="exampleInputEmail2">选择期数</label>
        <select class="form-control" name="limit_num" id="limit_num" onchange="godayurl()">
            <option value="">--请选择--</option>
            <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
              <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
            <?php } ?>
        </select>
        </div>
        </div>
        <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form>        

        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">频率分区选号法</a>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="duiJiangBtn2" role="button">频率走势统计</a>
        <?php } ?>
        
        <div class="clearfix"></div>
        <div class="center-block" id="chart" style="height: 300px;"></div>
        <div class="bs-example" data-example-id="contextual-table">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">期数</th>
                    <th width="5%">开奖号</th>
                    <th width="7%">高频</th>
                    <th width="4%">高数</th>
                    <th width="7%">中频</th>
                    <th width="4%">中数</th>
                    <th width="7%">低频</th>
                    <th width="4%">低数</th>
                    <th width="20%">高频区</th>
                    <th width="20%">中频区</th>
                    <th width="20%">低频区</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                        $redBall = array();
                        $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 30");
                        $max_dayid = 0;
                        while ($row2 = $dosql->GetArray()) {
                            if($max_dayid == 0){
                                $max_dayid = $row2['cp_dayid'];
                            }
                            $red_num = explode(',', $row2['red_num']);
                            foreach ($red_num as $tmp_red) {
                                if(!isset($redBall[$tmp_red])){
                                    $redBall[$tmp_red] = 0;
                                }
                                $redBall[$tmp_red]++;
                            }
                        }
                        //如何区分高频 中频 低频
                        arsort($redBall);
                        $highpl = max($redBall);
                        $midpl = round($highpl / 2);
                        $lowpl = round($midpl / 2);

                        $ballpl = array('highpl'=>array(), 'midpl'=>array(), 'lowpl'=>array());
                        foreach ($redBall as $tmp_red => $pinlv) {
                            if($pinlv > $midpl && $pinlv <= $highpl){
                                $ballpl['highpl'][$tmp_red] = $pinlv;
                            }else if($pinlv > $lowpl && $pinlv <= $midpl){
                                $ballpl['midpl'][$tmp_red] = $pinlv;
                            }else if($pinlv <= $lowpl){
                                $ballpl['lowpl'][$tmp_red] = $pinlv;
                            }
                        }
                        ksort($ballpl['highpl']);
                        ksort($ballpl['midpl']);
                        ksort($ballpl['lowpl']);
                    ?>
                    <td><?php echo nextCpDayId($max_dayid) ?></td>
                    <td><?php //echo $row['opencode'] ?></td>
                    <td><?php //echo implode(',',$cur_ball['high']['red']); ?></td>
                    <td></td>
                    <td><?php //echo implode(',',$cur_ball['mid']['red']); ?></td>
                    <td></td>
                    <td><?php //echo implode(',',$cur_ball['low']['red']); ?></td>
                    <td></td>
                    <?php 
                        $highpl_ball = array_keys($ballpl['highpl']); 
                        $midpl_ball  = array_keys($ballpl['midpl']); 
                        $lowpl_ball  = array_keys($ballpl['lowpl']); 
                    ?>
                    <td>
                    <?php 
                        foreach ($highpl_ball as $tmp_red) {
                            echo '<button class="btn btn-info btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
                        }
                    ?>
                    </td>
                    <td>
                    <?php 
                        foreach ($midpl_ball as $tmp_red) {
                            echo '<button class="btn btn-primary btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
                        }
                    ?>
                    </td>
                    <td>
                    <?php 
                        foreach ($lowpl_ball as $tmp_red) {
                            echo '<button class="btn btn-danger btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
                        }
                    ?>
                    </td>
                </tr>
                <?php 
                    $sql = "SELECT * FROM `#@__caipiao_red_pinlv_fenqu` ";
                    if(!empty($cp_dayid)){
                        $sql .= " AND cp_dayid='$cp_dayid'";
                    }
                    if(isset($isdo)){
                        $sql .= " AND isdo='$isdo'";
                    }
                    $sql .= " ORDER BY cp_dayid DESC";

                    if( !isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey ){
                        $sql .= " LIMIT {$no_admin_limit}";
                    }
                    $dopage->GetPage($sql, 5);
                    $i = 0;
                    while($row = $dosql->GetArray()){
                        $i++;
                ?>
                <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                    
                    <td><?php echo $row['cp_dayid'] ?></td>
                    <td><?php echo $row['opencode'] ?></td>
                    <?php 
                        $cur_ball = unserialize($row['cur_ball']);
                    ?>
                    <td><?php echo implode(',',$cur_ball['high']['red']); ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" type="button"><?php echo $cur_ball['high']['num'] ?></button>
                    </td>
                    <td><?php echo implode(',',$cur_ball['mid']['red']); ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" type="button"><?php echo $cur_ball['mid']['num']; ?></button>
                    </td>
                    <td><?php echo implode(',',$cur_ball['low']['red']); ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" type="button"><?php echo $cur_ball['low']['num']; ?></button>
                    </td>
                    <?php 
                        $highpl_ball = unserialize($row['highpl_ball']); 
                        $highpl_ball = array_keys($highpl_ball); 
                        $midpl_ball  = unserialize($row['midpl_ball']); 
                        $midpl_ball  = array_keys($midpl_ball); 
                        $lowpl_ball  = unserialize($row['lowpl_ball']); 
                        $lowpl_ball  = array_keys($lowpl_ball); 
                    ?>
                    <td>
                    <?php 
                        foreach ($highpl_ball as $tmp_red) {
                            echo '<button class="btn btn-info btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
                        }
                    ?>
                    </td>
                    <td>
                    <?php 
                        foreach ($midpl_ball as $tmp_red) {
                            echo '<button class="btn btn-primary btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
                        }
                    ?>
                    </td>
                    <td>
                    <?php 
                        foreach ($lowpl_ball as $tmp_red) {
                            echo '<button class="btn btn-danger btn-sm" type="button">'.$tmp_red .'</span>
                            </button>';
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
<?php

$redPLFQ = array();
$redPLFQ['high'] = array();
$redPLFQ['mid']  = array();
$redPLFQ['low']  = array();

$limit_num = isset($limit_num) ? $limit_num : 30;
if( (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey) && $limit_num > $no_admin_limit ){
    $limit_num = $no_admin_limit;
}
$dosql->Execute("SELECT * FROM `#@__caipiao_red_pinlv_fenqu` ORDER BY cp_dayid DESC LIMIT $limit_num");
while($row = $dosql->GetArray()){
    $cur_ball = unserialize($row['cur_ball']);
    $xdate = $row['cp_dayid'] - intval(substr($row['cp_dayid'], 0, 4).'000');
    if($xdate == 1){
      $xdate = substr($row['cp_dayid'], 0, 4);
    } 
    $redPLFQ['date'][] = $xdate;
    $redPLFQ['high'][] = $cur_ball['high']['num'];
    $redPLFQ['mid'][]  = $cur_ball['mid']['num'];
    $redPLFQ['low'][]  = $cur_ball['low']['num'];
}

$redPLFQ['date'] = array_reverse($redPLFQ['date']);
$redPLFQ['high'] = array_reverse($redPLFQ['high']);
$redPLFQ['mid']  = array_reverse($redPLFQ['mid']);
$redPLFQ['low']  = array_reverse($redPLFQ['low']);

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
            var myChart  = ec.init(document.getElementById('chart'));
            var title    = '高频、中频、低频区命中走势图';

            var xData  = <?php echo json_encode($redPLFQ['date']) ?>;
            var y1Data = <?php echo json_encode($redPLFQ['high']) ?>;
            var y2Data = <?php echo json_encode($redPLFQ['mid']) ?>;
            var y3Data = <?php echo json_encode($redPLFQ['low']) ?>;

            var option = {
              title: {
                  text: title,
                  left: 'center'
              },
              legend: {
                  left: 'left',
                  data: ['高频区命中数', '中频区命中数', '低频区命中数']
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
                  type: 'log',
                  min: 0,
                  max: 6,
                  splitNumber:6,
                  name: 'y'
              },
              series: [{
                      name: '高频区命中数',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'red',
                          lineStyle:{
                              color:'red'  
                          } 
                        }
                      },
                      data: y1Data
                  },{
                      name: '中频区命中数',
                      type: 'line',
                      itemStyle : { 
                        normal: {
                          label : {show: true},
                          color:'purple',
                          lineStyle:{
                              color:'purple'  
                          } 
                        }
                      },
                      data: y2Data
                  },{
                      name: '低频区命中数',
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
                      data: y3Data
                  }]
            };
            myChart.setOption(option);
        }
    );
    </script>