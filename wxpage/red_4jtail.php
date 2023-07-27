<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
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
    <title>间隔4期红球尾数数据规律 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+'&num='+$("#num").val();
    }
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (getDaySel(30) as $t_cp_dayid => $cp_dayidtxt) { ?>
            <option value="<?php echo $t_cp_dayid ?>" <?php echo isset($cp_dayid) && $t_cp_dayid == $cp_dayid ? 'selected' : '' ?>><?php echo $cp_dayidtxt ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">前N期</label>
            <select class="form-control" name="num" id="num" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (array(8,10,12,16,20) as $numtmp) { ?>
            <option value="<?php echo $numtmp ?>" <?php echo isset($num) && $num == $numtmp ? 'selected' : '' ?>>前<?php echo $numtmp ?>期</option>
            <?php } ?>
            </select>
          </div>
        </form> 
        
        <div class="clearfix"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <?php 
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");
                    $maxid = $maxid['cp_dayid'];

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $num      = isset($num) && !empty($num) ? $num : 8;
                    $redTail  = redUpTailTrend($cp_dayid, $num);
                    $redTail2 = array_values($redTail);

                    $allBlue = array();
                    for ($i=0; $i < 10; $i++) { 
                        $allBlue[] = $i;
                    }

                    $curWinTail = array();
                    $curNoWinTail = array();
                    $preWinTail = array();
                    $curblue = '';
                    if(!empty($cp_dayid)){
                        $cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                        $curRed = explode(",", $cur['red_num']);
                        $curblue = $cur['blue_num'];
                        foreach ($curRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $curWinTail)){
                                $curWinTail[] = $tmptail;
                            }
                        }
                        sort($curWinTail);
                        $curNoWinTail = array_diff($allBlue, $curWinTail);

                        $pre = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
                        $pre = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'{$pre['cp_dayid']}' ORDER BY cp_dayid DESC");
                        $preRed = explode(",", $pre['red_num']);
                        foreach ($preRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $preWinTail)){
                                $preWinTail[] = $tmptail;
                            }
                        }
                        sort($preWinTail);
                    }
                ?>
                <thead>
                    <tr>
                        <th width="">继续下落</th>
                        <th width="">期数</th>
                        <th width="">红球尾</th>
                        <th width="">余尾</th>
                        <th width="">余尾下期命中</th>
                        <th width="">蓝号</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($redTail as $tmp_id => $row) {?>
                    <tr>
                        <td>
                        <?php 
                            $lefttail = array();
                            if(isset($redTail[$tmp_id-1])){
                                $lefttail = array_intersect($row['win'], $redTail[$tmp_id-1]['win']);
                            }
                            echo implode(' ', $lefttail);
                        ?>
                        </td>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td>
                        <?php 
                            // $nowin_righttail = isset($redTail[$tmp_id-1]['nowin']) ? array_intersect($row['win'], $redTail[$tmp_id-1]['nowin']) : array();
                            // echo implode(' ', $nowin_righttail);
                            echo implode(' ', $row['win']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            echo implode(' ', $row['nowin']);
                        ?>
                        </td>
                        <td>
                        <?php 
                            $righttail = array();
                            if(isset($redTail[$tmp_id-1])){
                                $righttail = array_intersect($row['win'], $redTail[$tmp_id-1]['nowin']);
                            }
                            echo implode(' ', $righttail);
                        ?>
                        </td>
                        <td><?php echo $row['blue_num'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>
                        <?php 
                            $end = end($redTail);
                            if($curWinTail){
                                $lefttail = array_intersect($curWinTail, $end['win']);
                                echo implode(' ', $lefttail);
                            }
                        ?>
                        </td>
                        <td><?php echo end($redTail2)['cp_dayid']+4 ?></td>
                        <td>
                        <?php 
                            if($curWinTail){
                                echo implode(' ', $curWinTail);
                            }
                        ?> 
                        </td>
                        <td>
                        <?php 
                            if($curNoWinTail){
                                echo implode(' ', $curNoWinTail);
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            if($curWinTail){
                                $righttail = array_intersect($curWinTail, $end['nowin']);
                                echo implode(' ', $righttail);
                            }
                        ?> 
                        </td>
                        <td><?php echo $curblue ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>