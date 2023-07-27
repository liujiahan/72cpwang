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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>遗漏数字偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'location_cross'
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

            <!-- <h1 class="text-center">投资<?php echo $payget['pay_num'] ?>元，回报<?php echo $payget['get_num'] ?>元，回报率<?php echo round($payget['get_num'] / $payget['pay_num'], 4)*100 . '%'; ?></h1> -->
            <!-- <p class="lead">历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p> -->
            <!-- <blockquote>
                <p>总计值和平均值越小，则当期中奖号码中的数字越热门。你可以从图表中看到：极大或极小的总计值会迅速向相反方向回复。</p>
                <p>历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p>
            </blockquote>  -->

            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">选择期数</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="godayurl()">
                        <option value="">--请选择--</option>
                        <?php foreach (getDaySel() as $daynum => $daytxt) { ?>
                        <option value="<?php echo $daynum ?>" <?php echo isset($cp_dayid) && $cp_dayid==$daynum ? 'selected' : '' ?>>
                            <?php echo $daytxt ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">前N期统计</label>
                    <select class="form-control" name="before_days" id="before_days" onchange="godayurl()">
                        <option value="">--请选择--</option>
                        <?php foreach (array(5=>'前5期', 6=>'前6期', 7=>'前7期', 8=>'前8期', 9=>'前9期', 10=>'前10期') as $b_days => $seltext) { ?>
                        <option value="<?php echo $b_days ?>" <?php echo isset($before_days) && $before_days==$b_days ? 'selected' : '' ?>>
                            <?php echo $seltext ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            <!-- <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">大小四区间</a> -->
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <?php

                            $before_days = isset($before_days) && !empty($before_days) ? $before_days : 5;

                    ?>
                    <thead>
                        <tr>
                            <th width="20%">遗漏期数</th>
                            <th width="30%">前<?php echo $before_days ?>期该遗漏开出个数</th>
                            <th width="30%">该遗漏期数红球</th>
                            <th width="20%">下期出球</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $next_miss = array();
                            $tmp_miss = array();
                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $before_days";
                                $next = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid = $cp_dayid");
                                if(isset($next['id'])){
                                    $red_num = explode(",", $next['red_num']);
                                    $tmp_miss = getCurMiss($next['cp_dayid']);
                                    $tmp_miss = $tmp_miss['red_miss_arr'];
                                    foreach ($tmp_miss as $red => $miss) {
                                        if(in_array($red, $red_num) && $miss <= 5){
                                            if(!isset($next_miss[$miss])){
                                                $next_miss[$miss] = array();
                                            }
                                            $next_miss[$miss][] = $red;
                                        }
                                    }
                                }
                            }else{
                                $sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $before_days";
                            }

                            $dosql->Execute($sql);
                            $missnums = array(0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0);

                            $tmp_rednum = array();
                            while($row = $dosql->GetArray()){
                                $cp_dayid = $row['cp_dayid'];
                                $red_num = explode(',', $row['red_num']);
                                $currmiss = getCurMiss($cp_dayid);
                                $red_miss_arr = $currmiss['red_miss_arr'];
                                foreach ($red_miss_arr as $red => $miss) {
                                    if(in_array($red, $red_num) && $miss <= 5){
                                        $missnums[$miss]++;
                                    }
                                }
                                
                            }
                            if(empty($tmp_miss)){
                                $tmp_miss = redMissing();
                            }

                            asort($missnums);

                            $i = 0;
                        ?>
                        <?php foreach ($missnums as $miss_num => $miss_chu_ball) { ?>
                        <tr class="<?php echo $i % 2 == 0 ? 'active' : 'info'; ?>">
                            <?php
                                $cur_tmpmiss = array();
                                foreach ($tmp_miss as $red => $missv) {
                                    if($missv == $miss_num){
                                        $cur_tmpmiss[] = $red;
                                    }
                                }
                            ?>
                            <td><?php echo $miss_num ?></td>
                            <td><?php echo $miss_chu_ball . " | 遗漏{$miss_num}期热号：" . implode(".", $cur_tmpmiss).""; ?>
                            </td>
                            <td>
                                <?php 
                                    foreach ($cur_tmpmiss as $red) {
                                        echo '<a class="btn btn-primary" type="button" onclick="window.location.href=\'red_kchart.php?cur_red='.$red.'\';">'.$red.'</span></a>';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if(isset($next_miss[$miss_num])){
                                        foreach ($next_miss[$miss_num] as $red) {
                                            echo '<button class="btn btn-danger" type="button">'.$red.'</span></button>';
                                        }
                                    }
                                ?>
                            </td>
                        </tr>          
                        <?php $i++;} ?>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
